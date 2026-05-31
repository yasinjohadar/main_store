<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class DynamicConfigLoader
{
    public function load(): void
    {
        if (! Schema::hasTable('system_settings')) {
            return;
        }

        try {
            $this->loadStoreSettings();
            $this->loadIntegrationSettings();
        } catch (\Throwable) {
            // DB unavailable during install/migrate
        }
    }

    protected function loadStoreSettings(): void
    {
        $settings = app(StoreSettingsService::class)->all();
        foreach ($settings as $key => $value) {
            Config::set('store.' . $key, $value);
        }
    }

    protected function loadIntegrationSettings(): void
    {
        $settings = app(IntegrationSettingsService::class)->all();
        $appUrl = config('app.url');

        Config::set('services.paypal.client_id', $settings[IntegrationSettingsService::KEY_PAYPAL_CLIENT_ID] ?: config('services.paypal.client_id'));
        Config::set('services.paypal.client_secret', $settings[IntegrationSettingsService::KEY_PAYPAL_CLIENT_SECRET] ?: config('services.paypal.client_secret'));
        Config::set('services.paypal.mode', $settings[IntegrationSettingsService::KEY_PAYPAL_MODE] ?: config('services.paypal.mode', 'sandbox'));

        Config::set('services.stripe.key', $settings[IntegrationSettingsService::KEY_STRIPE_KEY] ?: config('services.stripe.key'));
        Config::set('services.stripe.secret', $settings[IntegrationSettingsService::KEY_STRIPE_SECRET] ?: config('services.stripe.secret'));
        Config::set('services.stripe.webhook_secret', $settings[IntegrationSettingsService::KEY_STRIPE_WEBHOOK_SECRET] ?: config('services.stripe.webhook_secret'));

        Config::set('services.google.client_id', $settings[IntegrationSettingsService::KEY_GOOGLE_CLIENT_ID] ?: config('services.google.client_id'));
        Config::set('services.google.client_secret', $settings[IntegrationSettingsService::KEY_GOOGLE_CLIENT_SECRET] ?: config('services.google.client_secret'));
        Config::set('services.google.redirect', $appUrl . '/auth/google/callback');

        Config::set('services.facebook.client_id', $settings[IntegrationSettingsService::KEY_FACEBOOK_CLIENT_ID] ?: config('services.facebook.client_id'));
        Config::set('services.facebook.client_secret', $settings[IntegrationSettingsService::KEY_FACEBOOK_CLIENT_SECRET] ?: config('services.facebook.client_secret'));
        Config::set('services.facebook.redirect', $appUrl . '/auth/facebook/callback');

        Config::set('services.indexnow.key', $settings[IntegrationSettingsService::KEY_INDEXNOW_KEY] ?: config('services.indexnow.key'));
    }
}
