@php
    $bundleProducts = ($frequentlyBoughtProducts ?? collect())->take(3);
    $bundleTotal = $product->effective_price + $bundleProducts->sum('effective_price');
    $bundleCount = 1 + $bundleProducts->count();
@endphp

<div class="store-product-section store-product-section--bundle">
<div class="frequently-bought-together mt-5">
    <div class="store-product-section__head">
        <h2 class="title title-underline store-product-section__title">يُشترى معاً بشكل متكرر</h2>
    </div>
    <div class="bought-together-products row mt-8 pb-4">
        {{-- المنتج الحالي --}}
        <div class="product product-wrap text-center">
            <figure class="product-media">
                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" width="138" height="138" />
                <div class="product-checkbox">
                    <input type="checkbox" class="custom-checkbox" id="product_check_current" checked disabled>
                    <label></label>
                </div>
            </figure>
            <div class="product-details">
                <h4 class="product-name">
                    <a href="{{ route('frontend.product.show', $product->slug) }}">{{ $product->name }}</a>
                </h4>
                <div class="product-price">{{ number_format($product->effective_price, 2) }} ر.س</div>
            </div>
        </div>

        @forelse($bundleProducts as $index => $bundleProduct)
            <div class="product product-wrap text-center">
                <figure class="product-media">
                    <img src="{{ $bundleProduct->primary_image_url }}" alt="{{ $bundleProduct->name }}" width="138" height="138" />
                    <div class="product-checkbox">
                        <input type="checkbox" class="custom-checkbox" id="product_check{{ $index + 1 }}"
                            name="product_check{{ $index + 1 }}" checked>
                        <label></label>
                    </div>
                </figure>
                <div class="product-details">
                    <h4 class="product-name">
                        <a href="{{ route('frontend.product.show', $bundleProduct->slug) }}">{{ $bundleProduct->name }}</a>
                    </h4>
                    <div class="product-price">{{ number_format($bundleProduct->effective_price, 2) }} ر.س</div>
                </div>
            </div>
        @empty
            @for($i = 1; $i <= 2; $i++)
                <div class="product product-wrap text-center">
                    <figure class="product-media">
                        <img src="{{ asset('frontend/assets/images/products/default/bought-' . $i . '.jpg') }}"
                            alt="منتج تجريبي" width="138" height="138" />
                        <div class="product-checkbox">
                            <input type="checkbox" class="custom-checkbox" id="product_check{{ $i }}" disabled>
                            <label></label>
                        </div>
                    </figure>
                    <div class="product-details">
                        <h4 class="product-name">
                            <a href="#">منتج تجريبي {{ $i }}</a>
                        </h4>
                        <div class="product-price text-muted">— ر.س</div>
                    </div>
                </div>
            @endfor
        @endforelse

        <div class="product-button">
            <div class="bought-price font-weight-bolder text-primary ls-50">
                {{ number_format($bundleTotal, 2) }} ر.س
            </div>
            <div class="bought-count">لـ {{ $bundleCount }} {{ $bundleCount === 1 ? 'منتج' : 'منتجات' }}</div>
            <a href="{{ route('frontend.cart.index') }}" class="btn btn-dark btn-rounded">أضف الكل للسلة</a>
            @if($bundleProducts->isEmpty())
                <p class="text-muted small mt-2 mb-0">* اقتراحات تجريبية — سيتم الربط لاحقاً</p>
            @endif
        </div>
    </div>
</div>
</div>
