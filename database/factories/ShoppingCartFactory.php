<?php

namespace Database\Factories;

use App\Models\ShoppingCart;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShoppingCart>
 */
class ShoppingCartFactory extends Factory
{
    protected $model = ShoppingCart::class;

    public function definition(): array
    {
        return [
            'user_id' => null,
            'session_id' => fake()->unique()->uuid(),
            'coupon_code' => null,
            'discount_amount' => 0,
        ];
    }

    public function forUser($user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id ?? $user,
            'session_id' => null,
        ]);
    }
}
