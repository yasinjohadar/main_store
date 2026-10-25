<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'فاتورة')</title>
    @php($faviconUrl = site_setting_url(\App\Services\SiteSettingsService::KEY_SITE_FAVICON) ?: asset('frontend/assets/images/icons/favicon.png'))
    <link rel="icon" href="{{ $faviconUrl }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/demo-rtl.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/custom-fonts.css') }}?v={{ filemtime(public_path('frontend/assets/css/custom-fonts.css')) }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/store-theme-overrides.css') }}?v={{ filemtime(public_path('frontend/assets/css/store-theme-overrides.css')) }}">
    @include('frontend.partials.store-theme')
    @stack('styles')
</head>
<body class="store-invoice-page" dir="rtl">
    @yield('content')
    @stack('scripts')
</body>
</html>
