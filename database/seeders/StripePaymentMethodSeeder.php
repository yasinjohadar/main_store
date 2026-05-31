<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class StripePaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        PaymentMethod::query()->firstOrCreate(
            ['slug' => 'stripe-card'],
            [
                'name' => 'بطاقة ائتمان (Stripe)',
                'driver' => 'stripe',
                'is_active' => true,
                'order' => 1,
                'config' => [
                    'use_global_keys' => true,
                ],
            ]
        );
    }
}
