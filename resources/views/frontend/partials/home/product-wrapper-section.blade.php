@props([
    'title',
    'products',
    'bannerImage',
    'bannerBg' => '#ebeced',
    'bannerSubtitle' => '',
    'bannerTitle' => '',
    'shopUrl' => null,
    'wrapperClass' => 'product-wrapper-1 appear-animate mb-5',
    // grid: side banner + two-row grid · band: tinted full-width strip, banner on the opposite side, one row · wide: wide banner above one row
    'variant' => 'grid',
])

@php
    $shopUrl = $shopUrl ?? route('frontend.shop.index');
    $singleRow = in_array($variant, ['band', 'wide'], true);
    $navId = 'home-pw-' . substr(md5($title . $variant), 0, 8);
    $slidesPerView = $variant === 'wide' ? 5 : 4;
@endphp

<div class="{{ $wrapperClass }} home-products home-products--{{ $variant }}">
    <div class="home-products__head">
        <div class="home-products__heading">
            <span class="home-products__accent" aria-hidden="true"></span>
            <h2 class="home-products__title">{{ $title }}</h2>
        </div>
        <div class="home-products__tools">
            @if($singleRow && $products->count() > $slidesPerView - 1)
                <div class="home-products__nav">
                    <button type="button" class="home-products__nav-btn {{ $navId }}-prev" aria-label="السابق">
                        <i class="w-icon-long-arrow-right"></i>
                    </button>
                    <button type="button" class="home-products__nav-btn {{ $navId }}-next" aria-label="التالي">
                        <i class="w-icon-long-arrow-left"></i>
                    </button>
                </div>
            @endif
            <a href="{{ $shopUrl }}" class="home-products__more">
                عرض الكل<i class="w-icon-long-arrow-left"></i>
            </a>
        </div>
    </div>

    <div class="row home-products__body">
        <div class="{{ $variant === 'wide' ? 'col-12' : 'col-lg-3 col-sm-4' }} mb-4 home-products__banner-col">
            <a href="{{ $shopUrl }}" class="home-product-side-banner h-100"
                style="--banner-bg: {{ $bannerBg }};"
                aria-label="{{ strip_tags($bannerTitle ?: $title) }}">
                <span class="home-product-side-banner__media" aria-hidden="true">
                    <img src="{{ $bannerImage }}" alt="" loading="lazy">
                </span>
                <span class="home-product-side-banner__content">
                    @if($bannerSubtitle)
                        <span class="home-product-side-banner__subtitle">{{ $bannerSubtitle }}</span>
                        <span class="home-product-side-banner__divider" aria-hidden="true"></span>
                    @endif
                    @if($bannerTitle)
                        <span class="home-product-side-banner__title">{!! $bannerTitle !!}</span>
                    @endif
                    <span class="home-product-side-banner__cta">
                        تسوق الآن
                        <i class="w-icon-long-arrow-left"></i>
                    </span>
                </span>
                <span class="home-product-side-banner__shine" aria-hidden="true"></span>
            </a>
        </div>
        <div class="{{ $variant === 'wide' ? 'col-12' : 'col-lg-9 col-sm-8' }} home-products__list-col">
            @if($products->isNotEmpty())
                @if($singleRow)
                    <div class="swiper-container swiper-theme" data-swiper-options="{
                        'spaceBetween': 20,
                        'slidesPerView': 2,
                        'navigation': {
                            'nextEl': '.{{ $navId }}-next',
                            'prevEl': '.{{ $navId }}-prev'
                        },
                        'breakpoints': {
                            '768': { 'slidesPerView': 3 },
                            '1200': { 'slidesPerView': {{ $slidesPerView }} }
                        }
                    }">
                        <div class="swiper-wrapper row cols-xl-{{ $slidesPerView }} cols-md-3 cols-2">
                            @foreach($products as $product)
                                <div class="swiper-slide product-col">
                                    <x-frontend.product-card :product="$product" :showSecondImage="true" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="swiper-container swiper-theme" data-swiper-options="{
                        'spaceBetween': 20,
                        'slidesPerView': 2,
                        'breakpoints': {
                            '992': { 'slidesPerView': 3 },
                            '1200': { 'slidesPerView': 4 }
                        }
                    }">
                        <div class="swiper-wrapper row cols-xl-4 cols-lg-3 cols-2">
                            @foreach($products->chunk(2) as $pair)
                                <div class="swiper-slide product-col">
                                    @foreach($pair as $product)
                                        <x-frontend.product-card :product="$product" :showSecondImage="true" />
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @else
                <p class="text-center py-6 mb-0">لا توجد منتجات في هذا القسم حالياً.</p>
            @endif
        </div>
    </div>
</div>
