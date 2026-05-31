@php
    $fallbackImages = [
        'frontend/assets/images/demos/demo-rtl/categories/category-4.jpg',
        'frontend/assets/images/demos/demo-rtl/categories/category-5.jpg',
        'frontend/assets/images/demos/demo-rtl/categories/category-6.jpg',
        'frontend/assets/images/demos/demo-rtl/categories/category-7.jpg',
        'frontend/assets/images/demos/demo-rtl/categories/category-8.jpg',
        'frontend/assets/images/demos/demo-rtl/categories/category-9.jpg',
        'frontend/assets/images/demos/demo-rtl/categories/category-10.jpg',
        'frontend/assets/images/demos/demo-rtl/categories/category-11.jpg',
    ];
    $fallbackNames = ['أزياء', 'أثاث', 'أحذية', 'رياضة', 'ألعاب', 'حواسيب', 'جمال', 'إلكترونيات'];
@endphp

<section class="store-shop-categories appear-animate mb-6">
    <div class="store-shop-categories__head">
        <h2 class="store-shop-categories__title">{{ $sectionTitle ?? 'تصفح التصنيفات' }}</h2>
        <p class="store-shop-categories__subtitle">{{ $sectionSubtitle ?? 'اختر التصنيف المناسب واكتشف منتجاتنا' }}</p>
    </div>

    <div class="swiper-container swiper-theme store-shop-categories__swiper" data-swiper-options="{
        'spaceBetween': 16,
        'slidesPerView': 2,
        'breakpoints': {
            '480': { 'slidesPerView': 3 },
            '576': { 'slidesPerView': 4 },
            '768': { 'slidesPerView': 5 },
            '992': { 'slidesPerView': 6 },
            '1200': { 'slidesPerView': 8, 'spaceBetween': 20 }
        }
    }">
        <div class="swiper-wrapper">
            @forelse($categories ?? [] as $index => $cat)
                <x-frontend.shop-category-card
                    :category="$cat"
                    :index="$index"
                    :fallback-image="asset($fallbackImages[$index % count($fallbackImages)])"
                />
            @empty
                @foreach($fallbackNames as $index => $name)
                    <div class="swiper-slide">
                        <a href="{{ route('frontend.shop.index') }}"
                            class="store-shop-category-card theme-cat-{{ ($index % 6) + 1 }}"
                            style="--cat-delay: {{ $index * 0.05 }}s">
                            <div class="store-shop-category-card__ring">
                                <div class="store-shop-category-card__media">
                                    <img src="{{ asset($fallbackImages[$index % count($fallbackImages)]) }}"
                                        alt="{{ $name }}" width="190" height="190" loading="lazy">
                                </div>
                                <span class="store-shop-category-card__shine"></span>
                            </div>
                            <div class="store-shop-category-card__content">
                                <h4 class="store-shop-category-card__name">{{ $name }}</h4>
                                <span class="store-shop-category-card__cta">
                                    تصفح<i class="w-icon-long-arrow-left"></i>
                                </span>
                            </div>
                        </a>
                    </div>
                @endforeach
            @endforelse
        </div>
    </div>
</section>
