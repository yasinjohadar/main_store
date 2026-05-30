<section class="related-product-section store-product-section store-product-section--related">
    <div class="store-product-section__head title-link-wrapper mb-4">
        <h4 class="title store-product-section__title">منتجات ذات صلة</h4>
        @if($product->category)
            <a href="{{ route('frontend.category.show', $product->category->slug) }}"
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
            @forelse($relatedProducts ?? [] as $related)
                <x-frontend.product-related-slide :product="$related" />
            @empty
                @for($i = 1; $i <= 4; $i++)
                    <div class="swiper-slide product">
                        <figure class="product-media">
                            <a href="{{ route('frontend.shop.index') }}">
                                <img src="{{ asset('frontend/assets/images/products/default/' . $i . '.jpg') }}" alt="منتج تجريبي" width="300" height="338" />
                            </a>
                        </figure>
                        <div class="product-details">
                            <h4 class="product-name"><a href="{{ route('frontend.shop.index') }}">منتج تجريبي</a></h4>
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
