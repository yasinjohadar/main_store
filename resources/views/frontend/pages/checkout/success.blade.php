@extends('frontend.layouts.master')

@section('title', 'تم تأكيد الطلب - ' . config('app.name'))
@section('body-class', 'checkout-success-page')
@section('main-class', 'checkout')
@section('header-class', 'header-border')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/style-rtl.min.css') }}">
@endpush

@section('content')
    @include('frontend.partials.page.page-header', [
        'title' => 'اكتمال الطلب',
        'icon' => 'w-icon-check',
    ])

    @include('frontend.partials.checkout.success-breadcrumb')

    <div class="page-content store-checkout-success-page">
        <div class="container">
            @if(session('success'))
                <div class="store-cart-alert store-cart-alert--success store-checkout-success-alert">
                    {{ session('success') }}
                </div>
            @endif

            @include('frontend.partials.checkout.order-confirmation', compact('order'))
        </div>
    </div>
@endsection
