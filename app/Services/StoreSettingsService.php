<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

class StoreSettingsService
{
    public const GROUP = 'store';

    public const KEY_GUEST_CHECKOUT = 'guest_checkout_enabled';
    public const KEY_PRICES_INCLUDE_TAX = 'prices_include_tax';
    public const KEY_DEFAULT_ORDER_STATUS = 'default_order_status';
    public const KEY_LOYALTY_REDEMPTION = 'loyalty_redemption_enabled';
    public const KEY_DEFAULT_COUNTRY = 'default_country';
    public const KEY_DEFAULT_CURRENCY = 'default_currency';
    public const KEY_LOW_STOCK_THRESHOLD = 'low_stock_threshold';
    public const KEY_ROBOTS_ALLOW_INDEX = 'robots_allow_index';

    private const CACHE_KEY = 'store_settings';
    private const CACHE_TTL = 3600;

    public static function schema(): array
    {
        return [
            self::KEY_GUEST_CHECKOUT => [
                'type' => 'boolean',
                'default' => false,
                'label' => 'السماح بالشراء كضيف',
                'section' => 'checkout',
                'hint' => 'عند التفعيل يمكن إتمام الطلب بدون تسجيل دخول.',
            ],
            self::KEY_PRICES_INCLUDE_TAX => [
                'type' => 'boolean',
                'default' => false,
                'label' => 'الأسعار تشمل الضريبة',
                'section' => 'tax',
                'hint' => 'يؤثر على طريقة حساب الضريبة في السلة والدفع.',
            ],
            self::KEY_DEFAULT_ORDER_STATUS => [
                'type' => 'string',
                'default' => 'pending',
                'label' => 'حالة الطلب الافتراضية',
                'section' => 'orders',
                'hint' => 'slug من جدول حالات الطلب (مثل pending).',
            ],
            self::KEY_LOYALTY_REDEMPTION => [
                'type' => 'boolean',
                'default' => true,
                'label' => 'تفعيل استبدال نقاط الولاء',
                'section' => 'loyalty',
                'hint' => 'يسمح للعملاء باستخدام النقاط عند الدفع.',
            ],
            self::KEY_DEFAULT_COUNTRY => [
                'type' => 'string',
                'default' => 'SA',
                'label' => 'الدولة الافتراضية',
                'section' => 'locale',
                'hint' => 'رمز ISO-3166 (مثال: SA).',
            ],
            self::KEY_DEFAULT_CURRENCY => [
                'type' => 'string',
                'default' => 'SAR',
                'label' => 'العملة الافتراضية',
                'section' => 'locale',
                'hint' => 'رمز العملة (مثال: SAR).',
            ],
            self::KEY_LOW_STOCK_THRESHOLD => [
                'type' => 'integer',
                'default' => 5,
                'label' => 'حد تنبيه المخزون المنخفض',
                'section' => 'inventory',
                'hint' => 'يُرسل تنبيه إداري عندما يكون المخزون ≤ هذا الرقم.',
            ],
            self::KEY_ROBOTS_ALLOW_INDEX => [
                'type' => 'boolean',
                'default' => true,
                'label' => 'السماح للفهرسة (robots.txt)',
                'section' => 'seo',
                'hint' => 'عند الإيقاف يُمنع الزحف عبر robots.txt.',
            ],
        ];
    }

    public static function sectionLabels(): array
    {
        return [
            'checkout' => 'الدفع',
            'tax' => 'الضريبة',
            'orders' => 'الطلبات',
            'loyalty' => 'الولاء',
            'locale' => 'المنطقة والعملة',
            'inventory' => 'المخزون',
            'seo' => 'SEO',
        ];
    }

    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $schema = self::schema();
            $out = [];
            foreach (array_keys($schema) as $key) {
                $def = $schema[$key];
                $envDefault = config('store.' . $key, $def['default']);
                $value = SystemSetting::ofGroup(self::GROUP)->byKey($key)->first()?->value;
                if ($value === null) {
                    $value = $envDefault;
                }
                $out[$key] = $this->castValue($value, $def['type']);
            }

            return $out;
        });
    }

    public function get(string $key, $default = null)
    {
        $all = $this->all();

        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    public function setMany(array $data): void
    {
        $schema = self::schema();
        foreach ($data as $key => $value) {
            if (! array_key_exists($key, $schema)) {
                continue;
            }
            $def = $schema[$key];
            $type = $def['type'];
            if ($type === 'boolean') {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
            } else {
                $value = (string) $value;
            }
            SystemSetting::set($key, $value, $type, self::GROUP);
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
            self::KEY_GUEST_CHECKOUT => ['nullable', 'boolean'],
            self::KEY_PRICES_INCLUDE_TAX => ['nullable', 'boolean'],
            self::KEY_DEFAULT_ORDER_STATUS => ['required', 'string', 'max:50'],
            self::KEY_LOYALTY_REDEMPTION => ['nullable', 'boolean'],
            self::KEY_DEFAULT_COUNTRY => ['required', 'string', 'size:2'],
            self::KEY_DEFAULT_CURRENCY => ['required', 'string', 'max:3'],
            self::KEY_LOW_STOCK_THRESHOLD => ['required', 'integer', 'min:0'],
            self::KEY_ROBOTS_ALLOW_INDEX => ['nullable', 'boolean'],
        ];
    }

    private function castValue($value, string $type)
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float' => (float) $value,
            default => (string) $value,
        };
    }
}
