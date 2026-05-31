<?php

namespace App\Services\Payments;

use App\Models\PaymentMethod;
use App\Services\IntegrationSettingsService;

class StripeCredentialResolver
{
    public function publishableKey(?PaymentMethod $method = null): ?string
    {
        if ($method && empty($method->config['use_global_keys'])) {
            $key = $method->config['public_key'] ?? null;
            if ($key) {
                return $key;
            }
        }

        return config('services.stripe.key')
            ?: app(IntegrationSettingsService::class)->get(IntegrationSettingsService::KEY_STRIPE_KEY);
    }

    public function secretKey(?PaymentMethod $method = null): ?string
    {
        if ($method && empty($method->config['use_global_keys'])) {
            $key = $method->config['secret_key'] ?? null;
            if ($key) {
                return $key;
            }
        }

        return config('services.stripe.secret')
            ?: app(IntegrationSettingsService::class)->get(IntegrationSettingsService::KEY_STRIPE_SECRET);
    }

    public function webhookSecret(): ?string
    {
        return config('services.stripe.webhook_secret')
            ?: app(IntegrationSettingsService::class)->get(IntegrationSettingsService::KEY_STRIPE_WEBHOOK_SECRET);
    }

    public function isTestMode(?string $secretKey = null): bool
    {
        $secretKey ??= $this->secretKey();

        return str_starts_with((string) $secretKey, 'sk_test_');
    }

    public function dashboardPaymentUrl(?string $paymentIntentId, ?PaymentMethod $method = null): ?string
    {
        if (! $paymentIntentId) {
            return null;
        }

        $base = $this->isTestMode($this->secretKey($method))
            ? 'https://dashboard.stripe.com/test'
            : 'https://dashboard.stripe.com';

        return "{$base}/payments/{$paymentIntentId}";
    }

    public function dashboardCheckoutSessionUrl(?string $sessionId, ?PaymentMethod $method = null): ?string
    {
        if (! $sessionId) {
            return null;
        }

        $base = $this->isTestMode($this->secretKey($method))
            ? 'https://dashboard.stripe.com/test'
            : 'https://dashboard.stripe.com';

        return "{$base}/checkout/sessions/{$sessionId}";
    }
}
