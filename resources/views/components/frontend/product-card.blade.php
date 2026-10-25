@props(['product', 'showSecondImage' => false])

@php
    $ratingPercent = (($product->reviews_avg_rating ?? 0) / 5) * 100;
    $reviewCount = $product->relationLoaded('reviews') ? $product->reviews->count() : 0;
    $productUrl = route('frontend.product.show', $product->slug);
    $isNew = $product->created_at && $product->created_at->gt(now()->subDays(14));
    $outOfStock = ! $product->in_stock;
@endphp

<div class="product-wrap store-product-card">
    <div class="product text-center store-product-card__inner">
        <figure class="product-media store-product-card__media">
            <a href="{{ $productUrl }}" class="store-product-card__image-link">
                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" width="300" height="338" loading="lazy" />
                @if($showSecondImage && $product->relationLoaded('images') && $product->images->count() > 1)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($product->images[1]->path) }}"
                        alt="{{ $product->name }}" width="300" height="338" loading="lazy" />
                @endif
            </a>
            <span class="store-product-card__shine"></span>

            <div class="product-action-vertical store-product-card__actions">
                <x-frontend.wishlist-button :product-id="$product->id" />
                <a href="{{ route('frontend.product.quick-view', $product->slug) }}"
                    class="btn-product-icon btn-quickview w-icon-search" title="معاينة سريعة"></a>
                <x-frontend.compare-button :product-id="$product->id" />
            </div>

            @if($product->has_discount || $isNew || $outOfStock)
                <div class="product-label-group store-product-card__labels">
                    @if($outOfStock)
                        <label class="product-label store-product-card__badge store-product-card__badge--out">نفد</label>
                    @endif
                    @if($product->has_discount)
                        <label class="product-label label-discount store-product-card__discount" dir="ltr">
                            -{{ $product->discount_percentage }}%
                        </label>
                    @endif
                    @if($isNew)
                        <label class="product-label store-product-card__badge store-product-card__badge--new">جديد</label>
                    @endif
                </div>
            @endif

            <form method="POST" action="{{ route('frontend.cart.store') }}"
                class="store-product-card__cart-form cart-add-form"
                data-product-name="{{ $product->name }}"
                data-product-url="{{ $productUrl }}"
                data-product-image="{{ $product->primary_image_url }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="store-product-card__cart-btn btn-add-cart" title="أضف للسلة" @disabled($outOfStock)>
                    <i class="w-icon-cart"></i>
                    <span>{{ $outOfStock ? 'غير متوفر' : 'أضف للسلة' }}</span>
                </button>
            </form>
        </figure>

        <div class="product-details store-product-card__details">
            @if($product->category)
                <div class="product-cat store-product-card__cat">
                    <a href="{{ route('frontend.category.show', $product->category->slug) }}">{{ $product->category->name }}</a>
                </div>
            @endif
            <h4 class="product-name store-product-card__name">
                <a href="{{ $productUrl }}">{{ $product->name }}</a>
            </h4>
            <div class="ratings-container store-product-card__rating">
                <div class="ratings-full">
                    <span class="ratings" style="width: {{ $ratingPercent }}%;"></span>
                    <span class="tooltiptext tooltip-top"></span>
                </div>
                @if($reviewCount > 0)
                    <a href="{{ $productUrl }}" class="rating-reviews">({{ $reviewCount }})</a>
                @endif
            </div>
            <div class="product-price store-product-card__price">
                @if($product->has_discount)
                    <ins class="new-price">{{ number_format($product->effective_price, 2) }} ر.س</ins>
                    <del class="old-price">{{ number_format($product->compare_at_price, 2) }} ر.س</del>
                @else
                    <ins class="new-price">{{ number_format($product->effective_price, 2) }} ر.س</ins>
                @endif
            </div>
        </div>
    </div>
</div>
