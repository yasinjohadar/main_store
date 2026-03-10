<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipping_zone_id',
        'name',
        'type',
        'is_active',
        'base_cost',
        'min_cart_total',
        'settings',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'base_cost' => 'decimal:2',
        'min_cart_total' => 'decimal:2',
        'settings' => 'array',
        'order' => 'integer',
    ];

    public const TYPE_FLAT_RATE = 'flat_rate';
    public const TYPE_FREE_SHIPPING = 'free_shipping';
    public const TYPE_BY_WEIGHT = 'by_weight';
    public const TYPE_BY_PRICE = 'by_price';

    public function zone()
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }

    public function rules()
    {
        return $this->hasMany(ShippingMethodRule::class)->orderBy('order');
    }
}

