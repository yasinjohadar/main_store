<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingZoneLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipping_zone_id',
        'country_code',
        'state',
        'city',
        'postal_code_pattern',
        'type',
    ];

    public function zone()
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }
}

