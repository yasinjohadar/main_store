@php
    $category = $sectionCategories->get(2);
    $shopUrl = $category ? route('frontend.category.show', $category->slug) : route('frontend.shop.index');
@endphp

@include('frontend.partials.home.product-wrapper-section', [
    'title' => $category?->name ?? 'المنزل والمطبخ',
    'products' => $homeProducts,
    'bannerImage' => asset('frontend/assets/images/demos/demo-rtl/banners/banner-8.jpg'),
    'bannerBg' => '#EAEFF3',
    'bannerSubtitle' => 'عروض وتخفيضات',
    'bannerTitle' => 'الأكثر رواجاً<br><span class="font-weight-normal text-capitalize">أدوات منزلية</span>',
    'shopUrl' => $shopUrl,
    'wrapperClass' => 'product-wrapper-1 product-wrapper-home appear-animate mb-7',
])
