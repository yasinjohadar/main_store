<div class="row gutter-lg">
    <div class="main-content">
        <div class="store-product-hero">
            <div class="product product-single row">
                @include('frontend.partials.product.gallery')
                @include('frontend.partials.product.info')
            </div>
        </div>

        @include('frontend.partials.product.frequently-bought')
        @include('frontend.partials.product.tabs')
        @include('frontend.partials.product.vendor-products')
        @include('frontend.partials.product.upsell-cross-sell')
        @include('frontend.partials.product.related-products')
    </div>

    @include('frontend.partials.product.sidebar')
</div>
