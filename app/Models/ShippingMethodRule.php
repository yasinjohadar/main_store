<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethodRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipping_method_id',
        'condition_type',
        'min_value',
        'max_value',
        'cost',
        'per_unit',
        'order',
    ];

    protected $casts = [
        'min_value' => 'decimal:3',
        'max_value' => 'decimal:3',
        'cost' => 'decimal:2',
        'per_unit' => 'decimal:2',
        'order' => 'integer',
    ];

    public function method()
    {
        return $this->belongsTo(ShippingMethod::class, 'shipping_method_id');
    }
}

