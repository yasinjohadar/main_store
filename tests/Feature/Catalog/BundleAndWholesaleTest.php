<?php

use App\Models\BundleItem;
use App\Models\Product;
use App\Models\ShoppingCart;
use App\Models\ShoppingCartItem;
use App\Models\User;

test('a wholesale customer pays the wholesale price', function () {
    $product = Product::factory()->create(['price' => 100, 'wholesale_price' => 60]);
    $wholesaleCustomer = User::factory()->create(['is_wholesale_customer' => true]);
    $regularCustomer = User::factory()->create(['is_wholesale_customer' => false]);

    expect($product->priceFor($wholesaleCustomer))->toBe(60.0);
    expect($product->priceFor($regularCustomer))->toBe(100.0);
    expect($product->priceFor(null))->toBe(100.0);
});

test('a product without a wholesale price always charges the regular price', function () {
    $product = Product::factory()->create(['price' => 100, 'wholesale_price' => null]);
    $wholesaleCustomer = User::factory()->create(['is_wholesale_customer' => true]);

    expect($product->priceFor($wholesaleCustomer))->toBe(100.0);
});

test('cart line totals use the wholesale price for a wholesale customer', function () {
    $product = Product::factory()->create(['price' => 100, 'wholesale_price' => 60]);
    $wholesaleCustomer = User::factory()->create(['is_wholesale_customer' => true]);

    $cart = ShoppingCart::factory()->forUser($wholesaleCustomer)->create();
    $item = ShoppingCartItem::factory()->create([
        'shopping_cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 3,
    ]);

    expect((float) $item->unit_price)->toBe(60.0);
    expect((float) $item->line_total)->toBe(180.0);
});

test('bundle savings percent reflects the discount vs buying components separately', function () {
    $componentA = Product::factory()->create(['price' => 50]);
    $componentB = Product::factory()->create(['price' => 30]);
    $bundle = Product::factory()->create(['price' => 64, 'is_bundle' => true]);

    BundleItem::create(['bundle_product_id' => $bundle->id, 'item_product_id' => $componentA->id, 'quantity' => 1]);
    BundleItem::create(['bundle_product_id' => $bundle->id, 'item_product_id' => $componentB->id, 'quantity' => 1]);

    $bundle->load('bundleItems.itemProduct');

    // Components total 80, bundle costs 64 -> 20% savings.
    expect($bundle->bundle_savings_percent)->toBe(20);
});

test('admin can add and remove products from a bundle', function () {
    $admin = User::factory()->create();
    $bundle = Product::factory()->create(['is_bundle' => true]);
    $component = Product::factory()->create();

    $this->actingAs($admin)->post(route('admin.products.bundle-items.store', $bundle), [
        'item_product_id' => $component->id,
        'quantity' => 2,
    ])->assertSessionHasNoErrors();

    $bundleItem = BundleItem::where('bundle_product_id', $bundle->id)->firstOrFail();
    expect($bundleItem->quantity)->toBe(2);

    $this->actingAs($admin)->delete(route('admin.products.bundle-items.destroy', [$bundle, $bundleItem]));

    expect(BundleItem::find($bundleItem->id))->toBeNull();
});

test('the product page renders bundle contents and the savings badge', function () {
    $componentA = Product::factory()->create(['price' => 50, 'name' => 'مكوّن أول']);
    $componentB = Product::factory()->create(['price' => 30, 'name' => 'مكوّن ثاني']);
    $bundle = Product::factory()->create(['price' => 64, 'is_bundle' => true, 'status' => 'active', 'is_visible' => true]);
    BundleItem::create(['bundle_product_id' => $bundle->id, 'item_product_id' => $componentA->id, 'quantity' => 1]);
    BundleItem::create(['bundle_product_id' => $bundle->id, 'item_product_id' => $componentB->id, 'quantity' => 1]);

    $response = $this->get(route('frontend.product.show', $bundle->slug));

    $response->assertOk();
    $response->assertSee('مكوّن أول');
    $response->assertSee('مكوّن ثاني');
    $response->assertSee('وفّر 20%');
});

test('the product page shows the wholesale price and badge to a wholesale customer', function () {
    $product = Product::factory()->create(['price' => 100, 'wholesale_price' => 60, 'status' => 'active', 'is_visible' => true]);
    $wholesaleCustomer = User::factory()->create(['is_wholesale_customer' => true]);

    $response = $this->actingAs($wholesaleCustomer)->get(route('frontend.product.show', $product->slug));

    $response->assertOk();
    $response->assertSee('سعر الجملة');
    $response->assertSee('60.00');
});

test('the admin product edit page renders the bundle management section', function () {
    $admin = User::factory()->create();
    $bundle = Product::factory()->create(['is_bundle' => true]);
    $component = Product::factory()->create();
    BundleItem::create(['bundle_product_id' => $bundle->id, 'item_product_id' => $component->id, 'quantity' => 1]);

    $response = $this->actingAs($admin)->get(route('admin.products.edit', $bundle));

    $response->assertOk();
    $response->assertSee('منتجات الحزمة');
    $response->assertSee($component->name);
});

test('admin can toggle a customer wholesale flag', function () {
    $admin = User::factory()->create();
    $customer = User::factory()->create(['is_wholesale_customer' => false]);

    $this->actingAs($admin)->post(route('admin.customers.toggle-wholesale', $customer));
    expect($customer->fresh()->is_wholesale_customer)->toBeTrue();

    $this->actingAs($admin)->post(route('admin.customers.toggle-wholesale', $customer));
    expect($customer->fresh()->is_wholesale_customer)->toBeFalse();
});
