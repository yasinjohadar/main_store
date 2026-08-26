<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'brand_id' => null,
            'tax_class_id' => null,
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1000, 999999),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####??')),
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 10, 500),
            'compare_at_price' => null,
            'cost' => null,
            'stock_quantity' => 100,
            'track_quantity' => true,
            'allow_backorder' => false,
            'status' => 'active',
            'is_featured' => false,
            'is_visible' => true,
            'allow_reviews' => true,
            'order' => 0,
        ];
    }

    public function withBrand(): static
    {
        return $this->state(fn (array $attributes) => [
            'brand_id' => Brand::factory(),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => 0,
            'allow_backorder' => false,
        ]);
    }

    public function digital(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_digital' => true,
            'track_quantity' => false,
        ]);
    }
}
