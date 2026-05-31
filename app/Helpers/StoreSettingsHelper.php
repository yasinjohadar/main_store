<?php

use App\Services\StoreSettingsService;

if (! function_exists('store_setting')) {
    function store_setting(string $key, $default = null)
    {
        return app(StoreSettingsService::class)->get($key, $default);
    }
}
