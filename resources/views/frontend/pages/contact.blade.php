@extends('frontend.layouts.master')

@section('title', 'اتصل بنا - ' . config('app.name'))
@section('body-class', 'store-contact-page')
@section('header-class', 'header-border')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/style-rtl.min.css') }}">
@endpush

@section('content')
    @include('frontend.partials.page.page-header', [
        'title' => 'اتصل بنا',
        'eyebrow' => 'تواصل معنا',
        'subtitle' => 'نحن هنا لمساعدتك',
        'icon' => 'w-icon-envelop',
        'breadcrumbItems' => [['label' => 'اتصل بنا']],
    ])

    @include('frontend.partials.pages.contact-content')
@endsection

@push('scripts')
    <script src="{{ asset('frontend/assets/vendor/sticky/sticky.js') }}"></script>
@endpush
