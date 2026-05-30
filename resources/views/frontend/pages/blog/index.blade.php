@extends('frontend.layouts.master')

@section('title', ($pageTitle ?? 'المدونة') . ' - ' . config('app.name'))
@section('body-class', 'blog-grid-page')
@section('header-class', 'header-border')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/style-rtl.min.css') }}">
@endpush

@section('content')
    @include('frontend.partials.page.page-header', [
        'title' => $pageTitle ?? 'المدونة',
        'eyebrow' => 'المدونة',
        'icon' => 'w-icon-comments',
        'breadcrumbItems' => $breadcrumbItems ?? [['label' => 'المدونة']],
    ])

    <div class="page-content mb-10 pb-2 store-blog-grid-page">
        <div class="container">
            @include('frontend.partials.blog.category-filters')
            @include('frontend.partials.blog.posts-grid')
        </div>
    </div>
@endsection
