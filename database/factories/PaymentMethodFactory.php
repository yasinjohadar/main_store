<?php

namespace Database\Factories;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentMethod>
 */
class PaymentMethodFactory extends Factory
{
    protected $model = PaymentMethod::class;

    public function definition(): array
    {
        return [
            'name' => 'الدفع عند الاستلام',
            'slug' => 'cod-' . fake()->unique()->numberBetween(1000, 999999),
            'driver' => 'cod',
            'config' => null,
            'is_active' => true,
            'order' => 0,
        ];
    }

    public function cod(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'الدفع عند الاستلام',
            'driver' => 'cod',
        ]);
    }

    public function bankTransfer(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'تحويل بنكي',
            'driver' => 'bank_transfer',
        ]);
    }

    public function stripe(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'بطاقة ائتمان (Stripe)',
            'driver' => 'stripe',
        ]);
    }

    public function paypal(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'PayPal',
            'driver' => 'paypal',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
