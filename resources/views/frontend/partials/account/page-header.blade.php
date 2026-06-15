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

<x-frontend.page-head
    :title="$displayTitle"
    subtitle="حسابي"
    :breadcrumb-items="$accountBreadcrumb"
/>
