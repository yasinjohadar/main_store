@extends('frontend.layouts.master')

@section('title', config('app.name') . ' - الرئيسية')

@section('content')
    @include('frontend.partials.home.hero-slider')

    <div class="container">
        @include('frontend.partials.home.icon-boxes')
        @include('frontend.partials.home.category-banners')
        @include('frontend.partials.home.deals-section')
    </div>

    @include('frontend.partials.home.top-categories')

    <div class="container">
        @include('frontend.partials.home.popular-departments')
        @include('frontend.partials.home.product-wrapper-1')
        @include('frontend.partials.home.product-wrapper-2')
        @include('frontend.partials.home.product-wrapper-3')
        @include('frontend.partials.home.brands')
        @include('frontend.partials.home.blog-section')
    </div>
@endsection
