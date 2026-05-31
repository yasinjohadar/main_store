@props(['seo' => null])

@php
    /** @var \App\DTOs\SeoData|null $seo */
    $siteName = site_setting(\App\Services\SiteSettingsService::KEY_SITE_NAME, config('app.name'));
@endphp

@if($seo)
    <meta name="description" content="{{ Str::limit(strip_tags($seo->description ?? ''), 320) }}">
    @if($seo->keywords)
        <meta name="keywords" content="{{ $seo->keywords }}">
    @endif
    <meta name="robots" content="{{ $seo->robots }}">
    <link rel="canonical" href="{{ $seo->canonical ?? url()->current() }}">

    <meta property="og:title" content="{{ $seo->ogTitleResolved() }}">
    @if($seo->ogDescriptionResolved())
        <meta property="og:description" content="{{ Str::limit(strip_tags($seo->ogDescriptionResolved()), 320) }}">
    @endif
    @if($seo->ogImage)
        <meta property="og:image" content="{{ $seo->ogImage }}">
    @endif
    <meta property="og:type" content="{{ $seo->ogType }}">
    <meta property="og:locale" content="{{ $seo->ogLocale }}">
    <meta property="og:url" content="{{ $seo->canonical ?? url()->current() }}">
    <meta property="og:site_name" content="{{ $siteName }}">

    <meta name="twitter:card" content="{{ $seo->twitterCard ?? 'summary_large_image' }}">
    <meta name="twitter:title" content="{{ $seo->twitterTitleResolved() }}">
    @if($seo->twitterDescriptionResolved())
        <meta name="twitter:description" content="{{ Str::limit(strip_tags($seo->twitterDescriptionResolved()), 320) }}">
    @endif
    @if($seo->twitterImage ?? $seo->ogImage)
        <meta name="twitter:image" content="{{ $seo->twitterImage ?? $seo->ogImage }}">
    @endif

    @foreach($seo->schemas as $schema)
        @if(! empty($schema))
            <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
        @endif
    @endforeach
@endif

@php
    $gscVerification = app(\App\Services\SiteSettingsService::class)->get(\App\Services\SiteSettingsService::KEY_GSC_VERIFICATION);
@endphp
@if($gscVerification)
    <meta name="google-site-verification" content="{{ $gscVerification }}">
@endif

@stack('seo-extra')
