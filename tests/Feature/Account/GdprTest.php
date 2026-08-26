<?php

use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\User;
use App\Services\GdprService;

test('data export contains the customer profile, addresses, orders and reviews', function () {
    $user = User::factory()->create(['name' => 'أحمد', 'phone' => '0500000000']);
    CustomerAddress::factory()->create(['user_id' => $user->id, 'city' => 'الرياض']);
    $order = Order::factory()->create(['user_id' => $user->id]);
    OrderItem::factory()->create(['order_id' => $order->id, 'product_name' => 'منتج تجريبي']);
    Review::factory()->create(['user_id' => $user->id, 'rating' => 5]);

    $export = app(GdprService::class)->exportUserData($user);

    expect($export['profile']['name'])->toBe('أحمد');
    expect($export['addresses'])->toHaveCount(1);
    expect($export['addresses'][0]['city'])->toBe('الرياض');
    expect($export['orders'])->toHaveCount(1);
    expect($export['orders'][0]['items'][0]['product_name'])->toBe('منتج تجريبي');
    expect($export['reviews'])->toHaveCount(1);
});

test('the export route returns a downloadable json file for the logged-in user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('frontend.account.privacy.export'));

    $response->assertOk();
    $response->assertHeader('content-disposition');
    expect($response->json('profile.email'))->toBe($user->email);
});

test('anonymizing a user strips personal data but keeps the order for accounting records', function () {
    $user = User::factory()->create(['email' => 'real@example.com', 'name' => 'Real Name']);
    CustomerAddress::factory()->create(['user_id' => $user->id]);
    $order = Order::factory()->create(['user_id' => $user->id, 'order_number' => 'ORD-KEEP-ME']);

    app(GdprService::class)->anonymizeUser($user);

    $user->refresh();
    expect($user->name)->not->toBe('Real Name');
    expect($user->email)->not->toBe('real@example.com');
    expect($user->is_active)->toBeFalse();
    expect($user->addresses()->count())->toBe(0);
    expect(Order::where('order_number', 'ORD-KEEP-ME')->exists())->toBeTrue();
});

test('requesting account deletion via the route anonymizes and logs the user out', function () {
    $user = User::factory()->create(['email' => 'real2@example.com']);

    $response = $this->actingAs($user)->delete(route('frontend.account.privacy.destroy'), [
        'confirm' => true,
    ]);

    $response->assertRedirect(route('frontend.home'));
    $this->assertGuest();
    expect($user->fresh()->email)->not->toBe('real2@example.com');
});
