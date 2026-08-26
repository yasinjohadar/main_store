<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 50, 500);

        return [
            'order_number' => 'ORD-' . fake()->unique()->numerify('##########'),
            'user_id' => User::factory(),
            'guest_email' => null,
            'order_status_id' => OrderStatus::where('slug', 'pending')->value('id') ?? OrderStatus::factory(),
            'shipping_method_id' => null,
            'subtotal' => $subtotal,
            'shipping_amount' => 0,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total' => $subtotal,
            'coupon_code' => null,
            'currency' => 'SAR',
            'customer_note' => null,
            'admin_note' => null,
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }

    public function withStatus(string $slug): static
    {
        return $this->state(fn (array $attributes) => [
            'order_status_id' => OrderStatus::where('slug', $slug)->value('id'),
        ]);
    }

    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'guest_email' => fake()->unique()->safeEmail(),
        ]);
    }
}
