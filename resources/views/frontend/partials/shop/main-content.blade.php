@php
    $layout = $layout ?? session('shop_layout', []);
    $filterRoute = $filterRoute ?? route('frontend.shop.index');
    $activeCategory = $activeCategory ?? null;
    $isList = ($layout['view'] ?? 'grid') === 'list';
@endphp

<div class="main-content">
    @include('frontend.partials.shop.toolbar', compact('filterRoute', 'layout', 'activeCategory', 'categories', 'brands'))

    @if($isList)
        @include('frontend.partials.shop.products-list', compact('products', 'layout', 'infiniteScrollUrl'))
    @else
        @include('frontend.partials.shop.products-grid', compact('products', 'layout', 'infiniteScrollUrl'))
    @endif

    @if(($layout['load'] ?? 'pagination') === 'pagination')
        @include('frontend.partials.shop.pagination', compact('products'))
    @else
        <div class="store-shop-infinite-status text-center py-3" data-infinite-status hidden>
            <span class="store-shop-infinite-status__loading">جاري تحميل المزيد...</span>
            <span class="store-shop-infinite-status__done">لا مزيد من المنتجات</span>
            <span class="store-shop-infinite-status__error">تعذّر تحميل المزيد. حاول مرة أخرى.</span>
        </div>
    @endif
</div>
