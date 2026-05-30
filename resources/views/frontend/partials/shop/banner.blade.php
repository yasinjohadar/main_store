@props([
    'title' => 'ساعات ذكية عصرية',
    'subtitle' => 'مجموعة الإكسسوارات',
    'description' => 'اكتشف أحدث المنتجات المختارة بعناية وبأسعار تنافسية',
    'badge' => 'عروض حصرية',
    'image' => null,
    'link' => null,
    'accent' => '#336699',
    'variant' => 'sidebar',
])

@php
    $bannerImage = $image ?? asset('frontend/assets/images/demos/demo-rtl/shop/banner1.jpg');
    $bannerLink = $link ?? route('frontend.shop.index');
    $variant = $variant ?? 'sidebar';
@endphp

@if($variant === 'fullwidth')
    @include('frontend.partials.shop.banners.fullwidth', get_defined_vars())
@elseif($variant === 'boxed')
    @include('frontend.partials.shop.banners.boxed', get_defined_vars())
@else
    @include('frontend.partials.shop.banners.sidebar', get_defined_vars())
@endif
