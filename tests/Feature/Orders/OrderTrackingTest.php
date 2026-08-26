<?php

use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderStatus;
use App\Models\User;

test('admin can set a tracking number and carrier on an order', function () {
    $admin = User::factory()->create();
    $order = Order::factory()->create();

    $this->actingAs($admin)->post(route('admin.orders.update-status', $order), [
        'order_status_id' => $order->order_status_id,
        'tracking_number' => 'ARM123456789',
        'tracking_carrier' => 'أرامكس',
    ])->assertSessionHasNoErrors();

    $order->refresh();
    expect($order->tracking_number)->toBe('ARM123456789');
    expect($order->tracking_carrier)->toBe('أرامكس');
});

test('a customer can see the tracking number on the public track-order page', function () {
    $order = Order::factory()->create([
        'order_number' => 'ORD-TRACK-1',
        'tracking_number' => 'ARM999',
        'tracking_carrier' => 'سمسا',
    ]);
    OrderAddress::factory()->create(['order_id' => $order->id, 'email' => 'buyer@example.com', 'type' => 'shipping']);

    $response = $this->post(route('frontend.track-order'), [
        'order_number' => 'ORD-TRACK-1',
        'email' => 'buyer@example.com',
    ]);

    $response->assertOk();
    $response->assertSee('ARM999');
    $response->assertSee('سمسا');
});
