@props(['product'])

@php
    $ratingPercent = (($product->reviews_avg_rating ?? 0) / 5) * 100;
    $reviewCount = $product->relationLoaded('reviews') ? $product->reviews->count() : 0;
    $productUrl = route('frontend.product.show', $product->slug);
    $description = $product->short_description
        ?: \Illuminate\Support\Str::limit(strip_tags($product->description ?? ''), 160);
@endphp

<div class="product product-list store-shop-list-item">
    <figure class="product-media store-shop-list-item__media">
        <a href="{{ $productUrl }}">
            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" width="330" height="338" loading="lazy">
        </a>
        <div class="product-action-vertical">
            <a href="{{ route('frontend.product.quick-view', $product->slug) }}"
                class="btn-product-icon btn-quickview w-icon-search" title="معاينة سريعة"></a>
        </div>
    </figure>

    <div class="product-details store-shop-list-item__body">
        @if($product->category)
            <div class="product-cat">
                <a href="{{ route('frontend.category.show', $product->category->slug) }}">{{ $product->category->name }}</a>
            </div>
        @endif

        <h4 class="product-name">
            <a href="{{ $productUrl }}">{{ $product->name }}</a>
        </h4>

        <div class="ratings-container">
            <div class="ratings-full">
                <span class="ratings" style="width: {{ $ratingPercent }}%;"></span>
                <span class="tooltiptext tooltip-top"></span>
            </div>
            <a href="{{ $productUrl }}" class="rating-reviews">
                ({{ $reviewCount }} {{ $reviewCount === 1 ? 'تقييم' : 'تقييمات' }})
            </a>
        </div>

        <div class="product-price">
            @if($product->has_discount)
                <ins class="new-price">{{ number_format($product->effective_price, 2) }} ر.س</ins>
                <del class="old-price">{{ number_format($product->compare_at_price, 2) }} ر.س</del>
            @else
                <ins class="new-price">{{ number_format($product->effective_price, 2) }} ر.س</ins>
            @endif
        </div>

        @if($description)
            <p class="store-shop-list-item__desc">{{ $description }}</p>
        @endif

        <div class="product-action store-shop-list-item__actions">
            <form method="POST" action="{{ route('frontend.cart.store') }}" class="cart-add-form d-inline"
                data-product-name="{{ $product->name }}"
                data-product-url="{{ $productUrl }}"
                data-product-image="{{ $product->primary_image_url }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn-product btn-add-cart btn-cart" title="أضف للسلة">
                    <i class="w-icon-cart"></i>
                    <span>أضف للسلة</span>
                </button>
            </form>
            <x-frontend.wishlist-button :product-id="$product->id" />
            <x-frontend.compare-button :product-id="$product->id" />
        </div>
    </div>
</div>
