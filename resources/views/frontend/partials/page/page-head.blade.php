@props([
    'title',
    'subtitle' => null,
    'breadcrumbItems' => [],
    'embedded' => false,
])

<x-frontend.page-head
    :title="$title"
    :subtitle="$subtitle"
    :breadcrumb-items="$breadcrumbItems"
    :embedded="$embedded"
/>
