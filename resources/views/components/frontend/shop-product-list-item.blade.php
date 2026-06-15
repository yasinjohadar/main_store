@props(['product'])

@php
    $ratingPercent = (($product->reviews_avg_rating ?? 0) / 5) * 100;
    $reviewCount = $product->relationLoaded('reviews') ? $product->reviews->count() : 0;
    $productUrl = route('frontend.product.show', $product->slug);
    $description = $product->short_description
        ?: \Illuminate\Support\Str::limit(strip_tags($product->description ?? ''), 140);
@endphp

<article class="product product-list store-shop-list-item">
    <div class="store-shop-list-item__card">
        <figure class="product-media store-shop-list-item__media">
            <a href="{{ $productUrl }}" class="store-shop-list-item__image-link">
                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" width="152" height="152" loading="lazy">
            </a>
            @if($product->has_discount)
                <span class="store-shop-list-item__badge">خصم {{ $product->discount_percentage }}%</span>
            @endif
            <div class="product-action-vertical store-shop-list-item__quick">
                <a href="{{ route('frontend.product.quick-view', $product->slug) }}"
                    class="btn-product-icon btn-quickview w-icon-search" title="معاينة سريعة"></a>
            </div>
        </figure>

        <div class="product-details store-shop-list-item__body">
            <div class="store-shop-list-item__head">
                @if($product->category)
                    <a href="{{ route('frontend.category.show', $product->category->slug) }}" class="store-shop-list-item__cat">
                        {{ $product->category->name }}
                    </a>
                @endif
                <h3 class="product-name store-shop-list-item__name">
                    <a href="{{ $productUrl }}">{{ $product->name }}</a>
                </h3>
            </div>

            <div class="store-shop-list-item__meta">
                <div class="ratings-container store-shop-list-item__rating">
                    <div class="ratings-full">
                        <span class="ratings" style="width: {{ $ratingPercent }}%;"></span>
                        <span class="tooltiptext tooltip-top"></span>
                    </div>
                    <a href="{{ $productUrl }}" class="rating-reviews">
                        ({{ $reviewCount }} {{ $reviewCount === 1 ? 'تقييم' : 'تقييمات' }})
                    </a>
                </div>
                <div class="product-price store-shop-list-item__price">
                    @if($product->has_discount)
                        <ins class="new-price">{{ number_format($product->effective_price, 2) }} ر.س</ins>
                        <del class="old-price">{{ number_format($product->compare_at_price, 2) }} ر.س</del>
                    @else
                        <ins class="new-price">{{ number_format($product->effective_price, 2) }} ر.س</ins>
                    @endif
                </div>
            </div>

            @if($description)
                <p class="store-shop-list-item__desc">{{ $description }}</p>
            @endif

            <div class="product-action store-shop-list-item__actions">
                <form method="POST" action="{{ route('frontend.cart.store') }}" class="cart-add-form store-shop-list-item__cart-form"
                    data-product-name="{{ $product->name }}"
                    data-product-url="{{ $productUrl }}"
                    data-product-image="{{ $product->primary_image_url }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn-product btn-add-cart btn-cart store-shop-list-item__cart-btn" title="أضف للسلة">
                        <i class="w-icon-cart"></i>
                        <span>أضف للسلة</span>
                    </button>
                </form>
                <div class="store-shop-list-item__icon-actions">
                    <x-frontend.wishlist-button :product-id="$product->id" />
                    <x-frontend.compare-button :product-id="$product->id" />
                </div>
            </div>
        </div>
    </div>
</article>
