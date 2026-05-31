<?php

namespace App\Services;

use App\Events\OrderStatusChanged;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\StockMovement;
use App\Services\Payments\PaymentGatewayManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderRefundService
{
    public function __construct(
        protected PaymentGatewayManager $paymentGatewayManager,
    ) {}

    /**
     * @return array{success: bool, message?: string}
     */
    public function refund(Order $order, float $amount, bool $restock = false, ?string $reason = null): array
    {
        $refundable = $this->refundableAmount($order);
        if ($amount <= 0) {
            return ['success' => false, 'message' => 'مبلغ الاسترداد يجب أن يكون أكبر من صفر.'];
        }
        if ($amount > $refundable) {
            return ['success' => false, 'message' => 'المبلغ يتجاوز القابل للاسترداد (' . number_format($refundable, 2) . ').'];
        }

        $payment = $order->payments()->where('status', 'completed')->latest()->first();

        DB::beginTransaction();
        try {
            if ($payment) {
                $method = $payment->paymentMethod;
                if ($method) {
                    $gateway = $this->paymentGatewayManager->resolve($method);
                    if (method_exists($gateway, 'refund')) {
                        $result = $gateway->refund($payment, $amount);
                        if (($result['success'] ?? false) === false) {
                            throw new \RuntimeException($result['message'] ?? 'فشل الاسترداد من بوابة الدفع');
                        }
                    }
                }
            }

            Payment::create([
                'order_id' => $order->id,
                'payment_method_id' => $payment?->payment_method_id,
                'amount' => -abs($amount),
                'currency' => $order->currency,
                'status' => 'refunded',
                'gateway_response' => $reason,
                'paid_at' => now(),
            ]);

            if ($restock) {
                $this->restockOrderItems($order, $reason);
            }

            $remaining = $this->refundableAmount($order->fresh());
            if ($remaining <= 0.01) {
                $refundedStatus = OrderStatus::where('slug', 'refunded')->first();
                if ($refundedStatus) {
                    $oldSlug = $order->status?->slug;
                    $order->update(['order_status_id' => $refundedStatus->id]);
                    OrderStatusChanged::dispatch($order->fresh(['status', 'user']), $oldSlug);
                }
            }

            DB::commit();

            return ['success' => true];
        } catch (\Throwable $e) {
            DB::rollBack();

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function refundableAmount(Order $order): float
    {
        $completed = (float) $order->payments()->where('status', 'completed')->sum('amount');
        $base = $completed > 0 ? $completed : (float) $order->total;
        $refunded = abs((float) $order->payments()->where('status', 'refunded')->sum('amount'));

        return max(0, $base - $refunded);
    }

    protected function restockOrderItems(Order $order, ?string $reason): void
    {
        $order->loadMissing('items.product', 'items.variant');

        foreach ($order->items as $item) {
            $product = $item->product;
            if (! $product || ! $product->track_quantity) {
                continue;
            }

            $variant = $item->variant;
            $qty = $item->quantity;

            if ($variant) {
                $variant->increment('stock_quantity', $qty);
            } else {
                $product->increment('stock_quantity', $qty);
            }

            StockMovement::create([
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
                'quantity_change' => $qty,
                'reason' => 'order_refund',
                'reference_type' => Order::class,
                'reference_id' => $order->id,
                'note' => $reason ?? 'إرجاع مخزون بسبب استرداد مالي',
                'created_by' => Auth::id(),
            ]);
        }
    }
}
