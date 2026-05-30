@props(['category', 'fallbackImage' => null, 'index' => 0])

@php
    $url = route('frontend.category.show', $category->slug);
    $count = $category->products_count ?? 0;
    $themes = ['theme-cat-1', 'theme-cat-2', 'theme-cat-3', 'theme-cat-4', 'theme-cat-5', 'theme-cat-6'];
    $theme = $themes[$index % count($themes)];
@endphp

<div class="swiper-slide">
    <a href="{{ $url }}" class="store-shop-category-card {{ $theme }}"
        style="--cat-delay: {{ $index * 0.05 }}s"
        aria-label="{{ $category->name }}">
        <div class="store-shop-category-card__ring">
            <div class="store-shop-category-card__media">
                <img src="{{ $category->image_url ?? $fallbackImage }}"
                    alt="{{ $category->name }}" width="190" height="190" loading="lazy">
            </div>
            <span class="store-shop-category-card__shine"></span>
            @if($count > 0)
                <span class="store-shop-category-card__badge">{{ $count }}</span>
            @endif
        </div>
        <div class="store-shop-category-card__content">
            <h4 class="store-shop-category-card__name">{{ $category->name }}</h4>
            <span class="store-shop-category-card__cta">
                تصفح<i class="w-icon-long-arrow-left"></i>
            </span>
        </div>
    </a>
</div>
