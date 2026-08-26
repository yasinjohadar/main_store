<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ShoppingCart;
use App\Models\ShoppingCartItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShoppingCartItem>
 */
class ShoppingCartItemFactory extends Factory
{
    protected $model = ShoppingCartItem::class;

    public function definition(): array
    {
        return [
            'shopping_cart_id' => ShoppingCart::factory(),
            'product_id' => Product::factory(),
            'product_variant_id' => null,
            'quantity' => 1,
        ];
    }
}
