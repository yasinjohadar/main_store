<?php

namespace App\Listeners;

use App\Models\Coupon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;

class CreateReferralCoupon
{
    public function handle(Registered $event): void
    {
        if (! config('store.referral_program_enabled', true)) {
            return;
        }

        $user = $event->user;

        $code = 'REF-' . strtoupper(Str::random(6));
        while (Coupon::where('code', $code)->exists()) {
            $code = 'REF-' . strtoupper(Str::random(6));
        }

        Coupon::create([
            'code' => $code,
            'name' => 'كود إحالة — ' . $user->name,
            'description' => 'كود خصم شخصي لمشاركته مع الأصدقاء',
            'type' => 'percentage',
            'value' => config('store.referral_discount_percent', 10),
            'usage_limit' => null,
            'status' => 'active',
            'applicable_to' => 'entire_store',
            'referrer_user_id' => $user->id,
        ]);
    }
}
