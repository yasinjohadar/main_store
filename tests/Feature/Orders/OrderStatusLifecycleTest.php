<?php

use App\Events\OrderStatusChanged;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\Event;

test('changing an order status creates a status history entry', function () {
    $admin = User::factory()->create();
    $pending = OrderStatus::where('slug', 'pending')->firstOrFail();
    $processing = OrderStatus::where('slug', 'processing')->firstOrFail();

    $order = Order::factory()->create(['order_status_id' => $pending->id]);

    $this->actingAs($admin)->post(route('admin.orders.update-status', $order), [
        'order_status_id' => $processing->id,
        'admin_note' => 'shipping soon',
    ])->assertSessionHasNoErrors();

    $order->refresh();
    expect($order->order_status_id)->toBe($processing->id);
    expect($order->admin_note)->toBe('shipping soon');

    $history = OrderStatusHistory::where('order_id', $order->id)->firstOrFail();
    expect($history->old_status_id)->toBe($pending->id);
    expect($history->new_status_id)->toBe($processing->id);
    expect($history->changed_by)->toBe($admin->id);
});

test('changing an order status dispatches the OrderStatusChanged event', function () {
    Event::fake([OrderStatusChanged::class]);

    $admin = User::factory()->create();
    $pending = OrderStatus::where('slug', 'pending')->firstOrFail();
    $shipped = OrderStatus::where('slug', 'shipped')->firstOrFail();

    $order = Order::factory()->create(['order_status_id' => $pending->id]);

    $this->actingAs($admin)->post(route('admin.orders.update-status', $order), [
        'order_status_id' => $shipped->id,
    ]);

    Event::assertDispatched(OrderStatusChanged::class, function ($event) use ($order, $shipped) {
        return $event->order->id === $order->id
            && $event->order->order_status_id === $shipped->id
            && $event->previousStatusSlug === 'pending';
    });
});

test('cancelling a shipped order does not change status again if unchanged', function () {
    $admin = User::factory()->create();
    $pending = OrderStatus::where('slug', 'pending')->firstOrFail();

    $order = Order::factory()->create(['order_status_id' => $pending->id]);

    $this->actingAs($admin)->post(route('admin.orders.update-status', $order), [
        'order_status_id' => $pending->id,
        'admin_note' => 'no change',
    ]);

    expect(OrderStatusHistory::where('order_id', $order->id)->count())->toBe(0);
    $order->refresh();
    expect($order->admin_note)->toBe('no change');
});

test('cancelling an order returns stock to the product', function () {
    $admin = User::factory()->create();
    $pending = OrderStatus::where('slug', 'pending')->firstOrFail();
    $cancelled = OrderStatus::where('slug', 'cancelled')->firstOrFail();

    $product = \App\Models\Product::factory()->create(['stock_quantity' => 5, 'track_quantity' => true]);
    $order = Order::factory()->create(['order_status_id' => $pending->id]);
    \App\Models\OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $this->actingAs($admin)->post(route('admin.orders.update-status', $order), [
        'order_status_id' => $cancelled->id,
    ]);

    $product->refresh();
    expect($product->stock_quantity)->toBe(7);
});
