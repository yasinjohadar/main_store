<section class="related-product-section store-product-section store-product-section--cross-sells mt-8">
    <div class="store-product-section__head title-link-wrapper mb-4">
        <h4 class="title store-product-section__title">قد يعجبك أيضاً</h4>
    </div>
    <div class="row cols-lg-4 cols-md-3 cols-sm-2 cols-2">
        @foreach($crossSellProducts as $product)
            <div class="col mb-4">
                <x-frontend.product-card :product="$product" />
            </div>
        @endforeach
    </div>
</section>
