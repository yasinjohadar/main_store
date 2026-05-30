@extends('frontend.layouts.master')

@section('title', 'طلب #' . $order->order_number . ' - ' . config('app.name'))
@section('body-class', 'my-account')
@section('header-class', 'header-border')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/style-rtl.min.css') }}">
@endpush

@section('content')
    @include('frontend.partials.account.page-header', [
        'title' => 'طلب #' . $order->order_number,
        'breadcrumbItems' => [
            ['label' => 'حسابي', 'url' => route('frontend.account')],
            ['label' => 'الطلبات', 'url' => route('frontend.account.orders')],
            ['label' => '#' . $order->order_number],
        ],
    ])

    <div class="page-content pt-2">
        <div class="container">
            <div class="account-tabs tab-vertical row gutter-lg">
                @include('frontend.partials.account.nav', ['activeTab' => 'orders'])

                <div class="tab-content account-tab-content mb-6">
                    @include('frontend.partials.account.order-detail')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('frontend/assets/vendor/sticky/sticky.js') }}"></script>
@endpush
