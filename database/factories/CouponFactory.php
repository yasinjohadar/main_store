<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('COUPON###')),
            'name' => fake()->words(2, true),
            'description' => fake()->optional()->sentence(),
            'type' => 'percentage',
            'value' => 10,
            'minimum_order_amount' => null,
            'usage_limit' => null,
            'status' => 'active',
            'starts_at' => null,
            'expires_at' => null,
            'applicable_to' => 'entire_store',
        ];
    }

    public function percentage(float $value = 10): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'percentage',
            'value' => $value,
        ]);
    }

    public function fixedAmount(float $value = 20): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'fixed_amount',
            'value' => $value,
        ]);
    }

    public function buyXGetY(float $value = 100): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'buy_x_get_y',
            'value' => $value,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDay(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    public function withMinimumOrder(float $amount): static
    {
        return $this->state(fn (array $attributes) => [
            'minimum_order_amount' => $amount,
        ]);
    }

    public function withUsageLimit(int $limit): static
    {
        return $this->state(fn (array $attributes) => [
            'usage_limit' => $limit,
        ]);
    }
}
