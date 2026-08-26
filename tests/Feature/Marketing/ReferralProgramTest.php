<?php

use App\Listeners\CreateReferralCoupon;
use App\Models\Coupon;
use App\Models\LoyaltyPointTransaction;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

test('registering a user creates a personal referral coupon', function () {
    $user = User::factory()->create();

    (new CreateReferralCoupon())->handle(new Registered($user));

    $coupon = Coupon::where('referrer_user_id', $user->id)->first();

    expect($coupon)->not->toBeNull();
    expect($coupon->type)->toBe('percentage');
    expect($coupon->status)->toBe('active');
    expect($coupon->code)->toStartWith('REF-');
});

test('referrer earns loyalty points when the referred customer completes their first order', function () {
    $admin = User::factory()->create();
    $referrer = User::factory()->create(['loyalty_points_balance' => 0]);
    $friend = User::factory()->create();

    $coupon = Coupon::create([
        'code' => 'REF-TESTER',
        'name' => 'كود إحالة',
        'type' => 'percentage',
        'value' => 10,
        'status' => 'active',
        'applicable_to' => 'entire_store',
        'referrer_user_id' => $referrer->id,
    ]);

    $pending = OrderStatus::where('slug', 'pending')->firstOrFail();
    $completed = OrderStatus::where('slug', 'completed')->firstOrFail();

    $order = Order::factory()->create([
        'user_id' => $friend->id,
        'coupon_code' => $coupon->code,
        'order_status_id' => $pending->id,
    ]);

    $this->actingAs($admin)->post(route('admin.orders.update-status', $order), [
        'order_status_id' => $completed->id,
    ]);

    expect($referrer->fresh()->loyalty_points_balance)->toBe(config('store.referral_bonus_points'));
});

test('referrer does not earn points from their own order using their own coupon', function () {
    $admin = User::factory()->create();
    $referrer = User::factory()->create(['loyalty_points_balance' => 0]);

    $coupon = Coupon::create([
        'code' => 'REF-SELF',
        'name' => 'كود إحالة',
        'type' => 'percentage',
        'value' => 10,
        'status' => 'active',
        'applicable_to' => 'entire_store',
        'referrer_user_id' => $referrer->id,
    ]);

    $pending = OrderStatus::where('slug', 'pending')->firstOrFail();
    $completed = OrderStatus::where('slug', 'completed')->firstOrFail();

    $order = Order::factory()->create([
        'user_id' => $referrer->id,
        'coupon_code' => $coupon->code,
        'order_status_id' => $pending->id,
    ]);

    $this->actingAs($admin)->post(route('admin.orders.update-status', $order), [
        'order_status_id' => $completed->id,
    ]);

    // The buyer still earns ordinary purchase points for their own order — only
    // the extra referral bonus (an ADJUST transaction) must not be granted.
    expect(LoyaltyPointTransaction::where('user_id', $referrer->id)->where('type', LoyaltyPointTransaction::TYPE_ADJUST)->exists())->toBeFalse();
});

test('referrer only earns points once even if the referred customer completes a second order', function () {
    $admin = User::factory()->create();
    $referrer = User::factory()->create(['loyalty_points_balance' => 0]);
    $friend = User::factory()->create();

    $coupon = Coupon::create([
        'code' => 'REF-TWICE',
        'name' => 'كود إحالة',
        'type' => 'percentage',
        'value' => 10,
        'status' => 'active',
        'applicable_to' => 'entire_store',
        'referrer_user_id' => $referrer->id,
    ]);

    $pending = OrderStatus::where('slug', 'pending')->firstOrFail();
    $completed = OrderStatus::where('slug', 'completed')->firstOrFail();

    $firstOrder = Order::factory()->create([
        'user_id' => $friend->id,
        'coupon_code' => $coupon->code,
        'order_status_id' => $pending->id,
    ]);
    $this->actingAs($admin)->post(route('admin.orders.update-status', $firstOrder), [
        'order_status_id' => $completed->id,
    ]);

    $secondOrder = Order::factory()->create([
        'user_id' => $friend->id,
        'coupon_code' => $coupon->code,
        'order_status_id' => $pending->id,
    ]);
    $this->actingAs($admin)->post(route('admin.orders.update-status', $secondOrder), [
        'order_status_id' => $completed->id,
    ]);

    expect($referrer->fresh()->loyalty_points_balance)->toBe(config('store.referral_bonus_points'));
});
