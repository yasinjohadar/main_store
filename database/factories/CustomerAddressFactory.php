<?php

namespace Database\Factories;

use App\Models\CustomerAddress;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomerAddress>
 */
class CustomerAddressFactory extends Factory
{
    protected $model = CustomerAddress::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => 'shipping',
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'country' => 'SA',
            'city' => fake()->city(),
            'state' => fake()->state(),
            'postal_code' => fake()->postcode(),
            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => null,
            'is_default' => true,
        ];
    }
}
