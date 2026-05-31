<?php

namespace App\Services\Payments\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\Events\PaymentCompleted;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalGateway implements PaymentGatewayInterface
{
    public function driver(): string
    {
        return 'paypal';
    }

    public function initiate(PaymentMethod $method, Order $order, Payment $payment): array
    {
        $config = $method->config ?? [];
        $clientId = $config['client_id'] ?? config('services.paypal.client_id');
        $secret = $config['client_secret'] ?? config('services.paypal.client_secret');
        $mode = $config['mode'] ?? config('services.paypal.mode', 'sandbox');

        if (! $clientId || ! $secret) {
            $payment->update(['status' => 'failed', 'gateway_response' => 'PayPal not configured']);

            return ['status' => 'failed'];
        }

        $baseUrl = $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        try {
            $token = $this->accessToken($baseUrl, $clientId, $secret);
            $response = Http::withToken($token)->post("{$baseUrl}/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => $order->order_number,
                    'amount' => [
                        'currency_code' => $order->currency ?? 'SAR',
                        'value' => number_format((float) $order->total, 2, '.', ''),
                    ],
                ]],
                'application_context' => [
                    'return_url' => route('frontend.checkout.paypal.return', $order),
                    'cancel_url' => route('frontend.checkout.index'),
                ],
            ]);

            if (! $response->successful()) {
                throw new \RuntimeException($response->body());
            }

            $paypalOrderId = $response->json('id');
            $approveUrl = collect($response->json('links', []))->firstWhere('rel', 'approve')['href'] ?? null;

            $payment->update([
                'transaction_id' => $paypalOrderId,
                'status' => 'pending',
                'metadata' => ['paypal_order_id' => $paypalOrderId],
            ]);

            return ['redirect_url' => $approveUrl, 'status' => 'pending'];
        } catch (\Throwable $e) {
            Log::error('PayPal initiate failed', ['error' => $e->getMessage()]);
            $payment->update(['status' => 'failed']);

            return ['status' => 'failed'];
        }
    }

    public function handleWebhook(array $payload): array
    {
        return ['success' => false];
    }

    public function refund(Payment $payment, float $amount): array
    {
        return ['success' => false, 'message' => 'PayPal refund not implemented'];
    }

    protected function accessToken(string $baseUrl, string $clientId, string $secret): string
    {
        $response = Http::asForm()
            ->withBasicAuth($clientId, $secret)
            ->post("{$baseUrl}/v1/oauth2/token", ['grant_type' => 'client_credentials']);

        return $response->json('access_token');
    }

    public function captureOrder(Payment $payment, string $paypalOrderId): bool
    {
        $method = $payment->paymentMethod;
        $config = $method?->config ?? [];
        $clientId = $config['client_id'] ?? config('services.paypal.client_id');
        $secret = $config['client_secret'] ?? config('services.paypal.client_secret');
        $mode = $config['mode'] ?? config('services.paypal.mode', 'sandbox');
        $baseUrl = $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';

        $token = $this->accessToken($baseUrl, $clientId, $secret);
        $response = Http::withToken($token)->post("{$baseUrl}/v2/checkout/orders/{$paypalOrderId}/capture");

        if (! $response->successful()) {
            return false;
        }

        $payment->update(['status' => 'completed', 'paid_at' => now()]);
        $order = $payment->order;
        $completedStatus = OrderStatus::where('slug', 'processing')->value('id');
        if ($completedStatus) {
            $order->update(['order_status_id' => $completedStatus]);
        }

        PaymentCompleted::dispatch($payment->fresh());

        return true;
    }
}
