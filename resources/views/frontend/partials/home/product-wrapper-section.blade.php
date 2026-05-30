@props([
    'title',
    'products',
    'bannerImage',
    'bannerBg' => '#ebeced',
    'bannerSubtitle' => '',
    'bannerTitle' => '',
    'shopUrl' => null,
    'wrapperClass' => 'product-wrapper-1 appear-animate mb-5',
])

@php
    $shopUrl = $shopUrl ?? route('frontend.shop.index');
@endphp

<div class="{{ $wrapperClass }}">
    <div class="title-link-wrapper pb-1 mb-4">
        <h2 class="title ls-normal mb-0">{{ $title }}</h2>
        <a href="{{ $shopUrl }}" class="btn btn-link btn-icon-left font-size-normal font-weight-bold ls-25 mb-0">
            المزيد من المنتجات<i class="w-icon-long-arrow-left"></i>
        </a>
    </div>
    <div class="row">
        <div class="col-lg-3 col-sm-4 mb-4">
            <div class="banner overlay-light h-100 br-sm"
                style="background-image: url({{ $bannerImage }}); background-color: {{ $bannerBg }};">
                <div class="banner-content content-top">
                    @if($bannerSubtitle)
                        <h5 class="banner-subtitle font-weight-normal mb-2">{{ $bannerSubtitle }}</h5>
                        <hr class="banner-divider bg-dark mb-2">
                    @endif
                    @if($bannerTitle)
                        <h3 class="banner-title font-weight-bolder ls-25 text-uppercase">{!! $bannerTitle !!}</h3>
                    @endif
                    <a href="{{ $shopUrl }}" class="btn btn-dark btn-outline btn-rounded btn-sm">تسوق الآن</a>
                </div>
            </div>
        </div>
        <div class="col-lg-9 col-sm-8">
            @if($products->isNotEmpty())
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
            @else
                <p class="text-center py-6 mb-0">لا توجد منتجات في هذا القسم حالياً.</p>
            @endif
        </div>
    </div>
</div>
