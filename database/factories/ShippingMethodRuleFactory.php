<?php

namespace Database\Factories;

use App\Models\ShippingMethod;
use App\Models\ShippingMethodRule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShippingMethodRule>
 */
class ShippingMethodRuleFactory extends Factory
{
    protected $model = ShippingMethodRule::class;

    public function definition(): array
    {
        return [
            'shipping_method_id' => ShippingMethod::factory(),
            'condition_type' => 'subtotal',
            'min_value' => 0,
            'max_value' => null,
            'cost' => 0,
            'per_unit' => null,
            'order' => 0,
        ];
    }
}
