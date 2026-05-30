@extends('frontend.layouts.master')

@section('title', ($category->meta_title ?: $category->name) . ' - ' . config('app.name'))
@section('body-class', '')
@section('header-class', 'header-border')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/style-rtl.min.css') }}">
@endpush

@section('content')
    @include('frontend.partials.page.page-header', [
        'title' => $category->name,
        'eyebrow' => 'التصنيف',
        'icon' => 'w-icon-category',
        'breadcrumbItems' => [
            ['label' => 'المتجر', 'url' => route('frontend.shop.index')],
            ['label' => $category->name],
        ],
    ])

    <div class="page-content store-shop-page">
        @if(($layout['banner'] ?? 'sidebar') === 'fullwidth')
            @include('frontend.partials.shop.banner', [
                'variant' => 'fullwidth',
                'title' => $category->name,
                'subtitle' => 'تصفح المجموعة',
                'description' => $category->description
                    ? \Illuminate\Support\Str::limit(strip_tags($category->description), 80)
                    : 'منتجات مختارة من هذا التصنيف',
                'badge' => $category->products_count . ' منتج',
                'image' => $category->cover_image_url ?? $category->image_url,
                'link' => route('frontend.category.show', $category->slug),
            ])
        @endif

        <div class="container{{ ($layout['banner'] ?? 'sidebar') === 'fullwidth' ? '-fluid' : '' }}">
            @if(($layout['banner'] ?? 'sidebar') !== 'fullwidth')
                @include('frontend.partials.shop.banner', [
                    'variant' => $layout['banner'] ?? 'sidebar',
                    'title' => $category->name,
                    'subtitle' => 'تصفح المجموعة',
                    'description' => $category->description
                        ? \Illuminate\Support\Str::limit(strip_tags($category->description), 80)
                        : 'منتجات مختارة من هذا التصنيف',
                    'badge' => $category->products_count . ' منتج',
                    'image' => $category->cover_image_url ?? $category->image_url,
                    'link' => route('frontend.category.show', $category->slug),
                ])
            @endif

            @include('frontend.partials.shop.category-slider')

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
