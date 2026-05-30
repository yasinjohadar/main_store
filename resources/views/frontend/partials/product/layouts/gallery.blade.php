<div class="row gutter-lg store-product-layout--gallery">
    <div class="main-content">
        <div class="store-product-hero">
            <div class="product product-single row store-product-gallery-layout">
                @include('frontend.partials.product.gallery-grid')
                @include('frontend.partials.product.info')
            </div>
        </div>

        @include('frontend.partials.product.frequently-bought')
        @include('frontend.partials.product.tabs')
        @include('frontend.partials.product.vendor-products')
        @include('frontend.partials.product.related-products')
    </div>

    @include('frontend.partials.product.sidebar')
</div>
