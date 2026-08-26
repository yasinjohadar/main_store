<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderReturn>
 */
class OrderReturnFactory extends Factory
{
    protected $model = OrderReturn::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'requested_by' => User::factory(),
            'status' => OrderReturn::STATUS_PENDING,
            'reason' => fake()->sentence(),
            'admin_note' => null,
            'requested_at' => now(),
            'processed_at' => null,
            'processed_by' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderReturn::STATUS_APPROVED,
            'processed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderReturn::STATUS_REJECTED,
            'processed_at' => now(),
        ]);
    }
}
