@props(['product'])

@php
    $ratingPercent = (($product->reviews_avg_rating ?? 0) / 5) * 100;
    $productUrl = route('frontend.product.show', $product->slug);
@endphp

<div class="product product-widget bb-no">
    <figure class="product-media">
        <a href="{{ $productUrl }}">
            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" width="105" height="118" />
        </a>
    </figure>
    <div class="product-details">
        <h4 class="product-name">
            <a href="{{ $productUrl }}">{{ $product->name }}</a>
        </h4>
        <div class="ratings-container">
            <div class="ratings-full">
                <span class="ratings" style="width: {{ $ratingPercent }}%;"></span>
                <span class="tooltiptext tooltip-top"></span>
            </div>
        </div>
        <div class="product-price">
            @if($product->has_discount)
                <ins class="new-price">{{ number_format($product->effective_price, 2) }} ر.س</ins>
                <del class="old-price">{{ number_format($product->compare_at_price, 2) }} ر.س</del>
            @else
                <ins class="new-price">{{ number_format($product->effective_price, 2) }} ر.س</ins>
            @endif
        </div>
    </div>
</div>
