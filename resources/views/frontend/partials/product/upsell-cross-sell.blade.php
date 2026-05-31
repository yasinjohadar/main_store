@if(isset($upsellProducts) && $upsellProducts->isNotEmpty())
<section class="related-product-section store-product-section store-product-section--upsell mt-6">
    <div class="store-product-section__head mb-4">
        <h4 class="title store-product-section__title">ترقية اختيارك</h4>
    </div>
    <div class="swiper-container swiper-theme" data-swiper-options="{'spaceBetween': 20, 'slidesPerView': 2, 'breakpoints': {'992': {'slidesPerView': 4}}}">
        <div class="swiper-wrapper row cols-lg-4 cols-md-3 cols-sm-2 cols-2">
            @foreach($upsellProducts as $related)
                <x-frontend.product-related-slide :product="$related" />
            @endforeach
        </div>
    </div>
</section>
@endif

@if(isset($crossSellProducts) && $crossSellProducts->isNotEmpty())
<section class="related-product-section store-product-section store-product-section--cross-sell mt-6">
    <div class="store-product-section__head mb-4">
        <h4 class="title store-product-section__title">يُشترى معاً</h4>
    </div>
    <div class="swiper-container swiper-theme" data-swiper-options="{'spaceBetween': 20, 'slidesPerView': 2, 'breakpoints': {'992': {'slidesPerView': 4}}}">
        <div class="swiper-wrapper row cols-lg-4 cols-md-3 cols-sm-2 cols-2">
            @foreach($crossSellProducts as $related)
                <x-frontend.product-related-slide :product="$related" />
            @endforeach
        </div>
    </div>
</section>
@endif
