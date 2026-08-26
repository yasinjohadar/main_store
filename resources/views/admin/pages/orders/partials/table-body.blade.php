<div class="table-responsive">
    <table class="admin-data-table">
        <thead>
            <tr>
                <th scope="col" style="width: 48px;">#</th>
                <th scope="col">رقم الطلب</th>
                <th scope="col">العميل</th>
                <th scope="col" style="width: 140px;">الحالة</th>
                <th scope="col">المجموع</th>
                <th scope="col">التاريخ</th>
                <th scope="col" style="width: 60px;">العمليات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                @php
                    $customerName = $order->user?->name ?? 'ضيف';
                    $customerInitial = mb_strtoupper(mb_substr($customerName, 0, 1));
                    $updateUrl = route('admin.orders.inline-update-status', $order);
                @endphp
                <tr data-order-row="{{ $order->id }}">
                    <th scope="row" class="row-number">{{ $orders->firstItem() + $loop->index }}</th>
                    <td>
                        <a href="{{ route('admin.orders.show', $order) }}" class="admin-user-link">
                            {{ $order->order_number }}
                        </a>
                        @if($order->items_count ?? $order->items->count())
                            <div class="text-muted small">
                                {{ $order->items->sum('quantity') }} قطعة
                            </div>
                        @endif
                    </td>
                    <td>
                        <div class="admin-user-cell">
                            <span class="admin-avatar-initial">{{ $customerInitial }}</span>
                            <div class="min-w-0">
                                <span class="d-block text-truncate">{{ $customerName }}</span>
                                @if($order->user?->email ?? $order->guest_email)
                                    <span class="text-muted small d-block text-truncate">
                                        {{ $order->user?->email ?? $order->guest_email }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="admin-badge admin-table__editable admin-table__editable--order-status"
                              role="button"
                              tabindex="0"
                              data-update-url="{{ $updateUrl }}"
                              data-original="{{ $order->order_status_id }}"
                              data-status-name="{{ $order->status->name }}"
                              data-status-color="{{ $order->status->color ?? '#6c757d' }}"
                              style="background-color: {{ $order->status->color ?? '#6c757d' }}; color: #fff;"
                              title="انقر لتغيير الحالة">
                            {{ $order->status->name }}
                        </span>
                    </td>
                    <td class="fw-bold">{{ $currencyService->format((float) $order->total) }}</td>
                    <td>
                        <span class="text-muted">{{ $order->created_at->format('Y-m-d') }}</span>
                        <span class="text-muted small d-block">{{ $order->created_at->format('H:i') }}</span>
                    </td>
                    <td>
                        <a class="admin-action-btn admin-action-view"
                           href="{{ route('admin.orders.show', $order) }}"
                           title="عرض الطلب">
                            <i class="las la-eye"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <div class="admin-empty-state">
                            <i class="las la-shopping-bag"></i>
                            <p class="mb-0 fw-medium">لا توجد طلبات</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-table-footer">
    {{ $orders->withQueryString()->links() }}
</div>
