@extends('frontend.layouts.master')

@php
    $tabTitles = [
        'dashboard' => 'حسابي',
        'orders' => 'طلباتي',
        'downloads' => 'التحميلات',
        'addresses' => 'العناوين',
        'details' => 'تفاصيل الحساب',
    ];
    $activeTab = $activeTab ?? 'dashboard';
    $tabLabel = $tabTitles[$activeTab] ?? 'حسابي';
@endphp

@section('title', $tabLabel . ' - ' . config('app.name'))
@section('body-class', 'my-account')
@section('header-class', 'header-border')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/style-rtl.min.css') }}">
@endpush

@section('content')
    @include('frontend.partials.account.page-header')

    <div class="page-content pt-2">
        <div class="container">
            <div class="account-tabs tab-vertical row gutter-lg">
                @include('frontend.partials.account.nav')

                <div class="tab-content account-tab-content mb-6">
                    @if($activeTab === 'dashboard')
                        @include('frontend.partials.account.dashboard')
                    @elseif($activeTab === 'orders')
                        @include('frontend.partials.account.orders')
                    @elseif($activeTab === 'downloads')
                        @include('frontend.partials.account.downloads')
                    @elseif($activeTab === 'addresses')
                        @include('frontend.partials.account.addresses')
                    @elseif($activeTab === 'details')
                        @include('frontend.partials.account.details')
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('frontend/assets/vendor/sticky/sticky.js') }}"></script>
@endpush
