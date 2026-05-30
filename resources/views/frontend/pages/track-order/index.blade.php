@extends('frontend.layouts.master')

@section('title', 'تتبع الطلب - ' . config('app.name'))
@section('body-class', '')
@section('main-class', 'track-order-page')
@section('header-class', 'header-border')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/style-rtl.min.css') }}">
@endpush

@section('content')
    @include('frontend.partials.page.page-header', [
        'title' => 'تتبع الطلب',
        'icon' => 'w-icon-truck',
        'breadcrumbItems' => [
            ['label' => 'المتجر', 'url' => route('frontend.shop.index')],
            ['label' => 'تتبع الطلب'],
        ],
    ])

    <div class="page-content store-track-order-page">
        <div class="container">
            <div class="store-track-order-main__head">
                <div>
                    <h1 class="store-track-order-main__title">تتبع الطلب</h1>
                    <p class="store-track-order-main__meta">اعرف حالة طلبك ومراحل الشحن في أي وقت</p>
                </div>
            </div>

            <div class="store-track-order-layout">
                @include('frontend.partials.track-order.form')

                @if($order)
                    @include('frontend.partials.track-order.result')
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('frontend/assets/vendor/sticky/sticky.js') }}"></script>
@endpush
