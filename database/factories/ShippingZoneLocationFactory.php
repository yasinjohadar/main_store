<?php

namespace Database\Factories;

use App\Models\ShippingZone;
use App\Models\ShippingZoneLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShippingZoneLocation>
 */
class ShippingZoneLocationFactory extends Factory
{
    protected $model = ShippingZoneLocation::class;

    public function definition(): array
    {
        return [
            'shipping_zone_id' => ShippingZone::factory(),
            'country_code' => 'SA',
            'state' => null,
            'city' => null,
            'postal_code_pattern' => null,
            'type' => 'country',
        ];
    }
}
