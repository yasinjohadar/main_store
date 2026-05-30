@extends('frontend.layouts.master')

@section('title', 'المقارنة - ' . config('app.name'))
@section('body-class', '')
@section('main-class', 'compare-page')
@section('header-class', 'header-border')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/style-rtl.min.css') }}">
@endpush

@section('content')
    @include('frontend.partials.page.page-header', [
        'title' => 'المقارنة',
        'icon' => 'w-icon-compare',
        'breadcrumbItems' => [
            ['label' => 'المتجر', 'url' => route('frontend.shop.index')],
            ['label' => 'المقارنة'],
        ],
    ])

    @include('frontend.partials.compare.content')
@endsection

@push('scripts')
    <script src="{{ asset('frontend/assets/vendor/sticky/sticky.js') }}"></script>
@endpush
