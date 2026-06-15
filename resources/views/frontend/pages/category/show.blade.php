@extends('frontend.layouts.master')

@section('title', ($category->meta_title ?: $category->name) . ' - ' . config('app.name'))
@section('body-class', '')
@section('header-class', 'header-border')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/style-rtl.min.css') }}">
@endpush

@section('content')
    <div class="page-content store-shop-page">
        <x-frontend.page-head
            :title="$category->name"
            :subtitle="$category->description
                ? \Illuminate\Support\Str::limit(strip_tags($category->description), 120)
                : 'منتجات مختارة من هذا التصنيف'"
            :breadcrumb-items="[
                ['label' => 'المتجر', 'url' => route('frontend.shop.index')],
                ['label' => $category->name],
            ]"
        />

        <div class="container">
            @if($category->children()->active()->exists())
                @include('frontend.partials.shop.category-slider', [
                    'categories' => $category->children()->active()->ordered()->get(),
                    'sectionTitle' => 'تصنيفات فرعية',
                    'sectionSubtitle' => 'تصفّح الأقسام داخل «' . $category->name . '»',
                ])
            @endif

            @include('frontend.partials.shop.catalog', [
                'filterRoute' => route('frontend.category.show', $category->slug),
                'activeCategory' => $category,
                'layout' => $layout,
                'infiniteScrollUrl' => $infiniteScrollUrl,
            ])
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('frontend/assets/vendor/sticky/sticky.js') }}"></script>
@endpush
