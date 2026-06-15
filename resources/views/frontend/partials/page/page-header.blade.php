@props([
    'title',
    'subtitle' => null,
    'eyebrow' => null,
    'icon' => null,
    'breadcrumbItems' => [],
    'variant' => 'default',
    'embedded' => false,
])

<x-frontend.page-head
    :title="$title"
    :subtitle="$subtitle ?? $eyebrow"
    :breadcrumb-items="$breadcrumbItems"
    :embedded="$embedded"
/>
