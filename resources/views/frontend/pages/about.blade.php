@extends('frontend.layouts.master')

@section('title', 'من نحن - ' . config('app.name'))
@section('body-class', 'store-about-page')
@section('header-class', 'header-border')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/style-rtl.min.css') }}">
@endpush

@section('content')
    @include('frontend.partials.page.page-header', [
        'title' => 'من نحن',
        'eyebrow' => 'عن المتجر',
        'subtitle' => 'تعرّف على قصتنا وقيمنا',
        'icon' => 'w-icon-store',
        'breadcrumbItems' => [['label' => 'من نحن']],
    ])

    @include('frontend.partials.pages.about-content')
@endsection

@push('scripts')
    <script src="{{ asset('frontend/assets/vendor/sticky/sticky.js') }}"></script>
@endpush
