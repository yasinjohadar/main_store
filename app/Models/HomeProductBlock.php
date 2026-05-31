<?php

namespace App\Models;

use App\Support\ResolvesPublicImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeProductBlock extends Model
{
    use ResolvesPublicImage;

    public const KEY_PRODUCT_1 = 'product_1';

    public const KEY_PRODUCT_2 = 'product_2';

    public const KEY_PRODUCT_3 = 'product_3';

    public const KEY_FASHION_BANNER = 'fashion_banner';

    protected $fillable = [
        'block_key',
        'section_title',
        'banner_image',
        'banner_bg_color',
        'banner_subtitle',
        'banner_title',
        'category_id',
        'button_url',
        'wrapper_class',
        'settings',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getBannerImageUrlAttribute(): ?string
    {
        return $this->resolvePublicImageUrl($this->banner_image);
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }

    public function resolvedShopUrl(): string
    {
        if ($this->button_url) {
            return $this->button_url;
        }

        if ($this->category) {
            return route('frontend.category.show', $this->category->slug);
        }

        return route('frontend.shop.index');
    }
}
