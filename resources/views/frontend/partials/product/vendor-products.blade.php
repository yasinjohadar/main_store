@php
    $vendorLabel = $product->brand?->name ?? 'البائع';
@endphp

<section class="vendor-product-section store-product-section store-product-section--vendor">
    <div class="store-product-section__head title-link-wrapper mb-4">
        <h4 class="title text-left store-product-section__title">منتجات أخرى من {{ $vendorLabel }}</h4>
        @if($product->brand)
            <a href="{{ route('frontend.shop.index', ['brand' => $product->brand->slug]) }}"
                class="btn btn-dark btn-link btn-slide-right btn-icon-right">
                المزيد<i class="w-icon-long-arrow-left"></i>
            </a>
        @else
            <a href="{{ route('frontend.shop.index') }}"
                class="btn btn-dark btn-link btn-slide-right btn-icon-right">
                المزيد<i class="w-icon-long-arrow-left"></i>
            </a>
        @endif
    </div>
    <div class="swiper-container swiper-theme" data-swiper-options="{
        'spaceBetween': 20,
        'slidesPerView': 2,
        'breakpoints': {
            '576': { 'slidesPerView': 2 },
            '768': { 'slidesPerView': 3 },
            '992': { 'slidesPerView': 4 }
        }
    }">
        <div class="swiper-wrapper row cols-lg-4 cols-md-3 cols-sm-2 cols-2">
            @forelse($vendorProducts ?? [] as $vendorProduct)
                <x-frontend.product-related-slide :product="$vendorProduct" />
            @empty
                @for($i = 1; $i <= 4; $i++)
                    <div class="swiper-slide product">
                        <figure class="product-media">
                            <a href="{{ route('frontend.shop.index') }}">
                                <img src="{{ asset('frontend/assets/images/products/default/' . $i . '.jpg') }}"
                                    alt="منتج تجريبي" width="300" height="338" />
                            </a>
                        </figure>
                        <div class="product-details">
                            <h4 class="product-name">
                                <a href="{{ route('frontend.shop.index') }}">منتج تجريبي {{ $i }}</a>
                            </h4>
                            <div class="product-pa-wrapper">
                                <div class="product-price text-muted">— ر.س</div>
                            </div>
                        </div>
                    </div>
                @endfor
            @endforelse
        </div>
    </div>
</section>
