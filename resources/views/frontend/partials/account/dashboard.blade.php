@php
    $user = auth()->user();
    $activeTab = $activeTab ?? 'dashboard';
    $stats = $dashboardStats ?? [];

    $cards = [
        [
            'url' => route('frontend.account.orders'),
            'icon' => 'w-icon-orders',
            'label' => 'الطلبات',
            'desc' => 'تتبع حالة مشترياتك',
            'theme' => 'theme-orders',
            'stat' => $stats['orders'] ?? 0,
            'stat_label' => 'طلب',
        ],
        [
            'url' => route('frontend.account.downloads'),
            'icon' => 'w-icon-download',
            'label' => 'التحميلات',
            'desc' => 'ملفات المنتجات الرقمية',
            'theme' => 'theme-downloads',
            'stat' => $stats['downloads'] ?? 0,
            'stat_label' => 'ملف',
        ],
        [
            'url' => route('frontend.account.addresses'),
            'icon' => 'w-icon-map-marker',
            'label' => 'العناوين',
            'desc' => 'عناوين الشحن والفوترة',
            'theme' => 'theme-addresses',
            'stat' => $stats['addresses'] ?? 0,
            'stat_label' => 'عنوان',
        ],
        [
            'url' => route('frontend.account.details'),
            'icon' => 'w-icon-user',
            'label' => 'تفاصيل الحساب',
            'desc' => 'البيانات وكلمة المرور',
            'theme' => 'theme-account',
            'stat' => null,
        ],
        [
            'url' => route('frontend.wishlist'),
            'icon' => 'w-icon-heart',
            'label' => 'المفضلة',
            'desc' => 'منتجاتك المحفوظة',
            'theme' => 'theme-wishlist',
            'stat' => $stats['wishlist'] ?? 0,
            'stat_label' => 'منتج',
        ],
    ];
@endphp

<div class="tab-pane {{ $activeTab === 'dashboard' ? 'active in' : '' }}" id="account-dashboard">
    <div class="account-welcome-banner mb-5">
        <div class="account-welcome-text">
            <span class="account-welcome-badge">لوحة التحكم</span>
            <h3 class="account-welcome-title">
                مرحباً، <span>{{ $user->name }}</span>
            </h3>
            <p class="account-welcome-desc mb-0">
                من هنا يمكنك إدارة طلباتك وعناوينك وبيانات حسابك بسهولة.
            </p>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="account-welcome-action">
            @csrf
            <button type="submit" class="btn btn-outline btn-dark btn-rounded btn-sm">
                <i class="w-icon-logout ml-1"></i> تسجيل الخروج
            </button>
        </form>
    </div>

    <div class="row account-quick-cards gutter-sm">
        @foreach($cards as $index => $card)
            <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                <a href="{{ $card['url'] }}" class="account-quick-card {{ $card['theme'] }}"
                    style="--card-delay: {{ $index * 0.07 }}s">
                    <div class="account-quick-card__glow"></div>
                    <div class="account-quick-card__inner">
                        <div class="account-quick-card__icon">
                            <i class="{{ $card['icon'] }}"></i>
                        </div>
                        <div class="account-quick-card__body">
                            <h4 class="account-quick-card__title">{{ $card['label'] }}</h4>
                            <p class="account-quick-card__desc">{{ $card['desc'] }}</p>
                        </div>
                        @if(isset($card['stat']) && $card['stat'] > 0)
                            <span class="account-quick-card__badge">
                                {{ $card['stat'] }} {{ $card['stat_label'] ?? '' }}
                            </span>
                        @endif
                        <span class="account-quick-card__arrow">
                            <i class="w-icon-long-arrow-left"></i>
                        </span>
                    </div>
                </a>
            </div>
        @endforeach

        <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
            <form method="POST" action="{{ route('logout') }}" class="h-100">
                @csrf
                <button type="submit" class="account-quick-card theme-logout w-100 h-100"
                    style="--card-delay: {{ count($cards) * 0.07 }}s">
                    <div class="account-quick-card__glow"></div>
                    <div class="account-quick-card__inner">
                        <div class="account-quick-card__icon">
                            <i class="w-icon-logout"></i>
                        </div>
                        <div class="account-quick-card__body">
                            <h4 class="account-quick-card__title">تسجيل الخروج</h4>
                            <p class="account-quick-card__desc">إنهاء الجلسة بأمان</p>
                        </div>
                        <span class="account-quick-card__arrow">
                            <i class="w-icon-long-arrow-left"></i>
                        </span>
                    </div>
                </button>
            </form>
        </div>
    </div>
</div>
