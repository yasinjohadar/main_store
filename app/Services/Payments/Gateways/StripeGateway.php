<?php

namespace App\Services\Payments\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\Payments\StripeCredentialResolver;
use App\Services\Payments\StripePaymentSyncService;
use App\Services\Payments\StripeWebhookService;
use Illuminate\Support\Facades\Log;

class StripeGateway implements PaymentGatewayInterface
{
    public function __construct(
        protected StripeCredentialResolver $credentials,
        protected StripePaymentSyncService $sync,
        protected StripeWebhookService $webhooks,
    ) {}

    public function driver(): string
    {
        return 'stripe';
    }

    public function initiate(PaymentMethod $method, Order $order, Payment $payment): array
    {
        $secret = $this->credentials->secretKey($method);
        if (! $secret || ! class_exists(\Stripe\Stripe::class)) {
            $payment->update(['status' => 'failed', 'gateway_response' => 'Stripe not configured']);

            return ['status' => 'failed'];
        }

        try {
            \Stripe\Stripe::setApiKey($secret);

            $order->loadMissing('items');

            $lineItems = $this->buildLineItems($order);
            $currency = strtolower($order->currency ?? 'sar');

            $session = \Stripe\Checkout\Session::create([
                'mode' => 'payment',
                'success_url' => route('frontend.checkout.success', $order) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('frontend.checkout.index', ['cancelled' => 1]),
                'client_reference_id' => $order->order_number,
                'customer_email' => $this->customerEmail($order),
                'line_items' => $lineItems,
                'metadata' => [
                    'order_id' => (string) $order->id,
                    'payment_id' => (string) $payment->id,
                    'order_number' => $order->order_number,
                ],
            ]);

            $payment->update([
                'transaction_id' => $session->id,
                'status' => 'pending',
                'metadata' => [
                    'stripe_session_id' => $session->id,
                ],
            ]);

            return ['redirect_url' => $session->url, 'status' => 'pending'];
        } catch (\Throwable $e) {
            Log::error('Stripe initiate failed', ['error' => $e->getMessage(), 'order_id' => $order->id]);
            $payment->update(['status' => 'failed', 'gateway_response' => $e->getMessage()]);

            return ['status' => 'failed'];
        }
    }

    public function handleWebhook(array $payload): array
    {
        return ['success' => false, 'message' => 'Use StripeWebhookService with signed payload'];
    }

    /**
     * @return array{success: bool, message?: string}
     */
    public function handleSignedWebhook(string $rawPayload, ?string $signature): array
    {
        $constructed = $this->webhooks->constructEvent($rawPayload, $signature);
        if (! ($constructed['success'] ?? false)) {
            return $constructed;
        }

        return $this->webhooks->processEvent($constructed['event']);
    }

    public function refund(Payment $payment, float $amount): array
    {
        $method = $payment->paymentMethod;
        $secret = $this->credentials->secretKey($method);
        $paymentIntentId = $this->sync->paymentIntentId($payment);

        if (! $secret || ! class_exists(\Stripe\Stripe::class) || ! $paymentIntentId) {
            return ['success' => false, 'message' => 'Stripe refund unavailable'];
        }

        try {
            \Stripe\Stripe::setApiKey($secret);
            $refund = \Stripe\Refund::create([
                'payment_intent' => $paymentIntentId,
                'amount' => (int) round($amount * 100),
            ]);

            return ['success' => true, 'refund_id' => $refund->id];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    protected function buildLineItems(Order $order): array
    {
        $currency = strtolower($order->currency ?? 'sar');
        $items = [];

        foreach ($order->items as $item) {
            $items[] = [
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => $item->product_name,
                        'description' => $item->variant_description,
                    ],
                    'unit_amount' => (int) round((float) $item->unit_price * 100),
                ],
                'quantity' => max(1, (int) $item->quantity),
            ];
        }

        $itemsTotal = $order->items->sum(fn ($item) => (float) $item->total);
        $orderTotal = (float) $order->total;
        $shipping = max(0, round((float) $order->shipping_amount, 2));
        $discount = max(0, round((float) $order->discount_amount + (float) ($order->points_discount_amount ?? 0), 2));
        $tax = max(0, round((float) $order->tax_amount, 2));

        $adjustment = round($orderTotal - $itemsTotal - $shipping - $tax + $discount, 2);

        if ($shipping > 0) {
            $items[] = [
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => ['name' => 'الشحن'],
                    'unit_amount' => (int) round($shipping * 100),
                ],
                'quantity' => 1,
            ];
        }

        if ($tax > 0) {
            $items[] = [
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => ['name' => 'الضريبة'],
                    'unit_amount' => (int) round($tax * 100),
                ],
                'quantity' => 1,
            ];
        }

        if ($adjustment > 0.01) {
            $items[] = [
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => ['name' => 'رسوم إضافية'],
                    'unit_amount' => (int) round($adjustment * 100),
                ],
                'quantity' => 1,
            ];
        } elseif ($adjustment < -0.01 && empty($items)) {
            $items[] = [
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => ['name' => 'طلب ' . $order->order_number],
                    'unit_amount' => (int) round($orderTotal * 100),
                ],
                'quantity' => 1,
            ];
        }

        if (empty($items)) {
            $items[] = [
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => ['name' => 'طلب ' . $order->order_number],
                    'unit_amount' => (int) round(max(0, $orderTotal) * 100),
                ],
                'quantity' => 1,
            ];
        }

        return $items;
    }

    protected function customerEmail(Order $order): ?string
    {
        $order->loadMissing('addresses', 'user');

        return $order->shipping_address?->email
            ?? $order->guest_email
            ?? $order->user?->email;
    }
}
