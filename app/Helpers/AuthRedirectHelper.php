<?php

use App\Models\User;

if (! function_exists('auth_home_route')) {
    function auth_home_route(?User $user = null): string
    {
        $user ??= auth()->user();

        if ($user?->hasRole('admin')) {
            return route('admin.dashboard', absolute: false);
        }

        return route('frontend.account', absolute: false);
    }
}
