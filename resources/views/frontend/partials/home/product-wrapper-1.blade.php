@php
    $category = $sectionCategories->get(0);
    $shopUrl = $category ? route('frontend.category.show', $category->slug) : route('frontend.shop.index');
@endphp

@include('frontend.partials.home.product-wrapper-section', [
    'title' => $category?->name ?? 'الملابس والأزياء',
    'products' => $fashionProducts,
    'bannerImage' => asset('frontend/assets/images/demos/demo-rtl/banners/banner-5.jpg'),
    'bannerBg' => '#ebeced',
    'bannerSubtitle' => 'عروض نهاية الأسبوع',
    'bannerTitle' => 'وصل حديثاً<br><span class="font-weight-normal text-capitalize">مجموعة جديدة</span>',
    'shopUrl' => $shopUrl,
])
