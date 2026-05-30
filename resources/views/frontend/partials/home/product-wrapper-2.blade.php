@php
    $category = $sectionCategories->get(1);
    $shopUrl = $category ? route('frontend.category.show', $category->slug) : route('frontend.shop.index');
@endphp

@include('frontend.partials.home.product-wrapper-section', [
    'title' => $category?->name ?? 'الإلكترونيات',
    'products' => $electronicsProducts,
    'bannerImage' => asset('frontend/assets/images/demos/demo-rtl/banners/banner-6.jpg'),
    'bannerBg' => '#252525',
    'bannerSubtitle' => 'مجموعة جديدة',
    'bannerTitle' => 'أفضل الكاميرات<br><span class="font-weight-normal text-capitalize">بدون مرآة</span>',
    'shopUrl' => $shopUrl,
    'wrapperClass' => 'product-wrapper-1 appear-animate mb-8',
])

<div class="banner banner-fashion appear-animate br-sm mb-9"
    style="background-image: url({{ asset('frontend/assets/images/demos/demo-rtl/banners/banner-7.jpg') }}); background-color: #383839;">
    <div class="banner-content align-items-center">
        <div class="content-left d-flex align-items-center mb-3">
            <div class="banner-price-info font-weight-bolder text-secondary text-uppercase lh-1 ls-25">
                <sup class="font-weight-bold">%</sup><sub class="font-weight-bold ls-25">خصم</sub>
                25
            </div>
            <hr class="banner-divider bg-white mt-0 mb-0 ml-8 mr-8">
        </div>
        <div class="content-right d-flex align-items-center flex-1 flex-wrap">
            <div class="banner-info mb-0 mr-auto pl-4 mb-3">
                <h3 class="banner-title text-white font-weight-bolder text-uppercase ls-25">أزياء اليوم</h3>
                <p class="text-white mb-0">
                    استخدم الكود
                    <span class="text-dark bg-white font-weight-bold ls-50 pl-1 pr-1 d-inline-block">SAVE25</span>
                    للحصول على أفضل عرض.
                </p>
            </div>
            <a href="{{ route('frontend.shop.index') }}"
                class="btn btn-white btn-outline btn-rounded btn-icon-left mb-3">
                تسوق الآن<i class="w-icon-long-arrow-left"></i>
            </a>
        </div>
    </div>
</div>
