@extends('frontend.layouts.master')

@section('title', 'المتجر - ' . config('app.name'))
@section('body-class', '')
@section('header-class', 'header-border')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/style-rtl.min.css') }}">
@endpush

@section('content')
    <div class="page-content store-shop-page">
        <x-frontend.page-head
            title="المتجر"
            subtitle="تصفح منتجاتنا واختر ما يناسبك"
            :breadcrumb-items="[['label' => 'المتجر']]"
        />

        <div class="container">
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
