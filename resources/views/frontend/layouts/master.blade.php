<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <title>@yield('title', $seo->title ?? config('app.name'))</title>
    @include('frontend.layouts.head')
    @stack('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/custom-fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/store-theme-overrides.css') }}">
</head>
<body class="@yield('body-class', 'home')" dir="rtl">
    <div class="page-wrapper">
        <h1 class="d-none">@yield('seo-h1', $seo->h1 ?? ($seoH1 ?? site_setting(\App\Services\SiteSettingsService::KEY_SITE_NAME, config('app.name'))))</h1>

        @include('frontend.layouts.main-header')

        <main class="main @yield('main-class')">
            @yield('content')
        </main>

        @include('frontend.layouts.footer')

        <x-frontend.sticky-footer />
        <x-frontend.scroll-top />
        <x-frontend.mobile-menu />
        <x-frontend.newsletter-popup />
        <x-frontend.quick-view-modal />
        <x-frontend.compare-bar />
    </div>

    @include('frontend.layouts.footer-scripts')
</body>
</html>
