<div class="main-content store-product-layout--no-sidebar">
    <div class="store-product-hero">
        <div class="product product-single row">
            @include('frontend.partials.product.gallery')
            @include('frontend.partials.product.info')
        </div>
    </div>

    @include('frontend.partials.product.frequently-bought')
    @include('frontend.partials.product.tabs')
    @include('frontend.partials.product.vendor-products')
    @include('frontend.partials.product.related-products')
</div>
