<?php

namespace App\Services;

use App\Events\OrderPlaced;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderDownload;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\ShoppingCart;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\Payments\PaymentGatewayManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService
{
    public function __construct(
        protected CartService $cartService,
        protected OrderTotalsService $orderTotalsService,
        protected LoyaltyService $loyaltyService,
        protected PaymentGatewayManager $paymentGatewayManager,
    ) {}

    /**
     * @return array{success: bool, order?: Order, redirect_url?: string, message?: string}
     */
    public function placeOrder(array $data, ?User $user = null): array
    {
        $cart = $this->cartService->getCart();
        if ($cart->items->isEmpty()) {
            return ['success' => false, 'message' => 'السلة فارغة.'];
        }

        $cart->load('items.product.files', 'items.variant');

        $shippingAddress = [
            'country' => $data['shipping_country'] ?? config('store.default_country', 'SA'),
            'state' => $data['shipping_state'] ?? null,
            'city' => $data['shipping_city'] ?? null,
            'postal_code' => $data['shipping_postal_code'] ?? $data['zip_code'] ?? null,
        ];

        $totals = $this->orderTotalsService->calculate(
            $cart,
            $shippingAddress,
            isset($data['shipping_method_id']) ? (int) $data['shipping_method_id'] : null,
            $cart->coupon_code,
            $user,
            (int) ($data['points_to_redeem'] ?? 0),
        );

        $paymentMethod = PaymentMethod::active()->find($data['payment_method_id'] ?? null);
        if (! $paymentMethod) {
            return ['success' => false, 'message' => 'طريقة الدفع غير صالحة.'];
        }

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => $user?->id,
                'guest_email' => $user ? null : ($data['email'] ?? null),
                'order_status_id' => OrderStatus::where('slug', config('store.default_order_status', 'pending'))->value('id'),
                'shipping_method_id' => $totals->selectedShippingMethod['id'] ?? null,
                'subtotal' => $totals->subtotal,
                'shipping_amount' => $totals->shipping,
                'tax_amount' => $totals->tax,
                'discount_amount' => $totals->discount,
                'total' => $totals->total,
                'coupon_code' => $totals->couponCode,
                'currency' => config('store.default_currency', 'SAR'),
                'customer_note' => $data['notes'] ?? $data['customer_note'] ?? null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            foreach ($cart->items as $item) {
                $this->createOrderItem($order, $item);
            }

            $this->createAddresses($order, $data);

            if ($totals->couponCode && $totals->discount > 0) {
                $coupon = Coupon::where('code', $totals->couponCode)->first();
                if ($coupon) {
                    CouponUsage::create([
                        'user_id' => $user?->id,
                        'coupon_id' => $coupon->id,
                        'order_number' => $order->order_number,
                        'discount_amount' => $totals->discount,
                        'used_at' => now(),
                    ]);
                }
            }

            if ($user && ($data['points_to_redeem'] ?? 0) > 0) {
                $this->loyaltyService->applyRedemptionToOrder($order, $user, (int) $data['points_to_redeem']);
                $order->refresh();
            }

            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method_id' => $paymentMethod->id,
                'amount' => $order->total,
                'currency' => $order->currency,
                'status' => 'pending',
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return ['success' => false, 'message' => 'حدث خطأ أثناء معالجة الطلب. يرجى المحاولة مرة أخرى.'];
        }

        $order->refresh();

        try {
            OrderPlaced::dispatch($order->fresh(['items', 'status', 'addresses', 'user']));
            $gatewayResult = $this->paymentGatewayManager->initiate($paymentMethod, $order, $payment);
        } catch (\Throwable $e) {
            report($e);
            $gatewayResult = [];
        }

        $this->cartService->clear();

        return [
            'success' => true,
            'order' => $order,
            'redirect_url' => $gatewayResult['redirect_url'] ?? null,
        ];
    }

    protected function createOrderItem(Order $order, $item): void
    {
        $product = $item->product;
        $variant = $item->variant;
        $unitPrice = (float) $item->unit_price;

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
            'product_name' => $product->name,
            'variant_description' => $variant?->display_name,
            'sku' => $variant?->sku ?? $product->sku,
            'quantity' => $item->quantity,
            'unit_price' => $unitPrice,
            'total' => $unitPrice * $item->quantity,
        ]);

        if ($product->track_quantity) {
            if ($variant) {
                $variant->decrement('stock_quantity', $item->quantity);
            } else {
                $product->decrement('stock_quantity', $item->quantity);
            }

            StockMovement::create([
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
                'quantity_change' => -1 * $item->quantity,
                'reason' => 'order_create',
                'reference_type' => Order::class,
                'reference_id' => $order->id,
                'note' => 'خصم مخزون — الطلب #' . $order->order_number,
                'created_by' => Auth::id(),
            ]);
        }

        if ($product->is_digital) {
            foreach ($product->files->where('downloadable', true) as $file) {
                OrderDownload::create([
                    'order_id' => $order->id,
                    'order_item_id' => $orderItem->id,
                    'product_file_id' => $file->id,
                    'download_token' => Str::random(40),
                    'remaining_downloads' => $product->digital_download_limit,
                    'expires_at' => $product->digital_download_expiry_days
                        ? now()->addDays((int) $product->digital_download_expiry_days)
                        : null,
                ]);
            }
        }
    }

    protected function createAddresses(Order $order, array $data): void
    {
        $shipping = [
            'order_id' => $order->id,
            'type' => 'shipping',
            'first_name' => $data['first_name'] ?? $data['shipping_first_name'] ?? '',
            'last_name' => $data['last_name'] ?? $data['shipping_last_name'] ?? '',
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? $data['shipping_phone'] ?? '',
            'address_line_1' => $data['address'] ?? $data['shipping_address_line_1'] ?? '',
            'address_line_2' => $data['shipping_address_line_2'] ?? null,
            'city' => $data['city'] ?? $data['shipping_city'] ?? '',
            'state' => $data['shipping_state'] ?? null,
            'postal_code' => $data['zip_code'] ?? $data['shipping_postal_code'] ?? null,
            'country' => $data['shipping_country'] ?? config('store.default_country', 'SA'),
        ];

        OrderAddress::create($shipping);

        $billingSame = ($data['billing_same_as_shipping'] ?? true) !== false;
        if ($billingSame) {
            OrderAddress::create(array_merge($shipping, ['type' => 'billing']));
        } elseif (! empty($data['billing_first_name'])) {
            OrderAddress::create([
                'order_id' => $order->id,
                'type' => 'billing',
                'first_name' => $data['billing_first_name'],
                'last_name' => $data['billing_last_name'] ?? '',
                'email' => $data['billing_email'] ?? $data['email'] ?? null,
                'phone' => $data['billing_phone'] ?? '',
                'address_line_1' => $data['billing_address_line_1'] ?? '',
                'address_line_2' => $data['billing_address_line_2'] ?? null,
                'city' => $data['billing_city'] ?? '',
                'state' => $data['billing_state'] ?? null,
                'postal_code' => $data['billing_postal_code'] ?? null,
                'country' => $data['billing_country'] ?? config('store.default_country', 'SA'),
            ]);
        }
    }
}
