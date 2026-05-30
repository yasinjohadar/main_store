@php
    $activeTab = $activeTab ?? 'dashboard';
    $stats = $accountStats ?? [];

    $items = [
        [
            'key' => 'dashboard',
            'url' => route('frontend.account'),
            'icon' => 'w-icon-home',
            'label' => 'لوحة التحكم',
            'theme' => 'theme-dashboard',
        ],
        [
            'key' => 'orders',
            'url' => route('frontend.account.orders'),
            'icon' => 'w-icon-orders',
            'label' => 'الطلبات',
            'theme' => 'theme-orders',
            'stat' => $stats['orders'] ?? 0,
        ],
        [
            'key' => 'downloads',
            'url' => route('frontend.account.downloads'),
            'icon' => 'w-icon-download',
            'label' => 'التحميلات',
            'theme' => 'theme-downloads',
            'stat' => $stats['downloads'] ?? 0,
        ],
        [
            'key' => 'addresses',
            'url' => route('frontend.account.addresses'),
            'icon' => 'w-icon-map-marker',
            'label' => 'العناوين',
            'theme' => 'theme-addresses',
            'stat' => $stats['addresses'] ?? 0,
        ],
        [
            'key' => 'details',
            'url' => route('frontend.account.details'),
            'icon' => 'w-icon-user',
            'label' => 'تفاصيل الحساب',
            'theme' => 'theme-account',
        ],
        [
            'key' => 'wishlist',
            'url' => route('frontend.wishlist'),
            'icon' => 'w-icon-heart',
            'label' => 'المفضلة',
            'theme' => 'theme-wishlist',
            'stat' => $stats['wishlist'] ?? 0,
        ],
    ];
@endphp

<ul class="account-sidebar-nav nav nav-tabs mb-6" role="tablist">
    @foreach($items as $index => $item)
        <li class="nav-item">
            <a href="{{ $item['url'] }}"
                class="account-sidebar-link {{ $item['theme'] }} {{ $activeTab === $item['key'] ? 'is-active' : '' }}"
                style="--nav-delay: {{ $index * 0.05 }}s">
                <span class="account-sidebar-link__glow"></span>
                <span class="account-sidebar-link__icon">
                    <i class="{{ $item['icon'] }}"></i>
                </span>
                <span class="account-sidebar-link__body">
                    <span class="account-sidebar-link__label">{{ $item['label'] }}</span>
                </span>
                @if(! empty($item['stat']))
                    <span class="account-sidebar-link__badge">{{ $item['stat'] }}</span>
                @endif
                <span class="account-sidebar-link__arrow">
                    <i class="w-icon-long-arrow-left"></i>
                </span>
            </a>
        </li>
    @endforeach

    <li class="nav-item account-sidebar-nav__logout">
        <form method="POST" action="{{ route('logout') }}" class="w-100">
            @csrf
            <button type="submit" class="account-sidebar-link theme-logout w-100"
                style="--nav-delay: {{ count($items) * 0.05 }}s">
                <span class="account-sidebar-link__glow"></span>
                <span class="account-sidebar-link__icon">
                    <i class="w-icon-logout"></i>
                </span>
                <span class="account-sidebar-link__body">
                    <span class="account-sidebar-link__label">تسجيل الخروج</span>
                </span>
                <span class="account-sidebar-link__arrow">
                    <i class="w-icon-long-arrow-left"></i>
                </span>
            </button>
        </form>
    </li>
</ul>
