@extends('frontend.layouts.master')



@section('title', 'المتجر - ' . config('app.name'))

@section('body-class', '')

@section('header-class', 'header-border')



@push('styles')

    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/style-rtl.min.css') }}">

@endpush



@section('content')

    @include('frontend.partials.page.page-header', [
        'title' => 'المتجر',
        'eyebrow' => 'التسوق',
        'subtitle' => 'تصفح منتجاتنا واختر ما يناسبك',
        'icon' => 'w-icon-category',
        'breadcrumbItems' => [['label' => 'المتجر']],
    ])



    <div class="page-content store-shop-page">

        @if(($layout['banner'] ?? 'sidebar') === 'fullwidth')

            @include('frontend.partials.shop.banner', ['variant' => 'fullwidth'])

        @endif



        <div class="container{{ ($layout['banner'] ?? 'sidebar') === 'fullwidth' ? '-fluid' : '' }}">

            @if(($layout['banner'] ?? 'sidebar') !== 'fullwidth')

                @include('frontend.partials.shop.banner', ['variant' => $layout['banner'] ?? 'sidebar'])

            @endif



            @include('frontend.partials.shop.category-slider')



            @include('frontend.partials.shop.catalog', [

                'filterRoute' => route('frontend.shop.index'),

                'activeCategory' => null,

                'layout' => $layout,

                'infiniteScrollUrl' => $infiniteScrollUrl,

            ])

        </div>

    </div>

@endsection



@push('scripts')

    <script src="{{ asset('frontend/assets/vendor/sticky/sticky.js') }}"></script>

@endpush

