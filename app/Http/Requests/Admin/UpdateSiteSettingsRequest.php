<?php

namespace App\Http\Requests\Admin;

use App\Services\SiteSettingsService;
use App\Support\SiteTheme;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return SiteSettingsService::validationRules();
    }

    protected function prepareForValidation(): void
    {
        $color = $this->input(SiteSettingsService::KEY_SITE_PRIMARY_COLOR);
        if (is_string($color) && $color !== '') {
            $normalized = SiteTheme::normalizeHex($color);
            if ($normalized) {
                $this->merge([SiteSettingsService::KEY_SITE_PRIMARY_COLOR => $normalized]);
            }
        }
    }

    public function messages(): array
    {
        return [
            'site_contact_email.email' => 'البريد الإلكتروني للتواصل غير صالح.',
            'site_primary_color.regex' => 'اللون الأساسي يجب أن يكون بصيغة hex مثل #336699.',
            'site_facebook_url.url' => 'رابط فيسبوك غير صالح.',
            'site_twitter_url.url' => 'رابط تويتر غير صالح.',
            'site_instagram_url.url' => 'رابط انستغرام غير صالح.',
            'site_logo_file.image' => 'يجب أن يكون الشعار صورة.',
            'site_logo_file.mimes' => 'صيغ الشعار المدعومة: PNG, JPG, WebP, SVG.',
            'site_logo_file.max' => 'حجم صورة الشعار يجب ألا يتجاوز 2 ميجابايت.',
            'site_favicon_file.image' => 'يجب أن تكون أيقونة الموقع صورة.',
            'site_favicon_file.mimes' => 'صيغ الأيقونة المدعومة: PNG, JPG, WebP, SVG, ICO.',
            'site_favicon_file.max' => 'حجم أيقونة الموقع يجب ألا يتجاوز 512 كيلوبايت.',
        ];
    }
}
