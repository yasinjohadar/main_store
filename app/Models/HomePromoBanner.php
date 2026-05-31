<?php

namespace App\Models;

use App\Support\ResolvesPublicImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HomePromoBanner extends Model
{
    use ResolvesPublicImage;

    protected $fillable = [
        'image',
        'background_color',
        'tone',
        'badge',
        'title',
        'subtitle',
        'price_label',
        'price_text',
        'button_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->resolvePublicImageUrl($this->image);
    }
}
