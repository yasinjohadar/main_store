<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

class IntegrationSettingsService
{
    public const GROUP = 'integrations';

    public const KEY_PAYPAL_CLIENT_ID = 'paypal_client_id';
    public const KEY_PAYPAL_CLIENT_SECRET = 'paypal_client_secret';
    public const KEY_PAYPAL_MODE = 'paypal_mode';
    public const KEY_STRIPE_KEY = 'stripe_key';
    public const KEY_STRIPE_SECRET = 'stripe_secret';
    public const KEY_STRIPE_WEBHOOK_SECRET = 'stripe_webhook_secret';
    public const KEY_GOOGLE_CLIENT_ID = 'google_client_id';
    public const KEY_GOOGLE_CLIENT_SECRET = 'google_client_secret';
    public const KEY_FACEBOOK_CLIENT_ID = 'facebook_client_id';
    public const KEY_FACEBOOK_CLIENT_SECRET = 'facebook_client_secret';
    public const KEY_INDEXNOW_KEY = 'indexnow_key';

    /** @var list<string> */
    public const SECRET_KEYS = [
        self::KEY_PAYPAL_CLIENT_SECRET,
        self::KEY_STRIPE_SECRET,
        self::KEY_STRIPE_WEBHOOK_SECRET,
        self::KEY_GOOGLE_CLIENT_SECRET,
        self::KEY_FACEBOOK_CLIENT_SECRET,
    ];

    private const CACHE_KEY = 'integration_settings';
    private const CACHE_TTL = 3600;

    public static function schema(): array
    {
        return [
            self::KEY_PAYPAL_CLIENT_ID => [
                'type' => 'string',
                'default' => '',
                'label' => 'PayPal Client ID',
                'section' => 'paypal',
                'hint' => 'من developer.paypal.com',
                'secret' => false,
            ],
            self::KEY_PAYPAL_CLIENT_SECRET => [
                'type' => 'string',
                'default' => '',
                'label' => 'PayPal Client Secret',
                'section' => 'paypal',
                'hint' => 'اتركه فارغاً للإبقاء على القيمة الحالية.',
                'secret' => true,
            ],
            self::KEY_PAYPAL_MODE => [
                'type' => 'string',
                'default' => 'sandbox',
                'label' => 'PayPal Mode',
                'section' => 'paypal',
                'hint' => 'sandbox أو live',
                'secret' => false,
            ],
            self::KEY_STRIPE_KEY => [
                'type' => 'string',
                'default' => '',
                'label' => 'Stripe Publishable Key',
                'section' => 'stripe',
                'hint' => 'pk_live_... أو pk_test_...',
                'secret' => false,
            ],
            self::KEY_STRIPE_SECRET => [
                'type' => 'string',
                'default' => '',
                'label' => 'Stripe Secret Key',
                'section' => 'stripe',
                'hint' => 'اتركه فارغاً للإبقاء على القيمة الحالية.',
                'secret' => true,
            ],
            self::KEY_STRIPE_WEBHOOK_SECRET => [
                'type' => 'string',
                'default' => '',
                'label' => 'Stripe Webhook Secret',
                'section' => 'stripe',
                'hint' => 'whsec_... — اتركه فارغاً للإبقاء على القيمة الحالية.',
                'secret' => true,
            ],
            self::KEY_GOOGLE_CLIENT_ID => [
                'type' => 'string',
                'default' => '',
                'label' => 'Google OAuth Client ID',
                'section' => 'oauth',
                'hint' => 'تسجيل الدخول عبر Google',
                'secret' => false,
            ],
            self::KEY_GOOGLE_CLIENT_SECRET => [
                'type' => 'string',
                'default' => '',
                'label' => 'Google OAuth Client Secret',
                'section' => 'oauth',
                'hint' => 'اتركه فارغاً للإبقاء على القيمة الحالية.',
                'secret' => true,
            ],
            self::KEY_FACEBOOK_CLIENT_ID => [
                'type' => 'string',
                'default' => '',
                'label' => 'Facebook App ID',
                'section' => 'oauth',
                'hint' => 'تسجيل الدخول عبر Facebook',
                'secret' => false,
            ],
            self::KEY_FACEBOOK_CLIENT_SECRET => [
                'type' => 'string',
                'default' => '',
                'label' => 'Facebook App Secret',
                'section' => 'oauth',
                'hint' => 'اتركه فارغاً للإبقاء على القيمة الحالية.',
                'secret' => true,
            ],
            self::KEY_INDEXNOW_KEY => [
                'type' => 'string',
                'default' => '',
                'label' => 'IndexNow Key',
                'section' => 'seo',
                'hint' => 'مفتاح فهرسة Bing/Yandex',
                'secret' => false,
            ],
        ];
    }

