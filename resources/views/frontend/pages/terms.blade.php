@extends('frontend.layouts.master')

@section('title', 'الشروط والأحكام - ' . config('app.name'))
@section('body-class', 'store-legal-page store-terms-page')
@section('header-class', 'header-border')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/style-rtl.min.css') }}">
@endpush

@section('content')
    @include('frontend.partials.page.page-header', [
        'title' => 'الشروط والأحكام',
        'eyebrow' => 'معلومات قانونية',
        'subtitle' => 'اطّلع على شروط استخدام المتجر',
        'icon' => 'w-icon-list',
        'breadcrumbItems' => [
            ['label' => 'المتجر', 'url' => route('frontend.shop.index')],
            ['label' => 'الشروط والأحكام'],
        ],
    ])

    @include('frontend.partials.pages.terms-content')
@endsection

@push('scripts')
    <script src="{{ asset('frontend/assets/vendor/sticky/sticky.js') }}"></script>
    @include('frontend.partials.pages.legal-scripts')
@endpush
