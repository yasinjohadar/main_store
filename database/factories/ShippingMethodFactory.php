<?php

namespace Database\Factories;

use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShippingMethod>
 */
class ShippingMethodFactory extends Factory
{
    protected $model = ShippingMethod::class;

    public function definition(): array
    {
        return [
            'shipping_zone_id' => ShippingZone::factory(),
            'name' => 'Standard Shipping',
            'type' => ShippingMethod::TYPE_FLAT_RATE,
            'is_active' => true,
            'base_cost' => 15,
            'min_cart_total' => null,
            'settings' => null,
            'order' => 0,
        ];
    }

    public function freeShipping(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ShippingMethod::TYPE_FREE_SHIPPING,
            'base_cost' => 0,
        ]);
    }

    public function flatRate(float $cost = 15): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ShippingMethod::TYPE_FLAT_RATE,
            'base_cost' => $cost,
        ]);
    }
}
