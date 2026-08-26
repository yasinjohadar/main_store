<?php

use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ShoppingCart;
use App\Models\User;

test('checkout with cash on delivery creates an order, items, addresses and decrements stock', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 50, 'stock_quantity' => 10, 'track_quantity' => true]);
    $this->actingAs($user)->post(route('frontend.cart.store'), ['product_id' => $product->id, 'quantity' => 3]);

    $paymentMethod = PaymentMethod::factory()->cod()->create();

    $response = $this->actingAs($user)->post(route('frontend.checkout.store'), [
        'first_name' => 'محمد',
        'last_name' => 'أحمد',
        'email' => 'customer@example.com',
        'phone' => '0500000000',
        'city' => 'الرياض',
        'address' => 'شارع الملك فهد',
        'payment_method_id' => $paymentMethod->id,
    ]);

    $order = Order::where('user_id', $user->id)->firstOrFail();
    $response->assertRedirect(route('frontend.checkout.success', $order));

    expect($order->items)->toHaveCount(1);
    expect($order->items->first()->quantity)->toBe(3);
    expect((float) $order->subtotal)->toBe(150.0);
    expect((float) $order->total)->toBe(150.0);

    expect($order->addresses()->where('type', 'shipping')->exists())->toBeTrue();
    expect($order->addresses()->where('type', 'billing')->exists())->toBeTrue();

    $product->refresh();
    expect($product->stock_quantity)->toBe(7);

    expect($order->payments()->where('payment_method_id', $paymentMethod->id)->exists())->toBeTrue();

    // cart is cleared after a successful order
    $cart = ShoppingCart::where('user_id', $user->id)->first();
    expect($cart?->items()->count() ?? 0)->toBe(0);
});

test('checkout with bank transfer creates a pending payment and order', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 80, 'stock_quantity' => 5, 'track_quantity' => true]);
    $this->actingAs($user)->post(route('frontend.cart.store'), ['product_id' => $product->id, 'quantity' => 1]);

    $paymentMethod = PaymentMethod::factory()->bankTransfer()->create();

    $response = $this->actingAs($user)->post(route('frontend.checkout.store'), [
        'first_name' => 'سارة',
        'last_name' => 'علي',
        'email' => 'sara@example.com',
        'phone' => '0511111111',
        'city' => 'جدة',
        'address' => 'شارع التحلية',
        'payment_method_id' => $paymentMethod->id,
    ]);

    $order = Order::where('user_id', $user->id)->firstOrFail();
    $response->assertRedirect(route('frontend.checkout.success', $order));

    $payment = $order->payments()->firstOrFail();
    expect($payment->status)->toBe('pending');

    $product->refresh();
    expect($product->stock_quantity)->toBe(4);
});

test('checkout fails gracefully with an empty cart', function () {
    $user = User::factory()->create();
    $paymentMethod = PaymentMethod::factory()->cod()->create();

    $response = $this->actingAs($user)->post(route('frontend.checkout.store'), [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'phone' => '0500000000',
        'city' => 'الرياض',
        'address' => 'street',
        'payment_method_id' => $paymentMethod->id,
    ]);

    $response->assertRedirect(route('frontend.cart.index'));
    expect(Order::count())->toBe(0);
});

test('checkout with an unconfigured stripe gateway still creates the order but marks payment failed', function () {
    // No Stripe secret key is configured in the test environment, and StripeGateway
    // checks credentials before making any Stripe API/network call, so this exercises
    // the real gateway class without hitting the network.
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 60, 'stock_quantity' => 5]);
    $this->actingAs($user)->post(route('frontend.cart.store'), ['product_id' => $product->id, 'quantity' => 1]);

    $paymentMethod = PaymentMethod::factory()->stripe()->create();

    $response = $this->actingAs($user)->post(route('frontend.checkout.store'), [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'phone' => '0500000000',
        'city' => 'الرياض',
        'address' => 'street',
        'payment_method_id' => $paymentMethod->id,
    ]);

    $order = Order::where('user_id', $user->id)->firstOrFail();
    $response->assertRedirect(route('frontend.checkout.success', $order));

    $payment = $order->payments()->firstOrFail();
    expect($payment->status)->toBe('failed');
});
