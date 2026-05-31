@php
    $brandsEyebrow = $section->resolvedEyebrow($sectionDefaults['eyebrow'] ?? 'شركاء النجاح');
    $brandsTitle = $section->resolvedTitle($sectionDefaults['title'] ?? 'عملاؤنا');
@endphp

@if(($homeBrands ?? collect())->isNotEmpty())
<section class="home-brands appear-animate mb-9">
    <div class="home-brands__header">
        @if($brandsEyebrow)
            <span class="home-brands__eyebrow">{{ $brandsEyebrow }}</span>
        @endif
        <h2 class="home-brands__title">{{ $brandsTitle }}</h2>
    </div>

    <div class="swiper home-brands__swiper">
        <div class="swiper-container swiper-theme" data-swiper-options="{
            'spaceBetween': 16,
            'slidesPerView': 2,
            'autoplay': { 'delay': 3500, 'disableOnInteraction': false },
            'breakpoints': {
                '576': { 'slidesPerView': 3, 'spaceBetween': 18 },
                '768': { 'slidesPerView': 4, 'spaceBetween': 20 },
                '992': { 'slidesPerView': 5, 'spaceBetween': 24 }
            }
        }">
            <div class="swiper-wrapper">
                @foreach($homeBrands as $brand)
                    <div class="swiper-slide">
                        <div class="home-brand-card">
                            @if($brand->url)
                                <a href="{{ $brand->url }}">
                                    <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" width="410" height="186" loading="lazy">
                                </a>
                            @else
                                <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" width="410" height="186" loading="lazy">
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
