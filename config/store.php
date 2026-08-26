<?php

/**
 * إعدادات المتجر — القيم الافتراضية من .env.
 * تُستبدَل تلقائياً من لوحة الإدارة: الإعدادات → إعدادات المتجر
 * (StoreSettingsService + DynamicConfigLoader).
 */
return [
    'guest_checkout_enabled' => env('STORE_GUEST_CHECKOUT', false),
    'prices_include_tax' => env('STORE_PRICES_INCLUDE_TAX', false),
    'default_order_status' => env('STORE_DEFAULT_ORDER_STATUS', 'pending'),
    'loyalty_redemption_enabled' => env('STORE_LOYALTY_REDEMPTION', true),
    'default_country' => env('STORE_DEFAULT_COUNTRY', 'SA'),
    'default_currency' => env('STORE_DEFAULT_CURRENCY', 'SAR'),
    'low_stock_threshold' => (int) env('STORE_LOW_STOCK_THRESHOLD', 5),
    'robots_allow_index' => env('STORE_ROBOTS_ALLOW_INDEX', true),
    'abandoned_cart_recovery_enabled' => env('STORE_ABANDONED_CART_RECOVERY', true),
    'abandoned_cart_hours' => (int) env('STORE_ABANDONED_CART_HOURS', 3),
    'referral_program_enabled' => env('STORE_REFERRAL_PROGRAM', true),
    'referral_discount_percent' => (float) env('STORE_REFERRAL_DISCOUNT_PERCENT', 10),
    'referral_bonus_points' => (int) env('STORE_REFERRAL_BONUS_POINTS', 100),
];
