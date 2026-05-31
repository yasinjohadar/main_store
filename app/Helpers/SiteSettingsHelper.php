<?php

use App\Services\SiteSettingsService;

if (! function_exists('site_setting')) {
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

if (! function_exists('site_setting_url')) {
    /**
     * Get the public URL for a site setting that stores a file path (logo, favicon).
     */
    function site_setting_url(string $key): ?string
    {
        return app(SiteSettingsService::class)->assetUrl(site_setting($key));
    }
}

if (! function_exists('site_setting_asset_exists')) {
    function site_setting_asset_exists(string $key): bool
    {
        return app(SiteSettingsService::class)->assetExists(site_setting($key));
    }
}
