<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SiteSettingsService
{
    public const GROUP = 'site';

    public const KEY_SITE_NAME = 'site_name';
    public const KEY_SITE_DESCRIPTION = 'site_description';
    public const KEY_SITE_LOGO = 'site_logo';
    public const KEY_SITE_FAVICON = 'site_favicon';
    public const KEY_SITE_MAINTENANCE_MODE = 'site_maintenance_mode';
    public const KEY_SITE_MAINTENANCE_MESSAGE = 'site_maintenance_message';
    public const KEY_SITE_CONTACT_EMAIL = 'site_contact_email';
    public const KEY_SITE_CONTACT_PHONE = 'site_contact_phone';
    public const KEY_SITE_ADDRESS = 'site_address';
    public const KEY_SITE_TIMEZONE = 'site_timezone';
    public const KEY_SITE_LOCALE = 'site_locale';
    public const KEY_SITE_META_KEYWORDS = 'site_meta_keywords';
    public const KEY_SITE_META_DESCRIPTION = 'site_meta_description';
    public const KEY_SITE_FOOTER_TEXT = 'site_footer_text';
    public const KEY_SITE_FACEBOOK_URL = 'site_facebook_url';
    public const KEY_SITE_TWITTER_URL = 'site_twitter_url';
    public const KEY_SITE_INSTAGRAM_URL = 'site_instagram_url';
    public const KEY_SITE_WHATSAPP_NUMBER = 'site_whatsapp_number';

    private const CACHE_KEY = 'site_settings';
    private const CACHE_TTL = 3600;

    /**
     * Schema for all site settings: key => [ type, default, label_ar, section, hint ]
     */
    public static function schema(): array
    {
        return [
            self::KEY_SITE_NAME => [
                'type' => 'string',
                'default' => config('app.name', 'المتجر'),
                'label' => 'اسم الموقع',
                'section' => 'general',
                'hint' => 'يظهر في الهيدر والعنوان والبريد.',
            ],
            self::KEY_SITE_DESCRIPTION => [
                'type' => 'string',
                'default' => '',
                'label' => 'وصف الموقع',
                'section' => 'general',
                'hint' => 'وصف مختصر يظهر في محركات البحث.',
            ],
            self::KEY_SITE_LOGO => [
                'type' => 'string',
                'default' => '',
                'label' => 'شعار الموقع',
                'section' => 'branding',
                'hint' => 'مسار الصورة (يُرفع من الحقل أدناه).',
            ],
            self::KEY_SITE_FAVICON => [
                'type' => 'string',
                'default' => '',
                'label' => 'أيقونة الموقع (Favicon)',
                'section' => 'branding',
                'hint' => 'صورة صغيرة تظهر في تاب المتصفح.',
            ],
            self::KEY_SITE_MAINTENANCE_MODE => [
                'type' => 'boolean',
                'default' => false,
                'label' => 'تفعيل وضع الصيانة',
                'section' => 'maintenance',
                'hint' => 'عند التفعيل يظهر زوار الموقع صفحة صيانة فقط.',
            ],
            self::KEY_SITE_MAINTENANCE_MESSAGE => [
                'type' => 'string',
                'default' => 'الموقع قيد الصيانة. نعود قريباً.',
                'label' => 'رسالة الصيانة',
                'section' => 'maintenance',
                'hint' => 'النص الذي يراه الزوار أثناء الصيانة.',
            ],
            self::KEY_SITE_CONTACT_EMAIL => [
                'type' => 'string',
                'default' => '',
                'label' => 'البريد الإلكتروني للتواصل',
                'section' => 'contact',
                'hint' => '',
            ],
            self::KEY_SITE_CONTACT_PHONE => [
                'type' => 'string',
                'default' => '',
                'label' => 'رقم الهاتف',
                'section' => 'contact',
                'hint' => '',
            ],
            self::KEY_SITE_ADDRESS => [
                'type' => 'string',
                'default' => '',
                'label' => 'العنوان',
                'section' => 'contact',
                'hint' => 'عنوان المتجر أو الشركة.',
            ],
            self::KEY_SITE_WHATSAPP_NUMBER => [
                'type' => 'string',
                'default' => '',
                'label' => 'رقم واتساب للتواصل',
                'section' => 'contact',
                'hint' => 'بدون + أو مسافات، مثال: 966501234567',
            ],
            self::KEY_SITE_TIMEZONE => [
                'type' => 'string',
                'default' => config('app.timezone', 'Asia/Riyadh'),
                'label' => 'المنطقة الزمنية',
                'section' => 'locale',
                'hint' => 'مثال: Asia/Riyadh',
            ],
            self::KEY_SITE_LOCALE => [
                'type' => 'string',
                'default' => config('app.locale', 'ar'),
                'label' => 'اللغة الافتراضية',
                'section' => 'locale',
                'hint' => 'مثال: ar, en',
            ],
            self::KEY_SITE_META_KEYWORDS => [
                'type' => 'string',
                'default' => '',
                'label' => 'كلمات ميتا (SEO)',
                'section' => 'seo',
                'hint' => 'كلمات مفصولة بفاصلة للبحث.',
            ],
            self::KEY_SITE_META_DESCRIPTION => [
                'type' => 'string',
                'default' => '',
                'label' => 'وصف ميتا (SEO)',
                'section' => 'seo',
                'hint' => 'وصف يظهر في نتائج محركات البحث.',
            ],
            self::KEY_SITE_FOOTER_TEXT => [
                'type' => 'string',
                'default' => '',
                'label' => 'نص التذييل',
                'section' => 'seo',
                'hint' => 'نص يظهر في أسفل الموقع.',
            ],
            self::KEY_SITE_FACEBOOK_URL => [
                'type' => 'string',
                'default' => '',
                'label' => 'رابط فيسبوك',
                'section' => 'social',
                'hint' => '',
            ],
            self::KEY_SITE_TWITTER_URL => [
                'type' => 'string',
                'default' => '',
                'label' => 'رابط تويتر / X',
                'section' => 'social',
                'hint' => '',
            ],
            self::KEY_SITE_INSTAGRAM_URL => [
                'type' => 'string',
                'default' => '',
                'label' => 'رابط انستغرام',
                'section' => 'social',
                'hint' => '',
            ],
        ];
    }

    public static function sectionLabels(): array
    {
        return [
            'general' => 'عام',
            'branding' => 'الهوية والعلامة',
            'contact' => 'التواصل',
            'maintenance' => 'وضع الصيانة',
            'locale' => 'اللغة والمنطقة',
            'seo' => 'SEO والتذييل',
            'social' => 'وسائل التواصل',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $schema = self::schema();
            $out = [];
            foreach (array_keys($schema) as $key) {
                $def = $schema[$key];
                $value = SystemSetting::getValue($key, $def['default']);
                $out[$key] = $this->castValue($value, $def['type']);
            }
            return $out;
        });
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $all = $this->all();
        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function setMany(array $data): void
    {
        $schema = self::schema();
        foreach ($data as $key => $value) {
            if (!array_key_exists($key, $schema)) {
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

    public function storeUpload(string $key, $file): ?string
    {
        $schema = self::schema();
        if (!isset($schema[$key]) || !$file || !$file->isValid()) {
            return null;
        }

        $disk = config('filesystems.default', 'public');
        $oldPath = $this->get($key);
        if ($oldPath && Storage::disk($disk)->exists($oldPath)) {
            Storage::disk($disk)->delete($oldPath);
        }

        $path = $file->store('site-settings', $disk);
        $this->setMany([$key => $path]);
        return $path;
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private function castValue($value, string $type)
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float' => (float) $value,
            'array', 'json' => is_string($value) ? json_decode($value, true) : $value,
            default => (string) $value,
        };
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function validationRules(): array
    {
        return [
            self::KEY_SITE_NAME => ['nullable', 'string', 'max:255'],
            self::KEY_SITE_DESCRIPTION => ['nullable', 'string', 'max:500'],
            self::KEY_SITE_LOGO => ['nullable', 'string', 'max:500'],
            self::KEY_SITE_FAVICON => ['nullable', 'string', 'max:500'],
            self::KEY_SITE_MAINTENANCE_MODE => ['nullable', 'boolean'],
            self::KEY_SITE_MAINTENANCE_MESSAGE => ['nullable', 'string', 'max:1000'],
            self::KEY_SITE_CONTACT_EMAIL => ['nullable', 'email', 'max:255'],
            self::KEY_SITE_CONTACT_PHONE => ['nullable', 'string', 'max:50'],
            self::KEY_SITE_ADDRESS => ['nullable', 'string', 'max:500'],
            self::KEY_SITE_WHATSAPP_NUMBER => ['nullable', 'string', 'max:20'],
            self::KEY_SITE_TIMEZONE => ['nullable', 'string', 'max:50'],
            self::KEY_SITE_LOCALE => ['nullable', 'string', 'max:10'],
            self::KEY_SITE_META_KEYWORDS => ['nullable', 'string', 'max:500'],
            self::KEY_SITE_META_DESCRIPTION => ['nullable', 'string', 'max:500'],
            self::KEY_SITE_FOOTER_TEXT => ['nullable', 'string', 'max:2000'],
            self::KEY_SITE_FACEBOOK_URL => ['nullable', 'url', 'max:500'],
            self::KEY_SITE_TWITTER_URL => ['nullable', 'url', 'max:500'],
            self::KEY_SITE_INSTAGRAM_URL => ['nullable', 'url', 'max:500'],
            'site_logo_file' => ['nullable', 'image', 'max:2048'],
            'site_favicon_file' => ['nullable', 'image', 'max:512'],
        ];
    }
}
