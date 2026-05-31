<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SiteSettingsService
{
    public const GROUP = 'site';

    /** Disk used for logo, favicon, and other public site assets. */
    public const UPLOAD_DISK = 'public';

    public const KEY_SITE_NAME = 'site_name';
    public const KEY_SITE_DESCRIPTION = 'site_description';
    public const KEY_SITE_LOGO = 'site_logo';
    public const KEY_SITE_FAVICON = 'site_favicon';
    public const KEY_SITE_PRIMARY_COLOR = 'site_primary_color';
    public const KEY_SITE_BORDER_RADIUS = 'site_border_radius';
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
    public const KEY_GA4_MEASUREMENT_ID = 'ga4_measurement_id';
    public const KEY_META_PIXEL_ID = 'meta_pixel_id';
    public const KEY_GTM_CONTAINER_ID = 'gtm_container_id';
    public const KEY_GSC_VERIFICATION = 'gsc_verification';
    public const KEY_ORGANIZATION_SAMEAS_LINKEDIN = 'organization_sameas_linkedin';
    public const KEY_SITE_WHATSAPP_NUMBER = 'site_whatsapp_number';

    public const KEY_NEWSLETTER_POPUP_ENABLED = 'newsletter_popup_enabled';
    public const KEY_NEWSLETTER_POPUP_DELAY = 'newsletter_popup_delay';
    public const KEY_NEWSLETTER_POPUP_BADGE_PREFIX = 'newsletter_popup_badge_prefix';
    public const KEY_NEWSLETTER_POPUP_BADGE_HIGHLIGHT = 'newsletter_popup_badge_highlight';
    public const KEY_NEWSLETTER_POPUP_TITLE = 'newsletter_popup_title';
    public const KEY_NEWSLETTER_POPUP_DESCRIPTION = 'newsletter_popup_description';
    public const KEY_NEWSLETTER_POPUP_EMAIL_PLACEHOLDER = 'newsletter_popup_email_placeholder';
    public const KEY_NEWSLETTER_POPUP_SUBMIT_LABEL = 'newsletter_popup_submit_label';
    public const KEY_NEWSLETTER_POPUP_DISMISS_LABEL = 'newsletter_popup_dismiss_label';
    public const KEY_NEWSLETTER_POPUP_IMAGE = 'newsletter_popup_image';

    public const DEFAULT_NEWSLETTER_POPUP_IMAGE = 'frontend/assets/images/demos/demo-rtl/newsletter-bg.jpg';

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
                'hint' => 'PNG أو JPG أو WebP أو SVG — يظهر في الهيدر والتذييل. ارفع صورة جديدة لاستبدال الشعار الحالي.',
            ],
            self::KEY_SITE_FAVICON => [
                'type' => 'string',
                'default' => '',
                'label' => 'أيقونة الموقع (Favicon)',
                'section' => 'branding',
                'hint' => 'PNG أو ICO أو SVG — تظهر في تاب المتصفح. الحجم المفضل 32×32 أو 64×64.',
            ],
            self::KEY_SITE_PRIMARY_COLOR => [
                'type' => 'string',
                'default' => \App\Support\SiteTheme::DEFAULT_PRIMARY,
                'label' => 'اللون الأساسي للموقع',
                'section' => 'branding',
                'hint' => 'يُطبَّق على أزرار المتجر والروابط والعناصر البارزة في الواجهة الخارجية.',
            ],
            self::KEY_SITE_BORDER_RADIUS => [
                'type' => 'integer',
                'default' => \App\Support\SiteTheme::DEFAULT_BORDER_RADIUS,
                'label' => 'انحناء الزوايا (Border Radius)',
                'section' => 'branding',
                'hint' => 'يُطبَّق على بطاقات المنتجات والأزرار والشارات والبانرات في الواجهة. القيمة بالبكسل (0 = زوايا حادة).',
            ],
            self::KEY_NEWSLETTER_POPUP_ENABLED => [
                'type' => 'boolean',
                'default' => true,
                'label' => 'تفعيل نافذة الاشتراك',
                'section' => 'popup',
                'hint' => 'عند التفعيل تظهر نافذة الاشتراك في النشرة البريدية للزوار في الصفحة الرئيسية.',
            ],
            self::KEY_NEWSLETTER_POPUP_DELAY => [
                'type' => 'integer',
                'default' => 7,
                'label' => 'تأخير الظهور (ثوانٍ)',
                'section' => 'popup',
                'hint' => 'المدة قبل ظهور النافذة بعد فتح الصفحة الرئيسية (1–60 ثانية).',
            ],
            self::KEY_NEWSLETTER_POPUP_IMAGE => [
                'type' => 'string',
                'default' => self::DEFAULT_NEWSLETTER_POPUP_IMAGE,
                'label' => 'صورة خلفية النافذة',
                'section' => 'popup',
                'hint' => 'صورة أفقية للخلفية — مقاس مقترح 780×400 بكسل. اترك الافتراضي أو ارفع صورة مخصصة.',
            ],
            self::KEY_NEWSLETTER_POPUP_BADGE_PREFIX => [
                'type' => 'string',
                'default' => 'خصومات حتى',
                'label' => 'نص العرض (قبل التمييز)',
                'section' => 'popup',
                'hint' => 'مثال: «خصومات حتى»',
            ],
            self::KEY_NEWSLETTER_POPUP_BADGE_HIGHLIGHT => [
                'type' => 'string',
                'default' => '25%',
                'label' => 'نص العرض المميز',
                'section' => 'popup',
                'hint' => 'يظهر بلون أساسي — مثال: «25%»',
            ],
            self::KEY_NEWSLETTER_POPUP_TITLE => [
                'type' => 'string',
                'default' => 'اشترك في نشرتنا البريدية',
                'label' => 'العنوان الرئيسي',
                'section' => 'popup',
                'hint' => '',
            ],
            self::KEY_NEWSLETTER_POPUP_DESCRIPTION => [
                'type' => 'string',
                'default' => 'انضم إلى قائمتنا البريدية لتصلك آخر العروض والخصومات الحصرية.',
                'label' => 'الوصف',
                'section' => 'popup',
                'hint' => '',
            ],
            self::KEY_NEWSLETTER_POPUP_EMAIL_PLACEHOLDER => [
                'type' => 'string',
                'default' => 'أدخل بريدك الإلكتروني',
                'label' => 'نص حقل البريد',
                'section' => 'popup',
                'hint' => '',
            ],
            self::KEY_NEWSLETTER_POPUP_SUBMIT_LABEL => [
                'type' => 'string',
                'default' => 'اشتراك',
                'label' => 'نص زر الإرسال',
                'section' => 'popup',
                'hint' => '',
            ],
            self::KEY_NEWSLETTER_POPUP_DISMISS_LABEL => [
                'type' => 'string',
                'default' => 'لا تظهر هذه النافذة مرة أخرى',
                'label' => 'نص «عدم الإظهار مجدداً»',
                'section' => 'popup',
                'hint' => '',
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
            self::KEY_GA4_MEASUREMENT_ID => [
                'type' => 'string',
                'default' => '',
                'label' => 'GA4 Measurement ID',
                'section' => 'analytics',
                'hint' => 'مثال: G-XXXXXXXXXX',
            ],
            self::KEY_META_PIXEL_ID => [
                'type' => 'string',
                'default' => '',
                'label' => 'Meta Pixel ID',
                'section' => 'analytics',
                'hint' => 'معرّف فيسبوك بيكسل',
            ],
            self::KEY_GTM_CONTAINER_ID => [
                'type' => 'string',
                'default' => '',
                'label' => 'Google Tag Manager ID',
                'section' => 'analytics',
                'hint' => 'اختياري: GTM-XXXXXXX',
            ],
            self::KEY_GSC_VERIFICATION => [
                'type' => 'string',
                'default' => '',
                'label' => 'Google Search Console verification',
                'section' => 'analytics',
                'hint' => 'محتوى meta google-site-verification',
            ],
            self::KEY_ORGANIZATION_SAMEAS_LINKEDIN => [
                'type' => 'string',
                'default' => '',
                'label' => 'رابط LinkedIn',
                'section' => 'social',
                'hint' => 'لـ sameAs في Organization schema',
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
            'analytics' => 'التحليلات والتتبع',
            'popup' => 'النوافذ المنبثقة',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function newsletterPopupConfig(): array
    {
        $imagePath = (string) $this->get(self::KEY_NEWSLETTER_POPUP_IMAGE, self::DEFAULT_NEWSLETTER_POPUP_IMAGE);

        return [
            'enabled' => (bool) $this->get(self::KEY_NEWSLETTER_POPUP_ENABLED, true),
            'delay_ms' => max(1, min(60, (int) $this->get(self::KEY_NEWSLETTER_POPUP_DELAY, 7))) * 1000,
            'badge_prefix' => (string) $this->get(self::KEY_NEWSLETTER_POPUP_BADGE_PREFIX, 'خصومات حتى'),
            'badge_highlight' => (string) $this->get(self::KEY_NEWSLETTER_POPUP_BADGE_HIGHLIGHT, '25%'),
            'title' => (string) $this->get(self::KEY_NEWSLETTER_POPUP_TITLE, 'اشترك في نشرتنا البريدية'),
            'description' => (string) $this->get(self::KEY_NEWSLETTER_POPUP_DESCRIPTION, 'انضم إلى قائمتنا البريدية لتصلك آخر العروض والخصومات الحصرية.'),
            'email_placeholder' => (string) $this->get(self::KEY_NEWSLETTER_POPUP_EMAIL_PLACEHOLDER, 'أدخل بريدك الإلكتروني'),
            'submit_label' => (string) $this->get(self::KEY_NEWSLETTER_POPUP_SUBMIT_LABEL, 'اشتراك'),
            'dismiss_label' => (string) $this->get(self::KEY_NEWSLETTER_POPUP_DISMISS_LABEL, 'لا تظهر هذه النافذة مرة أخرى'),
            'image_url' => $this->assetUrl($imagePath) ?? asset(self::DEFAULT_NEWSLETTER_POPUP_IMAGE),
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
        if (! isset($schema[$key]) || ! $file || ! $file->isValid()) {
            return null;
        }

        $disk = self::UPLOAD_DISK;
        $this->deleteStoredFile($this->get($key));

        $path = $file->store('site-settings', $disk);
        $this->setMany([$key => $path]);

        return $path;
    }

    public function deleteAsset(string $key): void
    {
        if (! array_key_exists($key, self::schema())) {
            return;
        }

        $this->deleteStoredFile($this->get($key));
        $this->setMany([$key => '']);
    }

    public function assetExists(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        foreach ($this->assetDisks() as $diskName) {
            if (Storage::disk($diskName)->exists($path)) {
                return true;
            }
        }

        return file_exists(public_path('storage/' . ltrim($path, '/')));
    }

    public function assetUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'frontend/')) {
            return asset($path);
        }

        if (Storage::disk(self::UPLOAD_DISK)->exists($path)) {
            return Storage::disk(self::UPLOAD_DISK)->url($path);
        }

        $legacyDisk = config('filesystems.default', 'local');
        if ($legacyDisk !== self::UPLOAD_DISK && Storage::disk($legacyDisk)->exists($path)) {
            $publicPath = public_path('storage/' . ltrim($path, '/'));
            if (file_exists($publicPath)) {
                return asset('storage/' . ltrim($path, '/'));
            }
        }

        $publicFallback = public_path('storage/' . ltrim($path, '/'));
        if (file_exists($publicFallback)) {
            return asset('storage/' . ltrim($path, '/'));
        }

        return null;
    }

    /**
     * @return list<string>
     */
    protected function assetDisks(): array
    {
        $disks = [self::UPLOAD_DISK];
        $default = config('filesystems.default', 'local');
        if (! in_array($default, $disks, true)) {
            $disks[] = $default;
        }

        return $disks;
    }

    protected function deleteStoredFile(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        foreach ($this->assetDisks() as $diskName) {
            $disk = Storage::disk($diskName);
            if ($disk->exists($path)) {
                $disk->delete($path);
            }
        }
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
            self::KEY_SITE_PRIMARY_COLOR => ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            self::KEY_SITE_BORDER_RADIUS => ['nullable', 'integer', 'min:0', 'max:50'],
            self::KEY_NEWSLETTER_POPUP_ENABLED => ['nullable', 'boolean'],
            self::KEY_NEWSLETTER_POPUP_DELAY => ['nullable', 'integer', 'min:1', 'max:60'],
            self::KEY_NEWSLETTER_POPUP_IMAGE => ['nullable', 'string', 'max:500'],
            self::KEY_NEWSLETTER_POPUP_BADGE_PREFIX => ['nullable', 'string', 'max:100'],
            self::KEY_NEWSLETTER_POPUP_BADGE_HIGHLIGHT => ['nullable', 'string', 'max:50'],
            self::KEY_NEWSLETTER_POPUP_TITLE => ['nullable', 'string', 'max:255'],
            self::KEY_NEWSLETTER_POPUP_DESCRIPTION => ['nullable', 'string', 'max:500'],
            self::KEY_NEWSLETTER_POPUP_EMAIL_PLACEHOLDER => ['nullable', 'string', 'max:255'],
            self::KEY_NEWSLETTER_POPUP_SUBMIT_LABEL => ['nullable', 'string', 'max:100'],
            self::KEY_NEWSLETTER_POPUP_DISMISS_LABEL => ['nullable', 'string', 'max:255'],
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
            self::KEY_GA4_MEASUREMENT_ID => ['nullable', 'string', 'max:50'],
            self::KEY_META_PIXEL_ID => ['nullable', 'string', 'max:50'],
            self::KEY_GTM_CONTAINER_ID => ['nullable', 'string', 'max:50'],
            self::KEY_GSC_VERIFICATION => ['nullable', 'string', 'max:100'],
            self::KEY_ORGANIZATION_SAMEAS_LINKEDIN => ['nullable', 'url', 'max:500'],
            'site_logo_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'site_favicon_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg,ico', 'max:512'],
            'remove_site_logo' => ['nullable', 'boolean'],
            'remove_site_favicon' => ['nullable', 'boolean'],
            'newsletter_popup_image_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_newsletter_popup_image' => ['nullable', 'boolean'],
        ];
    }
}
