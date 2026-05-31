@php
    $activeTab = $activeTab ?? 'downloads';
@endphp

<div class="tab-pane {{ $activeTab === 'downloads' ? 'active in' : '' }}" id="account-downloads">
    <div class="account-panel">
        @include('frontend.partials.account.section-header', [
            'icon' => 'w-icon-download',
            'title' => 'التحميلات',
            'subtitle' => 'ملفات المنتجات الرقمية المرتبطة بطلباتك',
            'theme' => 'theme-downloads',
        ])

        <div class="account-panel__body">
            @if($downloads->isNotEmpty())
                <div class="account-list">
                    @foreach($downloads as $download)
                        <div class="account-list-item theme-downloads">
                            <div class="account-list-item__icon">
                                <i class="w-icon-download"></i>
                            </div>
                            <div class="account-list-item__body">
                                <h5 class="account-list-item__title">{{ $download->item->product_name ?? 'منتج' }}</h5>
                                <p class="account-list-item__meta mb-0">طلب #{{ $download->order->order_number }}</p>
                            </div>
                            <div class="account-list-item__action">
                                @if($download->canDownload())
                                    <a href="{{ route('frontend.downloads.show', $download->download_token) }}" class="account-btn account-btn--sm account-btn--outline">تحميل</a>
                                @else
                                    <span class="account-status-badge account-status-badge--muted">غير متاح</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="account-empty-state">
                    <span class="account-empty-state__icon"><i class="w-icon-download"></i></span>
                    <h5 class="account-empty-state__title">لا توجد ملفات للتحميل</h5>
                    <p class="account-empty-state__desc">عند شراء منتجات رقمية ستظهر هنا.</p>
                    <a href="{{ route('frontend.shop.index') }}" class="account-btn account-btn--primary">
                        تسوق الآن<i class="w-icon-long-arrow-left"></i>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
