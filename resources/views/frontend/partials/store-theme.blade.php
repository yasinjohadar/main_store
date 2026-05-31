@php
    $theme = \App\Support\SiteTheme::variants();
@endphp
<style id="store-theme-vars">
:root {
    --store-primary: {{ $theme['primary'] }};
    --store-primary-rgb: {{ $theme['rgb'] }};
    --store-primary-dark: {{ $theme['dark'] }};
    --store-primary-darker: {{ $theme['darker'] }};
    --store-primary-darkest: {{ $theme['darkest'] }};
    --store-primary-mid: {{ $theme['mid'] }};
    --store-primary-deep: {{ $theme['deep'] }};
    --store-radius: {{ \App\Support\SiteTheme::borderRadius() }}px;
    --card-accent: var(--store-primary);
    --cat-accent: var(--store-primary);
    --hero-accent: var(--store-primary);
}
</style>
