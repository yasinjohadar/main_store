@props(['category', 'fallbackImage' => null, 'index' => 0])

@php
    $url = route('frontend.category.show', $category->slug);
    $count = $category->products_count ?? 0;
    $themes = ['theme-cat-1', 'theme-cat-2', 'theme-cat-3', 'theme-cat-4', 'theme-cat-5', 'theme-cat-6'];
    $theme = $themes[$index % count($themes)];
@endphp

<div class="swiper-slide">
    <a href="{{ $url }}" class="home-category-card {{ $theme }}"
        style="--cat-delay: {{ $index * 0.06 }}s"
        aria-label="{{ $category->name }}">
        <div class="home-category-card__media">
            <img src="{{ $category->image_url ?? $fallbackImage }}" alt="{{ $category->name }}" loading="lazy">
            <span class="home-category-card__overlay"></span>
            <span class="home-category-card__shine"></span>
        </div>
        <div class="home-category-card__content">
            @if($count > 0)
                <span class="home-category-card__count">{{ $count }} {{ $count === 1 ? 'منتج' : 'منتجات' }}</span>
            @endif
            <h4 class="home-category-card__name">{{ $category->name }}</h4>
            <span class="home-category-card__cta">
                تسوق الآن<i class="w-icon-long-arrow-left"></i>
            </span>
        </div>
    </a>
</div>
