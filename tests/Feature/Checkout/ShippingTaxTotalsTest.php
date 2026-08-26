<?php

use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use App\Models\ShippingZoneLocation;
use App\Models\ShoppingCart;
use App\Models\ShoppingCartItem;
use App\Models\TaxClass;
use App\Models\TaxRate;
use App\Services\OrderTotalsService;

function cartFor(Product $product, int $quantity = 1): ShoppingCart
{
    $cart = ShoppingCart::factory()->create();
    ShoppingCartItem::factory()->create([
        'shopping_cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => $quantity,
    ]);

    return $cart->load('items.product', 'items.variant');
}

test('shipping cost comes from the matched zone/method rather than being hardcoded', function () {
    $product = Product::factory()->create(['price' => 100, 'weight' => 1]);
    $cart = cartFor($product);

    $zone = ShippingZone::factory()->create(['is_active' => true]);
    ShippingZoneLocation::factory()->create(['shipping_zone_id' => $zone->id, 'country_code' => 'SA']);
    ShippingMethod::factory()->flatRate(20)->create(['shipping_zone_id' => $zone->id]);

    $totals = app(OrderTotalsService::class)->calculate($cart, ['country' => 'SA', 'city' => 'الرياض']);

    expect($totals->shipping)->toBe(20.0);

    // Changing the configured method cost changes the computed shipping total,
    // proving this isn't a hardcoded value.
    ShippingMethod::query()->update(['base_cost' => 35]);
    $totals2 = app(OrderTotalsService::class)->calculate($cart, ['country' => 'SA', 'city' => 'الرياض']);
    expect($totals2->shipping)->toBe(35.0);
});

test('no shipping zone matches an unconfigured country and shipping is zero', function () {
    $product = Product::factory()->create(['price' => 100]);
    $cart = cartFor($product);

    $zone = ShippingZone::factory()->create(['is_active' => true]);
    ShippingZoneLocation::factory()->create(['shipping_zone_id' => $zone->id, 'country_code' => 'SA']);
    ShippingMethod::factory()->flatRate(20)->create(['shipping_zone_id' => $zone->id]);

    $totals = app(OrderTotalsService::class)->calculate($cart, ['country' => 'US']);

    expect($totals->shipping)->toBe(0.0);
});

test('free shipping method returns zero cost regardless of subtotal', function () {
    $product = Product::factory()->create(['price' => 250]);
    $cart = cartFor($product);

    $zone = ShippingZone::factory()->create(['is_active' => true]);
    ShippingZoneLocation::factory()->create(['shipping_zone_id' => $zone->id, 'country_code' => 'SA']);
    ShippingMethod::factory()->freeShipping()->create(['shipping_zone_id' => $zone->id]);

    $totals = app(OrderTotalsService::class)->calculate($cart, ['country' => 'SA']);

    expect($totals->shipping)->toBe(0.0);
});

test('tax is computed from the product tax class rate and varies with the configured rate', function () {
    $taxClass = TaxClass::factory()->create();
    TaxRate::factory()->create(['tax_class_id' => $taxClass->id, 'rate' => 0.15, 'country_code' => 'SA']);

    $product = Product::factory()->create(['price' => 200, 'tax_class_id' => $taxClass->id]);
    $cart = cartFor($product);

    $totals = app(OrderTotalsService::class)->calculate($cart, ['country' => 'SA']);
    expect($totals->tax)->toBe(30.0); // 200 * 0.15

    TaxRate::query()->update(['rate' => 0.05]);
    $totals2 = app(OrderTotalsService::class)->calculate($cart, ['country' => 'SA']);
    expect($totals2->tax)->toBe(10.0); // 200 * 0.05
});

test('products without a tax class are not taxed', function () {
    $product = Product::factory()->create(['price' => 200, 'tax_class_id' => null]);
    $cart = cartFor($product);

    $totals = app(OrderTotalsService::class)->calculate($cart, ['country' => 'SA']);
    expect($totals->tax)->toBe(0.0);
});

test('checkout total combines subtotal, shipping, tax and discount', function () {
    $taxClass = TaxClass::factory()->create();
    TaxRate::factory()->create(['tax_class_id' => $taxClass->id, 'rate' => 0.15, 'country_code' => 'SA']);

    $product = Product::factory()->create(['price' => 100, 'tax_class_id' => $taxClass->id]);
    $cart = cartFor($product);

    $zone = ShippingZone::factory()->create(['is_active' => true]);
    ShippingZoneLocation::factory()->create(['shipping_zone_id' => $zone->id, 'country_code' => 'SA']);
    ShippingMethod::factory()->flatRate(10)->create(['shipping_zone_id' => $zone->id]);

    $totals = app(OrderTotalsService::class)->calculate($cart, ['country' => 'SA']);

    expect($totals->subtotal)->toBe(100.0);
    expect($totals->shipping)->toBe(10.0);
    expect($totals->tax)->toBe(15.0);
    expect($totals->total)->toBe(125.0);
});
