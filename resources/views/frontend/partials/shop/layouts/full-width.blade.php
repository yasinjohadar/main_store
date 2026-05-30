@include('frontend.partials.shop.main-content', compact(
    'products',
    'categories',
    'brands',
    'filterRoute',
    'activeCategory',
    'layout',
    'infiniteScrollUrl'
))
