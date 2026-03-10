<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'color', 'order', 'is_final'];

    protected $casts = [
        'order' => 'integer',
        'is_final' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'order_status_id');
    }

    public function scopeFinal($query)
    {
        return $query->where('is_final', true);
    }
}
