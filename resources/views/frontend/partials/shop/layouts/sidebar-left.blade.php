@php
    $filterRoute = $filterRoute ?? route('frontend.shop.index');
    $activeCategory = $activeCategory ?? null;
    $sidebarClass = 'store-shop-sidebar';
@endphp

@include('frontend.partials.shop.sidebar', compact('categories', 'brands', 'filterRoute', 'activeCategory', 'sidebarClass'))

@include('frontend.partials.shop.main-content', compact(
    'products',
    'categories',
    'brands',
    'filterRoute',
    'activeCategory',
    'layout',
    'infiniteScrollUrl'
))
