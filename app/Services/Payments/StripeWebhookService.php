<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Models\PaymentEvent;
use Illuminate\Support\Facades\Log;

class StripeWebhookService
{
    public function __construct(
        protected StripeCredentialResolver $credentials,
        protected StripePaymentSyncService $sync,
    ) {}

    /**
     * @return array{success: bool, message?: string}
     */
    public function constructEvent(string $payload, ?string $signature): array
    {
        $secret = $this->credentials->webhookSecret();

        if (! $secret) {
            return ['success' => false, 'message' => 'Webhook secret not configured'];
        }

        if (! $signature) {
            return ['success' => false, 'message' => 'Missing Stripe-Signature header'];
        }

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $signature, $secret);

            return ['success' => true, 'event' => $event];
        } catch (\UnexpectedValueException $e) {
            return ['success' => false, 'message' => 'Invalid payload'];
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return ['success' => false, 'message' => 'Invalid signature'];
        }
    }

    public function processEvent(\Stripe\Event $event): array
    {
        $existing = PaymentEvent::where('event_id', $event->id)->first();
        if ($existing) {
            return ['success' => true, 'duplicate' => true];
        }

        $paymentEvent = PaymentEvent::create([
            'provider' => 'stripe',
            'event_type' => $event->type,
            'event_id' => $event->id,
            'payload' => $event->toArray(),
            'status' => 'pending',
        ]);

        try {
            $result = $this->dispatchEvent($event, $paymentEvent);
            $paymentEvent->update([
                'status' => ($result['success'] ?? false) ? 'processed' : 'failed',
                'payment_id' => $result['payment_id'] ?? null,
                'order_id' => $result['order_id'] ?? null,
                'error_message' => $result['message'] ?? null,
                'processed_at' => now(),
            ]);

            return $result;
        } catch (\Throwable $e) {
            Log::error('Stripe webhook processing failed', [
                'event_id' => $event->id,
                'type' => $event->type,
                'error' => $e->getMessage(),
            ]);

            $paymentEvent->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'processed_at' => now(),
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    protected function dispatchEvent(\Stripe\Event $event, PaymentEvent $paymentEvent): array
    {
        return match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutSessionCompleted($event),
            'checkout.session.expired' => $this->handleCheckoutSessionExpired($event),
            'payment_intent.payment_failed' => $this->handlePaymentIntentFailed($event),
            'charge.refunded' => $this->handleChargeRefunded($event),
            default => ['success' => true, 'ignored' => true],
        };
    }

    protected function handleCheckoutSessionCompleted(\Stripe\Event $event): array
    {
        /** @var \Stripe\Checkout\Session $session */
        $session = $event->data->object;
        $payment = $this->findPaymentForSession($session);

        if (! $payment) {
            return ['success' => false, 'message' => 'Payment not found for session'];
        }

        $paymentIntentId = is_string($session->payment_intent)
            ? $session->payment_intent
            : ($session->payment_intent->id ?? null);

        $this->sync->markPaymentCompleted($payment, array_filter([
            'stripe_session_id' => $session->id,
            'stripe_payment_intent_id' => $paymentIntentId,
        ]));

        return [
            'success' => true,
            'payment_id' => $payment->id,
            'order_id' => $payment->order_id,
        ];
    }

    protected function handleCheckoutSessionExpired(\Stripe\Event $event): array
    {
        /** @var \Stripe\Checkout\Session $session */
        $session = $event->data->object;
        $payment = $this->findPaymentForSession($session);

        if (! $payment) {
            return ['success' => true, 'ignored' => true];
        }

        $this->sync->markPaymentCancelled($payment, 'انتهت جلسة Stripe Checkout');

        return [
            'success' => true,
            'payment_id' => $payment->id,
            'order_id' => $payment->order_id,
        ];
    }

    protected function handlePaymentIntentFailed(\Stripe\Event $event): array
    {
        /** @var \Stripe\PaymentIntent $intent */
        $intent = $event->data->object;
        $payment = $this->findPaymentForIntent($intent);

        if (! $payment) {
            return ['success' => true, 'ignored' => true];
        }

        $this->sync->markPaymentFailed($payment, $intent->last_payment_error->message ?? 'فشل الدفع');

        return [
            'success' => true,
            'payment_id' => $payment->id,
            'order_id' => $payment->order_id,
        ];
    }

    protected function handleChargeRefunded(\Stripe\Event $event): array
    {
        /** @var \Stripe\Charge $charge */
        $charge = $event->data->object;
        $paymentIntentId = $charge->payment_intent ?? null;

        if (! $paymentIntentId) {
            return ['success' => true, 'ignored' => true];
        }

        $payment = Payment::query()
            ->where('metadata->stripe_payment_intent_id', $paymentIntentId)
            ->latest()
            ->first();

        if (! $payment) {
            return ['success' => true, 'ignored' => true];
        }

        if ($payment->status !== 'refunded') {
            $payment->update(['status' => 'refunded']);
        }

        return [
            'success' => true,
            'payment_id' => $payment->id,
            'order_id' => $payment->order_id,
        ];
    }

    protected function findPaymentForSession(\Stripe\Checkout\Session $session): ?Payment
    {
        $paymentId = $session->metadata['payment_id'] ?? null;
        if ($paymentId) {
            $payment = Payment::find($paymentId);
            if ($payment) {
                return $payment;
            }
        }

        return Payment::query()
            ->where('transaction_id', $session->id)
            ->orWhere('metadata->stripe_session_id', $session->id)
            ->latest()
            ->first();
    }

    protected function findPaymentForIntent(\Stripe\PaymentIntent $intent): ?Payment
    {
        $paymentId = $intent->metadata['payment_id'] ?? null;
        if ($paymentId) {
            $payment = Payment::find($paymentId);
            if ($payment) {
                return $payment;
            }
        }

        return Payment::query()
            ->where('metadata->stripe_payment_intent_id', $intent->id)
            ->latest()
            ->first();
    }
}
