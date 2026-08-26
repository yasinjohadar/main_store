<?php

use App\Models\Product;

test('all migrations run on sqlite and a product can be created', function () {
    $product = Product::create([
        'name' => 'Sanity Product',
        'sku' => 'SANITY-1',
        'price' => 9.99,
        'stock_quantity' => 5,
        'status' => 'active',
        'is_visible' => true,
    ]);

    expect($product->exists)->toBeTrue();
    expect(Product::count())->toBe(1);
});
