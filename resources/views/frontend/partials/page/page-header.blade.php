@props([
    'title',
    'subtitle' => null,
    'eyebrow' => null,
    'icon' => null,
    'breadcrumbItems' => [],
    'variant' => 'default',
])

<x-frontend.page-hero
    :title="$title"
    :subtitle="$subtitle"
    :eyebrow="$eyebrow"
    :icon="$icon"
    :breadcrumb-items="$breadcrumbItems"
    :variant="$variant"
/>
