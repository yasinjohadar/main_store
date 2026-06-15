@extends('frontend.layouts.master')

@section('title', $post->meta_title ?: ($post->title . ' - ' . config('app.name')))
@section('body-class', 'blog-single-page')
@section('header-class', 'header-border')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/style-rtl.min.css') }}">
@endpush

@section('content')
    <div class="page-content store-blog-show-page">
        <div class="container store-blog-show-page__container">
            <x-frontend.page-head
                :title="$post->title"
                subtitle="المدونة"
                :embedded="true"
                :breadcrumb-items="[
                    ['label' => 'المدونة', 'url' => route('frontend.blog.index')],
                    ['label' => $post->title],
                ]"
            />

            <div class="row store-blog-show-layout">
                <div class="main-content post-single-content store-blog-show-main">
                    @include('frontend.partials.blog.show-content')
                </div>
                @include('frontend.partials.blog.sidebar', ['context' => 'show', 'post' => $post])
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('frontend/assets/vendor/sticky/sticky.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var copyBtn = document.querySelector('.store-blog-show__share-copy');
            if (!copyBtn) return;

            copyBtn.addEventListener('click', function () {
                var url = this.getAttribute('data-copy-url');
                if (navigator.clipboard && url) {
                    navigator.clipboard.writeText(url);
                }
            });
        });
    </script>
@endpush
