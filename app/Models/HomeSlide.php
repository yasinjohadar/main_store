<?php

namespace App\Models;

use App\Support\SiteTheme;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HomeSlide extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'badge',
        'background_image',
        'product_image',
        'background_color',
        'accent_color',
        'button_text',
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

    public function getBackgroundImageUrlAttribute(): ?string
    {
        return $this->imageUrl($this->background_image);
    }

    public function getProductImageUrlAttribute(): ?string
    {
        return $this->imageUrl($this->product_image);
    }

    public function getResolvedAccentColorAttribute(): string
    {
        return $this->accent_color ?: SiteTheme::primary();
    }

    public function getResolvedBackgroundColorAttribute(): string
    {
        return $this->background_color ?: '#e8edf3';
    }

    protected function imageUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        if (file_exists(public_path($path))) {
            return asset($path);
        }

        return null;
    }
}
