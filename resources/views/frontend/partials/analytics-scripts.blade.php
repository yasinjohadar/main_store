@php
    $settings = app(\App\Services\SiteSettingsService::class);
    $ga4Id = $settings->get(\App\Services\SiteSettingsService::KEY_GA4_MEASUREMENT_ID);
    $metaPixelId = $settings->get(\App\Services\SiteSettingsService::KEY_META_PIXEL_ID);
    $gtmId = $settings->get(\App\Services\SiteSettingsService::KEY_GTM_CONTAINER_ID);
@endphp

@if($gtmId)
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{ $gtmId }}');</script>
@endif

@if($ga4Id)
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4Id }}"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{{ $ga4Id }}', {
        send_page_view: true
        @auth
        , user_id: '{{ auth()->id() }}'
        @endauth
    });
</script>
@endif

@if($metaPixelId)
<script>
    !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '{{ $metaPixelId }}');
    fbq('track', 'PageView');
</script>
@endif

<script>
    window.StoreAnalytics = Object.assign(window.StoreAnalytics || {}, {
        ga4Id: @json($ga4Id),
        metaPixelId: @json($metaPixelId),
        beaconUrl: @json(route('frontend.analytics.event')),
        csrfToken: @json(csrf_token()),
        @if(!empty($analyticsProductView))
        productView: @json($analyticsProductView),
        @endif
        @if(!empty($analyticsBeginCheckout))
        beginCheckout: @json($analyticsBeginCheckout),
        @endif
        @if(!empty($analyticsSearch))
        searchTerm: @json($analyticsSearch),
        @endif
        @if(!empty($analyticsItemList))
        itemList: @json($analyticsItemList),
        @endif
        @if(session('analytics_purchase'))
        purchase: @json(session('analytics_purchase')),
        @endif
    });
</script>
<script src="{{ asset('frontend/assets/js/store-analytics.js') }}"></script>
