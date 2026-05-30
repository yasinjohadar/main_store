@php
    $layout = $layout ?? session('shop_layout', []);
    $gridClasses = app(\App\Services\ShopCatalogService::class)->gridClasses($layout);
    $isInfinite = ($layout['load'] ?? 'pagination') === 'infinite';
    $scrollUrl = $infiniteScrollUrl ?? route('frontend.shop.products', request()->query());
@endphp

<div class="product-wrapper row {{ $gridClasses }}{{ $isInfinite ? ' store-shop-products--infinite' : '' }}"
    @if($isInfinite)
        data-url="{{ $scrollUrl }}"
        data-page="1"
        data-has-more="{{ $products->hasMorePages() ? '1' : '0' }}"
    @endif>
    @include('frontend.partials.shop.products-items-list', compact('products', 'layout'))
</div>

@if($products->isEmpty())
    <div class="col-12 text-center py-5 store-shop-empty">
        <p class="mb-0">لم يتم العثور على منتجات.</p>
    </div>
@endif
