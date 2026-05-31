@php
    $fallbackCategoryImages = [
        'frontend/assets/images/demos/demo-rtl/categories/2-1.jpg',
        'frontend/assets/images/demos/demo-rtl/categories/2-2.jpg',
        'frontend/assets/images/demos/demo-rtl/categories/2-3.jpg',
        'frontend/assets/images/demos/demo-rtl/categories/2-4.jpg',
        'frontend/assets/images/demos/demo-rtl/categories/2-5.jpg',
        'frontend/assets/images/demos/demo-rtl/categories/2-6.jpg',
    ];
    $fallbackNames = ['أزياء', 'أثاث', 'أحذية', 'رياضة', 'ألعاب', 'حواسيب'];
    $sectionTitle = $section->resolvedTitle($sectionDefaults['title'] ?? 'أهم التصنيفات هذا الشهر');
    $sectionSubtitle = $section->resolvedSubtitle($sectionDefaults['subtitle'] ?? 'اكتشف مجموعات مختارة من أفضل التصنيفات');
@endphp

<section class="home-categories-section category-section top-category bg-grey pt-10 pb-10 appear-animate">
    <div class="container pb-2">
        <div class="home-categories-section__head">
            <h2 class="title justify-content-center pt-1 ls-normal mb-2">{{ $sectionTitle }}</h2>
            @if($sectionSubtitle)
                <p class="home-categories-section__subtitle text-center mb-5">{{ $sectionSubtitle }}</p>
            @endif
        </div>
        <div class="swiper">
            <div class="swiper-container swiper-theme shadow-swiper pg-show home-categories-swiper" data-swiper-options="{
                'spaceBetween': 20,
                'slidesPerView': 2,
                'breakpoints': {
                    '576': { 'slidesPerView': 3 },
                    '768': { 'slidesPerView': 4 },
                    '992': { 'slidesPerView': 5 },
                    '1200': { 'slidesPerView': 6 }
                }
            }">
                <div class="swiper-wrapper">
                    @forelse(($categories ?? collect())->take(12) as $index => $category)
                        <x-frontend.category-slide
                            :category="$category"
                            :index="$index"
                            :fallback-image="asset($fallbackCategoryImages[$index % count($fallbackCategoryImages)])"
                        />
                    @empty
                        @foreach($fallbackNames as $index => $name)
                            <div class="swiper-slide">
                                <a href="{{ route('frontend.shop.index') }}" class="home-category-card theme-cat-{{ ($index % 6) + 1 }}"
                                    style="--cat-delay: {{ $index * 0.06 }}s">
                                    <div class="home-category-card__media">
                                        <img src="{{ asset($fallbackCategoryImages[$index]) }}" alt="{{ $name }}" loading="lazy">
                                        <span class="home-category-card__overlay"></span>
                                        <span class="home-category-card__shine"></span>
                                    </div>
                                    <div class="home-category-card__content">
                                        <h4 class="home-category-card__name">{{ $name }}</h4>
                                        <span class="home-category-card__cta">
                                            تسوق الآن<i class="w-icon-long-arrow-left"></i>
                                        </span>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
