<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\User;

test('admin can create a return request for a delivered order', function () {
    $admin = User::factory()->create();
    $completed = OrderStatus::where('slug', 'completed')->firstOrFail();

    $product = Product::factory()->create(['stock_quantity' => 5, 'track_quantity' => true]);
    $order = Order::factory()->create(['order_status_id' => $completed->id]);
    $orderItem = OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 3,
    ]);

    $response = $this->actingAs($admin)->post(route('admin.order-returns.store', $order), [
        'reason' => 'Item damaged',
        'items' => [
            ['order_item_id' => $orderItem->id, 'quantity' => 2],
        ],
    ]);

    $orderReturn = OrderReturn::where('order_id', $order->id)->firstOrFail();
    $response->assertRedirect(route('admin.order-returns.show', $orderReturn));

    expect($orderReturn->status)->toBe(OrderReturn::STATUS_PENDING);
    expect($orderReturn->items)->toHaveCount(1);
    expect($orderReturn->items->first()->quantity)->toBe(2);
});

test('a return request cannot exceed the ordered quantity', function () {
    $admin = User::factory()->create();
    $product = Product::factory()->create();
    $order = Order::factory()->create();
    $orderItem = OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $this->actingAs($admin)->post(route('admin.order-returns.store', $order), [
        'items' => [
            ['order_item_id' => $orderItem->id, 'quantity' => 5],
        ],
    ]);

    expect(OrderReturn::where('order_id', $order->id)->count())->toBe(0);
});

test('admin approving a return restores stock and marks it approved', function () {
    $admin = User::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 3, 'track_quantity' => true]);
    $order = Order::factory()->create();
    $orderItem = OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $orderReturn = OrderReturn::factory()->create(['order_id' => $order->id]);
    \App\Models\OrderReturnItem::factory()->create([
        'order_return_id' => $orderReturn->id,
        'order_item_id' => $orderItem->id,
        'quantity' => 2,
    ]);

    $response = $this->actingAs($admin)->post(route('admin.order-returns.approve', $orderReturn), [
        'admin_note' => 'approved, restocked',
    ]);
    $response->assertSessionHasNoErrors();

    $orderReturn->refresh();
    expect($orderReturn->status)->toBe(OrderReturn::STATUS_APPROVED);
    expect($orderReturn->processed_by)->toBe($admin->id);

    $product->refresh();
    expect($product->stock_quantity)->toBe(5);
});

test('admin can reject a pending return request', function () {
    $admin = User::factory()->create();
    $order = Order::factory()->create();
    $orderReturn = OrderReturn::factory()->create(['order_id' => $order->id]);

    $this->actingAs($admin)->post(route('admin.order-returns.reject', $orderReturn), [
        'admin_note' => 'not eligible',
    ]);

    $orderReturn->refresh();
    expect($orderReturn->status)->toBe(OrderReturn::STATUS_REJECTED);
    expect($orderReturn->admin_note)->toBe('not eligible');
});

test('an already processed return cannot be approved again', function () {
    $admin = User::factory()->create();
    $order = Order::factory()->create();
    $orderReturn = OrderReturn::factory()->approved()->create(['order_id' => $order->id]);

    $this->actingAs($admin)->post(route('admin.order-returns.approve', $orderReturn));

    $orderReturn->refresh();
    expect($orderReturn->status)->toBe(OrderReturn::STATUS_APPROVED);
});
