<?php

use App\Services\SiteSettingsService;

if (!function_exists('site_setting')) {
    /**
     * Get a site setting value by key (cached).
     *
     * @param string $key Key from SiteSettingsService (e.g. site_name, site_logo)
     * @param mixed $default Default if not set
     * @return mixed
     */
    function site_setting(string $key, $default = null)
    {
        return app(SiteSettingsService::class)->get($key, $default);
    }
}

if (!function_exists('site_setting_url')) {
    /**
     * Get the full URL for a site setting that stores a file path (e.g. site_logo, site_favicon).
     *
     * @param string $key Key such as SiteSettingsService::KEY_SITE_LOGO or KEY_SITE_FAVICON
     * @return string|null URL or null if not set
     */
    function site_setting_url(string $key): ?string
    {
        $path = site_setting($key);
        if (empty($path)) {
            return null;
        }
        $disk = config('filesystems.default', 'public');
        try {
            return \Illuminate\Support\Facades\Storage::disk($disk)->url($path);
        } catch (\Throwable $e) {
            return asset('storage/' . ltrim($path, '/'));
        }
    }
}
