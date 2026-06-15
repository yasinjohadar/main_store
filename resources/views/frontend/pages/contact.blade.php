@extends('frontend.layouts.master')

@section('title', 'اتصل بنا - ' . config('app.name'))
@section('body-class', 'store-contact-page')
@section('header-class', 'header-border')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/style-rtl.min.css') }}">
@endpush

@section('content')
    <x-frontend.page-head
        title="اتصل بنا"
        subtitle="نحن هنا لمساعدتك — تواصل معنا عبر النموذج أو قنوات الدعم أدناه"
        :breadcrumb-items="[['label' => 'اتصل بنا']]"
    />

    @include('frontend.partials.pages.contact-content')
@endsection

@push('scripts')
    <script src="{{ asset('frontend/assets/vendor/sticky/sticky.js') }}"></script>
@endpush
