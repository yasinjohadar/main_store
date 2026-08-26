<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderDownload;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\StockMovement;
use App\Models\Payment;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\ShippingService;
use App\Services\TaxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected CouponService $couponService,
        protected ShippingService $shippingService,
        protected TaxService $taxService
    ) {}

    public function index()
    {
        $cart = $this->cartService->getCart();
        if ($cart->items->isEmpty()) {
            return redirect()->route('store.cart.index')->with('error', 'السلة فارغة.');
        }
        return view('store.checkout.index', compact('cart'));
    }

    public function store(Request $request)
    {
        $cart = $this->cartService->getCart();
        if ($cart->items->isEmpty()) {
            return redirect()->route('store.cart.index')->with('error', 'السلة فارغة.');
        }
        $cart->load('items.product.files', 'items.variant');

        $request->validate([
            'shipping_first_name' => 'required|string|max:255',
            'shipping_last_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:50',
            'shipping_address_line_1' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'nullable|string|max:100',
            'shipping_postal_code' => 'nullable|string|max:20',
            'shipping_country' => 'required|string|size:2',
        ]);

        DB::beginTransaction();
        try {
            $subtotal = $cart->subtotal;

            $shippingAddressData = [
                'country' => $request->input('shipping_country'),
                'state' => $request->input('shipping_state'),
                'city' => $request->input('shipping_city'),
                'postal_code' => $request->input('shipping_postal_code'),
            ];

            $shippingResult = $this->shippingService->calculateForCart($cart, $shippingAddressData);
            $shipping = $shippingResult['shipping_amount'] ?? 0;

            $taxResult = $this->taxService->calculateForCart($cart, $shippingAddressData);
            $tax = $taxResult['tax_amount'] ?? 0;
            $discount = 0;
            $couponCode = null;
            if ($cart->coupon_code) {
                $result = $this->couponService->calculateDiscount($cart->coupon_code, $cart);
                if ($result['success']) {
                    $discount = $result['discount_amount'];
                    $couponCode = $result['coupon_code'];
                }
            }
            $total = $subtotal + $shipping + $tax - $discount;

            $order = Order::create([
                'user_id' => Auth::id(),
                'order_status_id' => OrderStatus::where('slug', 'pending')->value('id'),
                'shipping_method_id' => $shippingResult['selected_method']['id'] ?? null,
                'subtotal' => $subtotal,
                'shipping_amount' => $shipping,
                'tax_amount' => $tax,
                'discount_amount' => $discount,
                'total' => $total,
                'coupon_code' => $couponCode,
                'customer_note' => $request->input('customer_note'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            foreach ($cart->items as $item) {
                $product = $item->product;
                $variant = $item->variant;
                $unitPrice = $variant ? $variant->effective_price : $product->effective_price;
                $name = $product->name;
                $sku = $variant ? $variant->sku : $product->sku;
                $variantDesc = $variant ? $variant->display_name : null;

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'product_name' => $name,
                    'variant_description' => $variantDesc,
                    'sku' => $sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $unitPrice,
                    'total' => $unitPrice * $item->quantity,
                ]);

                // تحديث المخزون إذا كان المنتج يتتبع الكمية
                if ($product->track_quantity) {
                    $quantityChange = -1 * $item->quantity;

                    if ($variant) {
                        $variant->decrement('stock_quantity', $item->quantity);
                    } else {
                        $product->decrement('stock_quantity', $item->quantity);
                    }

                    StockMovement::create([
                        'product_id' => $product->id,
                        'product_variant_id' => $variant?->id,
                        'quantity_change' => $quantityChange,
                        'reason' => 'order_create',
                        'reference_type' => Order::class,
                        'reference_id' => $order->id,
                        'note' => 'خصم مخزون بسبب إنشاء الطلب #' . $order->order_number,
                        'created_by' => Auth::id(),
                    ]);
                }

                if ($product->is_digital) {
                    $files = $product->files->where('downloadable', true);
                    foreach ($files as $file) {
                        $expiresAt = $product->digital_download_expiry_days
                            ? now()->addDays((int) $product->digital_download_expiry_days)
                            : null;
                        OrderDownload::create([
                            'order_id' => $order->id,
                            'order_item_id' => $orderItem->id,
                            'product_file_id' => $file->id,
                            'download_token' => Str::random(40),
                            'remaining_downloads' => $product->digital_download_limit,
                            'expires_at' => $expiresAt,
                        ]);
                    }
                }
            }

            OrderAddress::create([
                'order_id' => $order->id,
                'type' => 'shipping',
                'first_name' => $request->input('shipping_first_name'),
                'last_name' => $request->input('shipping_last_name'),
                'phone' => $request->input('shipping_phone'),
                'address_line_1' => $request->input('shipping_address_line_1'),
                'address_line_2' => $request->input('shipping_address_line_2'),
                'city' => $request->input('shipping_city'),
                'state' => $request->input('shipping_state'),
                'postal_code' => $request->input('shipping_postal_code'),
                'country' => $request->input('shipping_country'),
            ]);

            Payment::create([
                'order_id' => $order->id,
                'amount' => $total,
                'status' => 'pending',
            ]);

            $this->cartService->clear();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ. يرجى المحاولة مرة أخرى.');
        }

        return redirect()->route('store.checkout.success', $order)->with('success', 'تم إنشاء الطلب بنجاح.');
    }

    public function success(Order $order)
    {
        $order->load('items.product', 'items.downloads.file', 'status');
        return view('store.checkout.success', compact('order'));
    }
}