    public static function sectionLabels(): array
    {
        return [
            'paypal' => 'PayPal',
            'stripe' => 'Stripe',
            'oauth' => 'تسجيل الدخول الاجتماعي',
            'seo' => 'SEO / IndexNow',
        ];
    }

    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $schema = self::schema();
            $out = [];
            foreach (array_keys($schema) as $key) {
                $def = $schema[$key];
                $envDefault = self::envDefault($key);
                $value = SystemSetting::ofGroup(self::GROUP)->byKey($key)->first()?->value;
                if ($value === null || $value === '') {
                    $value = $envDefault;
                }
                $out[$key] = (string) $value;
            }

            return $out;
        });
    }

    public function get(string $key, $default = null): ?string
    {
        $all = $this->all();

        return array_key_exists($key, $all) ? ($all[$key] !== '' ? $all[$key] : $default) : $default;
    }

    public function hasStored(string $key): bool
    {
        return SystemSetting::ofGroup(self::GROUP)->byKey($key)->exists();
    }

    public function setMany(array $data): void
    {
        $schema = self::schema();
        foreach ($data as $key => $value) {
            if (! array_key_exists($key, $schema)) {
                continue;
            }
            if (in_array($key, self::SECRET_KEYS, true) && ($value === null || $value === '')) {
                continue;
            }
            SystemSetting::set($key, (string) $value, 'string', self::GROUP);
        }
        $this->clearCache();
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public static function validationRules(): array
    {
        return [
            self::KEY_PAYPAL_CLIENT_ID => ['nullable', 'string', 'max:500'],
            self::KEY_PAYPAL_CLIENT_SECRET => ['nullable', 'string', 'max:500'],
            self::KEY_PAYPAL_MODE => ['nullable', 'in:sandbox,live'],
            self::KEY_STRIPE_KEY => ['nullable', 'string', 'max:500'],
            self::KEY_STRIPE_SECRET => ['nullable', 'string', 'max:500'],
            self::KEY_STRIPE_WEBHOOK_SECRET => ['nullable', 'string', 'max:500'],
            self::KEY_GOOGLE_CLIENT_ID => ['nullable', 'string', 'max:500'],
            self::KEY_GOOGLE_CLIENT_SECRET => ['nullable', 'string', 'max:500'],
            self::KEY_FACEBOOK_CLIENT_ID => ['nullable', 'string', 'max:500'],
            self::KEY_FACEBOOK_CLIENT_SECRET => ['nullable', 'string', 'max:500'],
            self::KEY_INDEXNOW_KEY => ['nullable', 'string', 'max:100'],
        ];
    }

    public static function envDefault(string $key): string
    {
        return match ($key) {
            self::KEY_PAYPAL_CLIENT_ID => (string) env('PAYPAL_CLIENT_ID', ''),
            self::KEY_PAYPAL_CLIENT_SECRET => (string) env('PAYPAL_CLIENT_SECRET', ''),
            self::KEY_PAYPAL_MODE => (string) env('PAYPAL_MODE', 'sandbox'),
            self::KEY_STRIPE_KEY => (string) env('STRIPE_KEY', ''),
            self::KEY_STRIPE_SECRET => (string) env('STRIPE_SECRET', ''),
            self::KEY_STRIPE_WEBHOOK_SECRET => (string) env('STRIPE_WEBHOOK_SECRET', ''),
            self::KEY_GOOGLE_CLIENT_ID => (string) env('GOOGLE_CLIENT_ID', ''),
            self::KEY_GOOGLE_CLIENT_SECRET => (string) env('GOOGLE_CLIENT_SECRET', ''),
            self::KEY_FACEBOOK_CLIENT_ID => (string) env('FACEBOOK_CLIENT_ID', ''),
            self::KEY_FACEBOOK_CLIENT_SECRET => (string) env('FACEBOOK_CLIENT_SECRET', ''),
            self::KEY_INDEXNOW_KEY => (string) env('INDEXNOW_KEY', ''),
            default => '',
        };
    }
}
