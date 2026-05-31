<?php

namespace App\Services\Payments;

use App\Events\PaymentCompleted;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class StripePaymentSyncService
{
    public function __construct(
        protected StripeCredentialResolver $credentials,
    ) {}

    public function markPaymentCompleted(Payment $payment, array $extraMetadata = []): void
    {
        if ($payment->status === 'completed') {
            return;
        }

        $metadata = array_merge($payment->metadata ?? [], $extraMetadata);

        $payment->update([
            'status' => 'completed',
            'paid_at' => now(),
            'metadata' => $metadata,
        ]);

        $order = $payment->order;
        if ($order) {
            $statusId = OrderStatus::where('slug', 'processing')->value('id');
            if ($statusId && $order->order_status_id !== $statusId) {
                $order->update(['order_status_id' => $statusId]);
            }
        }

        PaymentCompleted::dispatch($payment->fresh(['order', 'paymentMethod']));
    }

    public function markPaymentFailed(Payment $payment, ?string $reason = null): void
    {
        if (in_array($payment->status, ['completed', 'refunded'], true)) {
            return;
        }

        $payment->update([
            'status' => 'failed',
            'gateway_response' => $reason,
        ]);
    }

    public function markPaymentCancelled(Payment $payment, ?string $reason = null): void
    {
        if (in_array($payment->status, ['completed', 'refunded'], true)) {
            return;
        }

        $payment->update([
            'status' => 'cancelled',
            'gateway_response' => $reason,
        ]);
    }

    /**
     * @return array{success: bool, payment?: Payment, message?: string}
     */
    public function syncFromCheckoutSession(string $sessionId, ?Order $order = null): array
    {
        if (! class_exists(\Stripe\Stripe::class)) {
            return ['success' => false, 'message' => 'Stripe SDK غير مثبت'];
        }

        $payment = Payment::query()
            ->where('transaction_id', $sessionId)
            ->orWhere('metadata->stripe_session_id', $sessionId)
            ->when($order, fn ($q) => $q->where('order_id', $order->id))
            ->latest()
            ->first();

        $method = $payment?->paymentMethod;
        $secret = $this->credentials->secretKey($method);

        if (! $secret) {
            return ['success' => false, 'message' => 'Stripe غير مُعد'];
        }

        try {
            \Stripe\Stripe::setApiKey($secret);
            $session = \Stripe\Checkout\Session::retrieve($sessionId, [
                'expand' => ['payment_intent'],
            ]);

            if (! $payment) {
                $paymentId = $session->metadata['payment_id'] ?? null;
                $payment = $paymentId ? Payment::find($paymentId) : null;
            }

            if (! $payment) {
                return ['success' => false, 'message' => 'الدفع غير موجود'];
            }

            $paymentIntentId = is_string($session->payment_intent)
                ? $session->payment_intent
                : ($session->payment_intent->id ?? null);

            $metadata = array_merge($payment->metadata ?? [], array_filter([
                'stripe_session_id' => $session->id,
                'stripe_payment_intent_id' => $paymentIntentId,
            ]));

            if ($session->payment_status === 'paid') {
                $this->markPaymentCompleted($payment, $metadata);

                return ['success' => true, 'payment' => $payment->fresh()];
            }

            if ($session->status === 'expired') {
                $this->markPaymentCancelled($payment, 'انتهت جلسة الدفع');

                return ['success' => false, 'message' => 'انتهت جلسة الدفع', 'payment' => $payment->fresh()];
            }

            $payment->update(['metadata' => $metadata]);

            return ['success' => true, 'payment' => $payment->fresh()];
        } catch (\Throwable $e) {
            Log::error('Stripe session sync failed', ['session_id' => $sessionId, 'error' => $e->getMessage()]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function paymentIntentId(Payment $payment): ?string
    {
        return $payment->metadata['stripe_payment_intent_id'] ?? null;
    }
}
