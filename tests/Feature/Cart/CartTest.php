<?php

use App\Models\Product;
use App\Models\ShoppingCart;
use App\Models\User;

test('user can add a product to the cart', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 50, 'stock_quantity' => 10]);

    $response = $this->actingAs($user)->post(route('frontend.cart.store'), [
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response->assertSessionHasNoErrors();

    $cart = ShoppingCart::where('user_id', $user->id)->firstOrFail();
    expect($cart->items)->toHaveCount(1);
    expect($cart->items->first()->quantity)->toBe(2);
    expect((float) $cart->subtotal)->toBe(100.0);
});

test('adding the same product twice increments quantity instead of duplicating the row', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 20, 'stock_quantity' => 10]);

    $this->actingAs($user)->post(route('frontend.cart.store'), ['product_id' => $product->id, 'quantity' => 1]);
    $this->actingAs($user)->post(route('frontend.cart.store'), ['product_id' => $product->id, 'quantity' => 2]);

    $cart = ShoppingCart::where('user_id', $user->id)->firstOrFail();
    expect($cart->items)->toHaveCount(1);
    expect($cart->items->first()->quantity)->toBe(3);
});

test('adding more than available stock is rejected when backorders are not allowed', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 2, 'track_quantity' => true, 'allow_backorder' => false]);

    $response = $this->actingAs($user)->post(route('frontend.cart.store'), [
        'product_id' => $product->id,
        'quantity' => 5,
    ]);

    $response->assertSessionHasErrors('quantity');
    $cart = ShoppingCart::where('user_id', $user->id)->first();
    expect($cart?->items()->count() ?? 0)->toBe(0);
});

test('user can update the quantity of a cart item', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 30, 'stock_quantity' => 10]);
    $this->actingAs($user)->post(route('frontend.cart.store'), ['product_id' => $product->id, 'quantity' => 1]);

    $cart = ShoppingCart::where('user_id', $user->id)->firstOrFail();
    $item = $cart->items->first();

    $response = $this->actingAs($user)->patch(route('frontend.cart.update', $item->id), ['quantity' => 4]);
    $response->assertSessionHasNoErrors();

    $item->refresh();
    expect($item->quantity)->toBe(4);
    expect((float) $item->line_total)->toBe(120.0);
});

test('updating quantity beyond stock is rejected', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 3, 'track_quantity' => true, 'allow_backorder' => false]);
    $this->actingAs($user)->post(route('frontend.cart.store'), ['product_id' => $product->id, 'quantity' => 1]);

    $cart = ShoppingCart::where('user_id', $user->id)->firstOrFail();
    $item = $cart->items->first();

    $response = $this->actingAs($user)->patch(route('frontend.cart.update', $item->id), ['quantity' => 10]);
    $response->assertStatus(302);
    $response->assertSessionHasErrors('quantity');

    $item->refresh();
    expect($item->quantity)->toBe(1);
});

test('user can remove an item from the cart', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 10, 'stock_quantity' => 10]);
    $this->actingAs($user)->post(route('frontend.cart.store'), ['product_id' => $product->id, 'quantity' => 1]);

    $cart = ShoppingCart::where('user_id', $user->id)->firstOrFail();
    $item = $cart->items->first();

    $this->actingAs($user)->delete(route('frontend.cart.destroy', $item->id));

    $cart->refresh();
    expect($cart->items)->toHaveCount(0);
});

test('cart totals recompute correctly across multiple items', function () {
    $user = User::factory()->create();
    $productA = Product::factory()->create(['price' => 25, 'stock_quantity' => 10]);
    $productB = Product::factory()->create(['price' => 40, 'stock_quantity' => 10]);

    $this->actingAs($user)->post(route('frontend.cart.store'), ['product_id' => $productA->id, 'quantity' => 2]);
    $this->actingAs($user)->post(route('frontend.cart.store'), ['product_id' => $productB->id, 'quantity' => 3]);

    $cart = ShoppingCart::where('user_id', $user->id)->firstOrFail();

    // 25*2 + 40*3 = 170
    expect((float) $cart->subtotal)->toBe(170.0);
    expect((int) $cart->items_count)->toBe(5);
    expect((float) $cart->total)->toBe(170.0);
});
