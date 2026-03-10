<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'compare_at_price',
        'cost',
        'stock_quantity',
        'weight',
        'barcode',
        'is_default',
        'image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'cost' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_default' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'product_variant_id');
    }

    public function attributeValues()
    {
        return $this->belongsToMany(
            ProductAttributeValue::class,
            'product_variant_attribute_values',
            'product_variant_id',
            'product_attribute_value_id'
        )->with('attribute');
    }

    public function getEffectivePriceAttribute()
    {
        return $this->price ?? $this->product->price;
    }

    public function getInStockAttribute()
    {
        return $this->stock_quantity > 0;
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : $this->product->primary_image_url;
    }

    public function getDisplayNameAttribute()
    {
        $parts = $this->attributeValues->map(fn ($v) => $v->value)->toArray();
        return implode(' / ', $parts) ?: $this->product->name;
    }
}
