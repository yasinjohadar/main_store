@php
    $titles = [
        'dashboard' => 'حسابي',
        'orders' => 'طلباتي',
        'downloads' => 'التحميلات',
        'addresses' => 'العناوين',
        'details' => 'تفاصيل الحساب',
    ];
    $activeTab = $activeTab ?? 'dashboard';
    $pageTitle = $titles[$activeTab] ?? 'حسابي';
    $displayTitle = $title ?? $pageTitle;
    $accountBreadcrumb = $breadcrumbItems ?? (
        $displayTitle === 'حسابي'
            ? [['label' => 'حسابي']]
            : [
                ['label' => 'حسابي', 'url' => route('frontend.account')],
                ['label' => $displayTitle],
            ]
    );
@endphp

<x-frontend.page-hero
    :title="$displayTitle"
    eyebrow="حسابي"
    icon="w-icon-user"
    :breadcrumb-items="$accountBreadcrumb"
/>
