@php
    $layout = $layout ?? session('shop_layout', []);
    $filterRoute = $filterRoute ?? route('frontend.shop.index');
    $activeCategory = $activeCategory ?? null;
    $catalogService = app(\App\Services\ShopCatalogService::class);
    $catalogClasses = $catalogService->catalogClasses($layout);
    $layoutPartial = $catalogService->layoutPartial($layout);
@endphp

<div class="{{ $catalogClasses }}">
    @include($layoutPartial, compact(
        'products',
        'categories',
        'brands',
        'filterRoute',
        'activeCategory',
        'layout',
        'infiniteScrollUrl'
    ))
</div>
