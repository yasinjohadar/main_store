@extends('frontend.layouts.master')

@section('title', 'الأسئلة الشائعة - ' . config('app.name'))
@section('body-class', 'store-faq-page')
@section('header-class', 'header-border')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/style-rtl.min.css') }}">
@endpush

@section('content')
    @include('frontend.partials.page.page-header', [
        'title' => 'الأسئلة الشائعة',
        'eyebrow' => 'المساعدة',
        'subtitle' => 'إجابات على أكثر الأسئلة شيوعاً',
        'icon' => 'w-icon-help',
        'breadcrumbItems' => [['label' => 'الأسئلة الشائعة']],
    ])

    @include('frontend.partials.pages.faq-content')
@endsection

@push('scripts')
    <script src="{{ asset('frontend/assets/vendor/sticky/sticky.js') }}"></script>
@endpush
