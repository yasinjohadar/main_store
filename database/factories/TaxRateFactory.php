<?php

namespace Database\Factories;

use App\Models\TaxClass;
use App\Models\TaxRate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaxRate>
 */
class TaxRateFactory extends Factory
{
    protected $model = TaxRate::class;

    public function definition(): array
    {
        return [
            'tax_class_id' => TaxClass::factory(),
            'name' => 'VAT',
            'rate' => 0.15,
            'country_code' => 'SA',
            'state' => null,
            'city' => null,
            'postal_code_pattern' => null,
            'is_compound' => false,
            'is_inclusive' => false,
            'is_active' => true,
            'order' => 0,
        ];
    }
}
