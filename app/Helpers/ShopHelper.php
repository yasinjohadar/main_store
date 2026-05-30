<?php

use App\Services\ShopCatalogService;

if (! function_exists('shop_query')) {
    function shop_query(array $overrides = [], ?string $route = null): string
    {
        $layoutKeys = ShopCatalogService::LAYOUT_KEYS;
        $filterKeys = ['sort', 'per_page', 'search', 'category', 'min_price', 'max_price', 'brand'];

        $params = array_merge(
            session('shop_layout', ShopCatalogService::DEFAULTS),
            request()->only(array_merge($layoutKeys, $filterKeys)),
            $overrides
        );

        unset($params['page']);

        $params = array_filter($params, fn ($value) => $value !== null && $value !== '');

        $base = $route ?? url()->current();

        return str_contains($base, '?')
            ? $base . '&' . http_build_query($params)
            : $base . '?' . http_build_query($params);
    }
}

if (! function_exists('shop_layout_active')) {
    function shop_layout_active(string $key, mixed $value, array $layout): bool
    {
        return ($layout[$key] ?? null) == $value;
    }
}
