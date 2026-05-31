@extends('frontend.layouts.master')

@section('body-class', 'store-product-page')
@section('header-class', 'header-border')
@section('main-class', 'mb-10 pb-1')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/vendor/magnific-popup/magnific-popup.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/vendor/photoswipe/photoswipe.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/vendor/photoswipe/default-skin/default-skin.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/style-rtl.min.css') }}">
@endpush


@section('content')
    @include('frontend.partials.product.breadcrumb')

    <div class="page-content store-product-page__content">
        <div class="container">
            <div class="store-product-layout-switcher mb-4">
                <span class="store-product-layout-switcher__icon"><i class="w-icon-category"></i></span>
                <span class="store-product-layout-switcher__label">تخطيط الصفحة:</span>
                @foreach(\App\Services\ProductLayoutService::LAYOUTS as $layoutOption)
                    @if($layoutOption !== 'variable' || $product->variants()->exists())
                        <a href="{{ request()->fullUrlWithQuery(['layout' => $layoutOption]) }}"
                            class="store-product-layout-switcher__link{{ ($productLayout ?? 'default') === $layoutOption ? ' is-active' : '' }}">
                            {{ match($layoutOption) {
                                'default' => 'افتراضي',
                                'without-sidebar' => 'بدون شريط',
                                'gallery' => 'معرض',
                                'variable' => 'متغير',
                                default => $layoutOption,
                            } }}
                        </a>
                    @endif
                @endforeach
            </div>

            <div class="store-product-body">
                @include($productLayoutPartial ?? 'frontend.partials.product.layouts.default')
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('frontend/assets/vendor/sticky/sticky.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/photoswipe/photoswipe.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/photoswipe/photoswipe-ui-default.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Wolmart !== 'undefined' && typeof Wolmart.initQtyInput === 'function') {
                jQuery('.product-single .quantity').each(function () {
                    jQuery(this).removeData('quantityInput');
                });
                Wolmart.initQtyInput('.product-single .quantity');
            }
        });
    </script>
@endpush
