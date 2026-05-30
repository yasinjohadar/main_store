@php
    $activeTab = $activeTab ?? 'orders';
    $itemCount = fn ($order) => $order->items->sum('quantity');
@endphp

<div class="tab-pane {{ $activeTab === 'orders' ? 'active in' : '' }} mb-4" id="account-orders">
    <div class="account-panel">
        @include('frontend.partials.account.section-header', [
            'icon' => 'w-icon-orders',
            'title' => 'الطلبات',
            'subtitle' => 'تتبع ومراجعة جميع مشترياتك',
            'theme' => 'theme-orders',
        ])

        <div class="account-panel__body">
            @if($orders->isNotEmpty())
                <div class="account-table-wrap">
                    <table class="account-table account-orders-table">
                        <thead>
                            <tr>
                                <th>الطلب</th>
                                <th>التاريخ</th>
                                <th>الحالة</th>
                                <th>الإجمالي</th>
                                <th class="text-center">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                @php $qty = $itemCount($order); @endphp
                                <tr>
                                    <td data-label="الطلب">
                                        <span class="account-order-id">#{{ $order->order_number }}</span>
                                    </td>
                                    <td data-label="التاريخ">
                                        {{ $order->created_at->locale('ar')->translatedFormat('j F Y') }}
                                    </td>
                                    <td data-label="الحالة">
                                        <span class="account-status-badge">{{ $order->status->name ?? 'قيد الانتظار' }}</span>
                                    </td>
                                    <td data-label="الإجمالي">
                                        <span class="account-order-total">{{ number_format($order->total, 2) }} ر.س</span>
                                        <span class="account-order-meta">لـ {{ $qty }} {{ $qty === 1 ? 'منتج' : 'منتجات' }}</span>
                                    </td>
                                    <td data-label="إجراءات" class="text-center">
                                        <a href="{{ route('frontend.account.orders.show', $order) }}"
                                            class="account-btn account-btn--sm account-btn--outline">
                                            عرض<i class="w-icon-long-arrow-left"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($orders->hasPages())
                    <div class="account-pagination">{{ $orders->links() }}</div>
                @endif
            @else
                <div class="account-empty-state">
                    <span class="account-empty-state__icon"><i class="w-icon-orders"></i></span>
                    <h5 class="account-empty-state__title">لا توجد طلبات بعد</h5>
                    <p class="account-empty-state__desc">ابدأ التسوق الآن وستظهر طلباتك هنا.</p>
                    <a href="{{ route('frontend.shop.index') }}" class="account-btn account-btn--primary">
                        تسوق الآن<i class="w-icon-long-arrow-left"></i>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
