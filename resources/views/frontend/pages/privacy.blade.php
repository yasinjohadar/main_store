@extends('frontend.layouts.master')

@section('title', 'سياسة الخصوصية - ' . config('app.name'))
@section('body-class', 'store-legal-page store-privacy-page')
@section('header-class', 'header-border')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/style-rtl.min.css') }}">
@endpush

@section('content')
    @include('frontend.partials.page.page-header', [
        'title' => 'سياسة الخصوصية',
        'eyebrow' => 'معلومات قانونية',
        'subtitle' => 'كيف نتعامل مع بياناتك الشخصية',
        'icon' => 'w-icon-lock',
        'breadcrumbItems' => [
            ['label' => 'المتجر', 'url' => route('frontend.shop.index')],
            ['label' => 'سياسة الخصوصية'],
        ],
    ])

    @include('frontend.partials.pages.privacy-content')
@endsection

@push('scripts')
    <script src="{{ asset('frontend/assets/vendor/sticky/sticky.js') }}"></script>
@endpush
