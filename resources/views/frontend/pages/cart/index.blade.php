@extends('frontend.layouts.master')

@section('title', 'سلة التسوق - ' . config('app.name'))
@section('body-class', '')
@section('main-class', 'cart')
@section('header-class', 'header-border')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/style-rtl.min.css') }}">
@endpush

@section('content')
    @include('frontend.partials.page.page-header', [
        'title' => 'سلة التسوق',
        'icon' => 'w-icon-cart',
    ])

    @include('frontend.partials.cart.breadcrumb')

    <div class="page-content store-cart-page">
        <div class="container">
            <div class="row gutter-lg mb-10 store-cart-layout">
                @include('frontend.partials.cart.items')
                @include('frontend.partials.cart.summary')
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('frontend/assets/vendor/sticky/sticky.js') }}"></script>
@endpush
