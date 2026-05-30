@include('frontend.partials.shop.main-content', compact(
    'products',
    'categories',
    'brands',
    'filterRoute',
    'activeCategory',
    'layout',
    'infiniteScrollUrl'
))

@php
    $sidebarClass = 'store-shop-sidebar';
@endphp

@include('frontend.partials.shop.sidebar', compact('categories', 'brands', 'filterRoute', 'activeCategory', 'sidebarClass'))
