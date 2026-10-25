<?php

namespace App\Models;

use App\Contracts\Seoable;
use App\Traits\HasSeoFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laravel\Scout\Searchable;

class Product extends Model implements Seoable
{
    use HasFactory, SoftDeletes, HasSeoFields;
    use Searchable {
        queueMakeSearchable as protected scoutQueueMakeSearchable;
        queueRemoveFromSearch as protected scoutQueueRemoveFromSearch;
    }

    protected $fillable = [
        'category_id',
        'brand_id',
        'tax_class_id',
        'is_digital',
        'is_bundle',
        'digital_download_limit',
        'digital_download_expiry_days',
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'price',
        'compare_at_price',
        'wholesale_price',
        'cost',
        'stock_quantity',
        'track_quantity',
        'allow_backorder',
        'weight',
        'dimensions',
        'barcode',
        'status',
        'is_featured',
        'is_visible',
        'allow_reviews',
        'reviews_require_approval',
        'order',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'focus_keyword',
        'focus_keyword_synonyms',
        'og_title',
        'og_description',
        'og_image',
        'robots_meta',
        'schema_type',
        'upsell_product_ids',
        'cross_sell_product_ids',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'cost' => 'decimal:2',
        'stock_quantity' => 'integer',
        'track_quantity' => 'boolean',
        'allow_backorder' => 'boolean',
        'is_digital' => 'boolean',
        'is_bundle' => 'boolean',
        'digital_download_limit' => 'integer',
        'digital_download_expiry_days' => 'integer',
        'is_featured' => 'boolean',
        'is_visible' => 'boolean',
        'allow_reviews' => 'boolean',
        'reviews_require_approval' => 'boolean',
        'order' => 'integer',
        'upsell_product_ids' => 'array',
        'cross_sell_product_ids' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && !$product->isDirty('slug')) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function taxClass()
    {
        return $this->belongsTo(TaxClass::class, 'tax_class_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class)->orderBy('is_default', 'desc');
    }

    public function attributes()
    {
        return $this->belongsToMany(ProductAttribute::class, 'product_product_attribute')->withPivot([])->orderBy('product_attributes.order');
    }

    public function hasPurchasableVariants(): bool
    {
        if ($this->relationLoaded('variants')) {
            return $this->variants->isNotEmpty();
        }

        return $this->variants()->exists();
    }

    /**
     * @return list<array{attribute: ProductAttribute, values: \Illuminate\Support\Collection}>
     */
    public function variantOptions(): array
    {
        return app(\App\Services\ProductVariantOptionService::class)->forProduct($this);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('order')->orderBy('id');
    }

    public function files()
    {
        return $this->hasMany(ProductFile::class)->orderBy('order')->orderBy('id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function tags()
    {
        return $this->belongsToMany(ProductTag::class, 'product_tag');
    }

    public function upsellProducts()
    {
        $ids = array_filter($this->upsell_product_ids ?? []);
        if (empty($ids)) {
            return collect();
        }

        return static::active()->whereIn('id', $ids)->with(['images', 'reviews'])->withAvg('reviews', 'rating')->get();
    }

    public function crossSellProducts()
    {
        $ids = array_filter($this->cross_sell_product_ids ?? []);
        if (empty($ids)) {
            return collect();
        }

        return static::active()->whereIn('id', $ids)->with(['images', 'reviews'])->withAvg('reviews', 'rating')->get();
    }

    public static function crossSellsForCartItems(iterable $productIds, int $limit = 4)
    {
        $ids = collect($productIds)->unique()->filter()->values();
        if ($ids->isEmpty()) {
            return collect();
        }

        $crossSellIds = static::whereIn('id', $ids)
            ->pluck('cross_sell_product_ids')
            ->flatten()
            ->filter(fn ($id) => ! $ids->contains($id))
            ->unique()
            ->take($limit * 2)
            ->values();

        if ($crossSellIds->isEmpty()) {
            return static::active()
                ->whereNotIn('id', $ids)
                ->with(['images', 'reviews'])
                ->withAvg('reviews', 'rating')
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }

        return static::active()
            ->whereIn('id', $crossSellIds)
            ->with(['images', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->limit($limit)
            ->get();
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * The component products this bundle contains (only meaningful when is_bundle is true).
     */
    public function bundleItems()
    {
        return $this->hasMany(BundleItem::class, 'bundle_product_id')->with('itemProduct');
    }

    /**
     * The retail-or-wholesale unit price for a given customer. Falls back to the
     * normal effective_price when the product has no wholesale price set or the
     * buyer isn't a flagged wholesale customer.
     */
    public function priceFor(?User $user): float
    {
        if ($user?->is_wholesale_customer && $this->wholesale_price !== null) {
            return (float) $this->wholesale_price;
        }

        return (float) $this->effective_price;
    }

    /**
     * Percentage saved by buying this bundle vs. its components at their own prices.
     * Returns null if there isn't a meaningful discount (or no items).
     */
    public function getBundleSavingsPercentAttribute(): ?int
    {
        if (! $this->is_bundle) {
            return null;
        }

        $componentsTotal = $this->bundleItems->sum(fn ($bundleItem) => $bundleItem->itemProduct
            ? (float) $bundleItem->itemProduct->effective_price * $bundleItem->quantity
            : 0);

        if ($componentsTotal <= 0 || $this->effective_price >= $componentsTotal) {
            return null;
        }

        return (int) round((1 - ($this->effective_price / $componentsTotal)) * 100);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('is_visible', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Whether new reviews for this product require admin approval before being visible.
     * Uses product override if set, otherwise global system setting.
     */
    public function reviewsRequireApproval(): bool
    {
        if ($this->reviews_require_approval !== null) {
            return (bool) $this->reviews_require_approval;
        }
        return SystemSetting::getValue('reviews_require_approval', '1') == '1';
    }

    public function getDefaultVariantAttribute()
    {
        return $this->variants()->where('is_default', true)->first() ?? $this->variants()->first();
    }

    public function getEffectivePriceAttribute()
    {
        $variant = $this->default_variant;
        if ($variant && $variant->price !== null) {
            return $variant->price;
        }
        return $this->price;
    }

    public function getInStockAttribute()
    {
        if (!$this->track_quantity) {
            return true;
        }
        if ($this->variants()->exists()) {
            return $this->variants()->where('stock_quantity', '>', 0)->exists();
        }
        return $this->stock_quantity > 0;
    }

    public function getPrimaryImageAttribute()
    {
        return $this->images()->where('is_primary', true)->first()
            ?? $this->images()->first();
    }

    public function getPrimaryImageUrlAttribute()
    {
        $img = $this->primary_image;
        if ($img) {
            return Storage::url($img->path);
        }
        $seed = $this->id ?? rand(1, 100);
        return "https://picsum.photos/seed/product{$seed}/400/450";
    }

    public function getDefaultImageUrlAttribute()
    {
        $seed = $this->id ?? rand(1, 100);
        return "https://picsum.photos/seed/product{$seed}/400/450";
    }

    public function getHasDiscountAttribute()
    {
        return $this->compare_at_price && $this->compare_at_price > $this->effective_price;
    }

    public function getDiscountPercentageAttribute()
    {
        if (!$this->has_discount) {
            return 0;
        }
        return (int) round((1 - ($this->effective_price / $this->compare_at_price)) * 100);
    }

    public function getReviewsAvgRatingAttribute()
    {
        if (!$this->relationLoaded('reviews') || $this->reviews->isEmpty()) {
            return 0;
        }
        return $this->reviews->avg('rating');
    }

    public function searchableAs(): string
    {
        return 'products_index';
    }

    /**
     * Search indexing is best-effort: an unreachable search engine must not block saving
     * products (search already falls back to the database in ShopCatalogService).
     */
    public function queueMakeSearchable($models)
    {
        try {
            return $this->scoutQueueMakeSearchable($models);
        } catch (\Throwable $e) {
            Log::warning('Product search indexing failed: ' . $e->getMessage());
        }
    }

    public function queueRemoveFromSearch($models)
    {
        try {
            return $this->scoutQueueRemoveFromSearch($models);
        } catch (\Throwable $e) {
            Log::warning('Product search de-indexing failed: ' . $e->getMessage());
        }
    }

    public function shouldBeSearchable(): bool
    {
        return $this->status === 'active' && $this->is_visible;
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'short_description' => $this->short_description,
            'description' => strip_tags((string) $this->description),
            'category_id' => $this->category_id,
            'category_name' => $this->category?->name,
            'brand_id' => $this->brand_id,
            'brand_name' => $this->brand?->name,
            'price' => (float) $this->price,
            'is_visible' => (bool) $this->is_visible,
            'status' => $this->status,
            'created_at' => $this->created_at?->timestamp,
        ];
    }
}
