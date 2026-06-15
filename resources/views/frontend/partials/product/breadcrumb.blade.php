@php
    $breadcrumbItems = [
        ['label' => 'المتجر', 'url' => route('frontend.shop.index')],
    ];

    if ($product->category) {
        $breadcrumbItems[] = [
            'label' => $product->category->name,
            'url' => route('frontend.category.show', $product->category->slug),
        ];
    }

    $breadcrumbItems[] = ['label' => $product->name];
@endphp

<x-frontend.page-head
    :title="$product->name"
    :breadcrumb-items="$breadcrumbItems"
    :embedded="true"
/>
