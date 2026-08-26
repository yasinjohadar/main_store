<?php

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Product;
use App\Models\ShoppingCart;
use App\Models\User;

test('a valid percentage coupon is applied and discounts the cart', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 100, 'stock_quantity' => 10]);
    $this->actingAs($user)->post(route('frontend.cart.store'), ['product_id' => $product->id, 'quantity' => 1]);

    $coupon = Coupon::factory()->percentage(10)->create(['code' => 'PCT10']);

    $response = $this->actingAs($user)->post(route('frontend.cart.apply-coupon'), ['coupon' => $coupon->code]);
    $response->assertSessionHasNoErrors();

    $cart = ShoppingCart::where('user_id', $user->id)->firstOrFail();
    expect($cart->coupon_code)->toBe('PCT10');
    expect((float) $cart->discount_amount)->toBe(10.0);
    expect((float) $cart->total)->toBe(90.0);
});

test('a valid fixed amount coupon is applied and discounts the cart', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 100, 'stock_quantity' => 10]);
    $this->actingAs($user)->post(route('frontend.cart.store'), ['product_id' => $product->id, 'quantity' => 1]);

    $coupon = Coupon::factory()->fixedAmount(15)->create(['code' => 'FIXED15']);

    $this->actingAs($user)->post(route('frontend.cart.apply-coupon'), ['coupon' => $coupon->code])
        ->assertSessionHasNoErrors();

    $cart = ShoppingCart::where('user_id', $user->id)->firstOrFail();
    expect((float) $cart->discount_amount)->toBe(15.0);
    expect((float) $cart->total)->toBe(85.0);
});

test('a buy_x_get_y coupon caps the discount at the cart subtotal', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 30, 'stock_quantity' => 10]);
    $this->actingAs($user)->post(route('frontend.cart.store'), ['product_id' => $product->id, 'quantity' => 1]);

    // value (100) exceeds the subtotal (30), discount should be capped at subtotal.
    $coupon = Coupon::factory()->buyXGetY(100)->create(['code' => 'BXGY']);

    $this->actingAs($user)->post(route('frontend.cart.apply-coupon'), ['coupon' => $coupon->code])
        ->assertSessionHasNoErrors();

    $cart = ShoppingCart::where('user_id', $user->id)->firstOrFail();
    expect((float) $cart->discount_amount)->toBe(30.0);
    expect((float) $cart->total)->toBe(0.0);
});

test('an expired coupon is rejected', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 100, 'stock_quantity' => 10]);
    $this->actingAs($user)->post(route('frontend.cart.store'), ['product_id' => $product->id, 'quantity' => 1]);

    $coupon = Coupon::factory()->expired()->create(['code' => 'EXPIRED1']);

    $response = $this->actingAs($user)->post(route('frontend.cart.apply-coupon'), ['coupon' => $coupon->code]);
    $response->assertSessionHasErrors('coupon');

    $cart = ShoppingCart::where('user_id', $user->id)->firstOrFail();
    expect($cart->coupon_code)->toBeNull();
});

test('a coupon past its usage limit is rejected', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 100, 'stock_quantity' => 10]);
    $this->actingAs($user)->post(route('frontend.cart.store'), ['product_id' => $product->id, 'quantity' => 1]);

    $coupon = Coupon::factory()->withUsageLimit(1)->create(['code' => 'LIMIT1']);
    CouponUsage::factory()->create(['coupon_id' => $coupon->id]);

    $response = $this->actingAs($user)->post(route('frontend.cart.apply-coupon'), ['coupon' => $coupon->code]);
    $response->assertSessionHasErrors('coupon');

    $cart = ShoppingCart::where('user_id', $user->id)->firstOrFail();
    expect($cart->coupon_code)->toBeNull();
});

test('a coupon below its minimum order amount is rejected', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 20, 'stock_quantity' => 10]);
    $this->actingAs($user)->post(route('frontend.cart.store'), ['product_id' => $product->id, 'quantity' => 1]);

    $coupon = Coupon::factory()->withMinimumOrder(100)->create(['code' => 'MIN100']);

    $response = $this->actingAs($user)->post(route('frontend.cart.apply-coupon'), ['coupon' => $coupon->code]);
    $response->assertSessionHasErrors('coupon');

    $cart = ShoppingCart::where('user_id', $user->id)->firstOrFail();
    expect($cart->coupon_code)->toBeNull();
});

test('an invalid coupon code is rejected', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 20, 'stock_quantity' => 10]);
    $this->actingAs($user)->post(route('frontend.cart.store'), ['product_id' => $product->id, 'quantity' => 1]);

    $response = $this->actingAs($user)->post(route('frontend.cart.apply-coupon'), ['coupon' => 'DOES-NOT-EXIST']);
    $response->assertSessionHasErrors('coupon');
});

test('removing a coupon clears the discount', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 100, 'stock_quantity' => 10]);
    $this->actingAs($user)->post(route('frontend.cart.store'), ['product_id' => $product->id, 'quantity' => 1]);

    $coupon = Coupon::factory()->percentage(10)->create(['code' => 'REMOVE10']);
    $this->actingAs($user)->post(route('frontend.cart.apply-coupon'), ['coupon' => $coupon->code]);

    $this->actingAs($user)->post(route('frontend.cart.remove-coupon'));

    $cart = ShoppingCart::where('user_id', $user->id)->firstOrFail();
    expect($cart->coupon_code)->toBeNull();
    expect((float) $cart->discount_amount)->toBe(0.0);
});
