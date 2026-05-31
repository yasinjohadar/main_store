@extends('admin.layouts.master')

@section('page-title')
    طلب {{ $order->order_number }}
@stop

@section('content')
    @php
        $statusColor = $order->status->color ?? '#6c757d';
        $customerName = $order->user?->name
            ?? $order->shipping_address?->full_name
            ?? 'ضيف';
        $customerEmail = $order->user?->email ?? $order->guest_email;
        $customerInitial = mb_strtoupper(mb_substr($customerName, 0, 1));
        $latestPayment = $order->payments->sortByDesc('created_at')->first();
        $returnedQty = [];
        foreach ($order->returns->where('status', 'approved') as $ret) {
            foreach ($ret->items as $ri) {
                $returnedQty[$ri->order_item_id] = ($returnedQty[$ri->order_item_id] ?? 0) + $ri->quantity;
            }
        }
        $hasReturnable = collect($order->items)->filter(fn ($item) => ($returnedQty[$item->id] ?? 0) < $item->quantity)->isNotEmpty();
        $paymentStatusLabels = [
            'completed' => ['label' => 'مدفوع', 'class' => 'admin-table__badge--success'],
            'pending' => ['label' => 'قيد الانتظار', 'class' => 'admin-table__badge--warning'],
            'failed' => ['label' => 'فشل', 'class' => 'admin-table__badge--danger'],
            'cancelled' => ['label' => 'ملغى', 'class' => 'admin-table__badge--secondary'],
            'refunded' => ['label' => 'مسترد', 'class' => 'admin-table__badge--secondary'],
        ];
        $paymentBadge = $paymentStatusLabels[$latestPayment?->status ?? ''] ?? ['label' => '—', 'class' => 'admin-table__badge--secondary'];
    @endphp

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="admin-detail__header page-header-breadcrumb my-4">
                <div>
                    <h5 class="admin-detail__title">طلب {{ $order->order_number }}</h5>
                    <p class="admin-detail__subtitle">
                        {{ $order->created_at->format('Y-m-d H:i') }}
                        @if($customerEmail)
                            <span class="mx-1">·</span>
                            {{ $customerEmail }}
                        @endif
                    </p>
                </div>
                <div class="admin-detail__actions">
                    <span class="admin-order-status" style="background-color: {{ $statusColor }}">
                        <span class="admin-order-status__dot"></span>
                        {{ $order->status->name }}
                    </span>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-light btn-sm">
                        <i class="las la-arrow-right me-1"></i> العودة للقائمة
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
                </div>
            @endif

            <div class="admin-order-layout">
                {{-- العمود الرئيسي --}}
                <div class="admin-order-main">
                    <div class="admin-detail-card">
                        <div class="admin-order-customer-card">
                            <div class="admin-order-customer-card__avatar">{{ $customerInitial }}</div>
                            <div class="min-w-0">
                                <p class="admin-order-customer-card__name">{{ $customerName }}</p>
                                <p class="admin-order-customer-card__email">
                                    @if($customerEmail)
                                        {{ $customerEmail }}
                                    @else
                                        لا يوجد بريد إلكتروني
                                    @endif
                                </p>
                            </div>
                            @if($order->user)
                                <a href="{{ route('users.show', $order->user) }}" class="btn btn-outline-primary btn-sm ms-auto">
                                    <i class="las la-user me-1"></i> الملف
                                </a>
                            @endif
                        </div>

                        <div class="admin-detail-card__body">
                            <div class="admin-detail-stats mb-3">
                                <div class="admin-detail-stat">
                                    <span class="admin-detail-stat__label">إجمالي الطلب</span>
                                    <span class="admin-detail-stat__value admin-detail-stat__value--price">
                                        {{ $currencyService->format((float) $order->total) }}
                                    </span>
                                </div>
                                <div class="admin-detail-stat">
                                    <span class="admin-detail-stat__label">عدد البنود</span>
                                    <span class="admin-detail-stat__value">{{ $order->items->sum('quantity') }}</span>
                                    <span class="admin-detail-stat__sub">{{ $order->items->count() }} منتج</span>
                                </div>
                                <div class="admin-detail-stat">
                                    <span class="admin-detail-stat__label">الكوبون</span>
                                    <span class="admin-detail-stat__value">{{ $order->coupon_code ?: '—' }}</span>
                                </div>
                                <div class="admin-detail-stat">
                                    <span class="admin-detail-stat__label">الدفع</span>
                                    <span class="admin-detail-stat__value">
                                        <span class="admin-table__badge {{ $paymentBadge['class'] }}">{{ $paymentBadge['label'] }}</span>
                                    </span>
                                    @if($latestPayment?->paymentMethod)
                                        <span class="admin-detail-stat__sub">{{ $latestPayment->paymentMethod->name }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="admin-order-meta-grid">
                                <div class="admin-order-meta-tile">
                                    <span class="admin-order-meta-tile__label">طريقة الشحن</span>
                                    <span class="admin-order-meta-tile__value">{{ $order->shippingMethod?->name ?? '—' }}</span>
                                </div>
                                <div class="admin-order-meta-tile">
                                    <span class="admin-order-meta-tile__label">تاريخ الطلب</span>
                                    <span class="admin-order-meta-tile__value">{{ $order->created_at->format('Y-m-d H:i') }}</span>
                                </div>
                                @if($order->points_redeemed > 0)
                                    <div class="admin-order-meta-tile">
                                        <span class="admin-order-meta-tile__label">نقاط مستخدمة</span>
                                        <span class="admin-order-meta-tile__value">{{ number_format($order->points_redeemed) }}</span>
                                    </div>
                                @endif
                                @if($order->currency)
                                    <div class="admin-order-meta-tile">
                                        <span class="admin-order-meta-tile__label">العملة</span>
                                        <span class="admin-order-meta-tile__value">{{ $order->currency }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="admin-detail-section admin-detail-card">
                        <div class="admin-detail-card__header">
                            <h6><i class="las la-shopping-bag me-1 text-primary"></i> عناصر الطلب</h6>
                            <span class="admin-table__badge admin-table__badge--secondary">{{ $order->items->count() }} بند</span>
                        </div>
                        <div class="admin-detail-card__body p-0">
                            <div class="admin-order-table-wrap">
                                <table class="admin-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>المنتج</th>
                                            <th>SKU</th>
                                            <th class="text-center">الكمية</th>
                                            <th>السعر</th>
                                            <th>الإجمالي</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items as $item)
                                            @php
                                                $thumbUrl = $item->product?->primary_image_url;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="admin-order-item">
                                                        <div class="admin-order-item__thumb">
                                                            @if($thumbUrl)
                                                                <img src="{{ $thumbUrl }}" alt="">
                                                            @else
                                                                <i class="las la-box"></i>
                                                            @endif
                                                        </div>
                                                        <div class="min-w-0">
                                                            <div class="admin-order-item__name">
                                                                @if($item->product)
                                                                    <a href="{{ route('admin.products.show', $item->product) }}" class="text-decoration-none">
                                                                        {{ $item->product_name }}
                                                                    </a>
                                                                @else
                                                                    {{ $item->product_name }}
                                                                @endif
                                                            </div>
                                                            @if($item->variant_description)
                                                                <div class="admin-order-item__variant">{{ $item->variant_description }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($item->sku)
                                                        <code class="admin-table__sku">{{ $item->sku }}</code>
                                                    @else
                                                        <span class="admin-table__muted">—</span>
                                                    @endif
                                                </td>
                                                <td class="text-center fw-semibold">{{ $item->quantity }}</td>
                                                <td>{{ $currencyService->format((float) $item->unit_price) }}</td>
                                                <td class="fw-semibold">{{ $currencyService->format((float) $item->total) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="admin-detail-card h-100">
                                <div class="admin-detail-card__header">
                                    <h6><i class="las la-shipping-fast me-1 text-primary"></i> عنوان الشحن</h6>
                                </div>
                                <div class="admin-detail-card__body">
                                    @if($order->shipping_address)
                                        <div class="admin-order-address">
                                            <div class="admin-order-address__icon"><i class="las la-map-marker-alt"></i></div>
                                            <div class="admin-order-address__lines">
                                                <span class="admin-order-address__name">{{ $order->shipping_address->full_name }}</span>
                                                <span>{{ $order->shipping_address->full_address }}</span>
                                                @if($order->shipping_address->phone)
                                                    <span><i class="las la-phone me-1"></i>{{ $order->shipping_address->phone }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="admin-detail-empty py-3">
                                            <i class="las la-map-marked-alt"></i>
                                            <p class="mb-0">لا يوجد عنوان شحن.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="admin-detail-card h-100">
                                <div class="admin-detail-card__header">
                                    <h6><i class="las la-file-invoice me-1 text-primary"></i> عنوان الفوترة</h6>
                                </div>
                                <div class="admin-detail-card__body">
                                    @if($order->billing_address)
                                        <div class="admin-order-address">
                                            <div class="admin-order-address__icon"><i class="las la-receipt"></i></div>
                                            <div class="admin-order-address__lines">
                                                <span class="admin-order-address__name">{{ $order->billing_address->full_name }}</span>
                                                <span>{{ $order->billing_address->full_address }}</span>
                                                @if($order->billing_address->phone)
                                                    <span><i class="las la-phone me-1"></i>{{ $order->billing_address->phone }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="admin-detail-empty py-3">
                                            <i class="las la-receipt"></i>
                                            <p class="mb-0">لا يوجد عنوان فوترة.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($order->customer_note)
                        <div class="admin-detail-section admin-detail-card">
                            <div class="admin-detail-card__header">
                                <h6><i class="las la-comment-dots me-1 text-primary"></i> ملاحظة العميل</h6>
                            </div>
                            <div class="admin-detail-card__body">
                                <p class="admin-detail-prose mb-0">{{ $order->customer_note }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- الشريط الجانبي --}}
                <aside class="admin-order-sidebar">
                    <div class="admin-detail-card">
                        <div class="admin-detail-card__header">
                            <h6><i class="las la-sync me-1 text-primary"></i> تحديث الحالة</h6>
                        </div>
                        <div class="admin-detail-card__body admin-order-panel-form">
                            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">حالة الطلب</label>
                                    <select name="order_status_id" class="form-select @error('order_status_id') is-invalid @enderror">
                                        @foreach(\App\Models\OrderStatus::orderBy('order')->get() as $s)
                                            <option value="{{ $s->id }}" {{ $order->order_status_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('order_status_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">ملاحظة إدارية (اختياري)</label>
                                    <textarea name="admin_note" rows="3" class="form-control @error('admin_note') is-invalid @enderror" placeholder="سبب تغيير الحالة أو أي ملاحظات داخلية...">{{ old('admin_note', $order->admin_note) }}</textarea>
                                    @error('admin_note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="las la-save me-1"></i> حفظ الحالة
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="admin-detail-card">
                        <div class="admin-detail-card__header">
                            <h6><i class="las la-calculator me-1 text-primary"></i> ملخص الأسعار</h6>
                        </div>
                        <div class="admin-detail-card__body">
                            <div class="admin-order-price-rows">
                                <div class="admin-order-price-row">
                                    <span class="admin-order-price-row__label">المجموع الفرعي</span>
                                    <span class="admin-order-price-row__value">{{ $currencyService->format((float) $order->subtotal) }}</span>
                                </div>
                                <div class="admin-order-price-row">
                                    <span class="admin-order-price-row__label">الشحن</span>
                                    <span class="admin-order-price-row__value">{{ $currencyService->format((float) $order->shipping_amount) }}</span>
                                </div>
                                <div class="admin-order-price-row">
                                    <span class="admin-order-price-row__label">الضريبة</span>
                                    <span class="admin-order-price-row__value">{{ $currencyService->format((float) $order->tax_amount) }}</span>
                                </div>
                                @if($order->discount_amount > 0)
                                    <div class="admin-order-price-row admin-order-price-row--discount">
                                        <span class="admin-order-price-row__label">الخصم</span>
                                        <span class="admin-order-price-row__value">−{{ $currencyService->format((float) $order->discount_amount) }}</span>
                                    </div>
                                @endif
                                @if($order->points_discount_amount > 0)
                                    <div class="admin-order-price-row admin-order-price-row--discount">
                                        <span class="admin-order-price-row__label">خصم النقاط</span>
                                        <span class="admin-order-price-row__value">−{{ $currencyService->format((float) $order->points_discount_amount) }}</span>
                                    </div>
                                @endif
                                <div class="admin-order-price-row admin-order-price-row--total">
                                    <span class="admin-order-price-row__label">الإجمالي</span>
                                    <span class="admin-order-price-row__value">{{ $currencyService->format((float) $order->total) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($order->payments->isNotEmpty())
                        <div class="admin-detail-card">
                            <div class="admin-detail-card__header">
                                <h6><i class="las la-credit-card me-1 text-primary"></i> سجل المدفوعات</h6>
                                <span class="admin-table__badge admin-table__badge--secondary">{{ $order->payments->count() }}</span>
                            </div>
                            <div class="admin-detail-card__body p-0">
                                <div class="table-responsive">
                                    <table class="admin-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>الوسيلة</th>
                                                <th>المبلغ</th>
                                                <th>الحالة</th>
                                                <th>التاريخ</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($order->payments->sortByDesc('created_at') as $pay)
                                                @php
                                                    $payBadge = $paymentStatusLabels[$pay->status] ?? ['label' => $pay->status, 'class' => 'admin-table__badge--secondary'];
                                                    $stripeSession = $pay->metadata['stripe_session_id'] ?? $pay->transaction_id;
                                                @endphp
                                                <tr>
                                                    <td>{{ $pay->id }}</td>
                                                    <td>{{ $pay->paymentMethod?->name ?? '—' }}</td>
                                                    <td>{{ $currencyService->format((float) $pay->amount) }}</td>
                                                    <td><span class="admin-table__badge {{ $payBadge['class'] }}">{{ $payBadge['label'] }}</span></td>
                                                    <td>{{ $pay->created_at->format('Y-m-d H:i') }}</td>
                                                    <td class="text-nowrap">
                                                        <a href="{{ route('admin.payments.show', $pay) }}" class="btn btn-sm btn-outline-primary">تفاصيل</a>
                                                        @if($pay->isStripe() && $stripeSession)
                                                            @php
                                                                $stripeResolver = app(\App\Services\Payments\StripeCredentialResolver::class);
                                                                $stripeUrl = $stripeResolver->dashboardCheckoutSessionUrl($stripeSession, $pay->paymentMethod);
                                                            @endphp
                                                            @if($stripeUrl)
                                                                <a href="{{ $stripeUrl }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">Stripe</a>
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($order->statusHistory->isNotEmpty())
                        <div class="admin-detail-card">
                            <div class="admin-detail-card__header">
                                <h6><i class="las la-history me-1 text-primary"></i> سجل الحالة</h6>
                            </div>
                            <div class="admin-detail-card__body">
                                <div class="admin-order-timeline">
                                    @foreach($order->statusHistory as $history)
                                        <div class="admin-order-timeline__item">
                                            <span class="admin-order-timeline__dot" style="background-color: {{ $history->newStatus->color ?? 'rgb(var(--primary-rgb))' }}"></span>
                                            <div class="admin-order-timeline__head">
                                                <div>
                                                    <div class="admin-order-timeline__status">{{ $history->newStatus->name ?? '—' }}</div>
                                                    <div class="admin-order-timeline__meta">
                                                        {{ $history->user->name ?? 'النظام' }}
                                                        · {{ $history->created_at->format('Y-m-d H:i') }}
                                                    </div>
                                                </div>
                                            </div>
                                            @if($history->note)
                                                <p class="admin-order-timeline__note mb-0">{{ $history->note }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(($refundableAmount ?? 0) > 0)
                        <div class="admin-detail-card">
                            <div class="admin-detail-card__header">
                                <h6><i class="las la-undo-alt me-1 text-warning"></i> استرداد مالي</h6>
                            </div>
                            <div class="admin-detail-card__body admin-order-panel-form">
                                <p class="small text-muted mb-3">
                                    قابل للاسترداد:
                                    <strong class="text-dark">{{ $currencyService->format((float) $refundableAmount) }}</strong>
                                </p>
                                <form action="{{ route('admin.orders.refund', $order) }}" method="POST">
                                    @csrf
                                    @error('refund')<div class="alert alert-danger py-2">{{ $message }}</div>@enderror
                                    <div class="mb-2">
                                        <label class="form-label">المبلغ</label>
                                        <input type="number" step="0.01" name="amount" class="form-control" max="{{ $refundableAmount }}" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">السبب (اختياري)</label>
                                        <input type="text" name="reason" class="form-control">
                                    </div>
                                    <div class="form-check mb-3">
                                        <input type="checkbox" name="restock" value="1" class="form-check-input" id="refundRestock">
                                        <label class="form-check-label" for="refundRestock">إرجاع المخزون</label>
                                    </div>
                                    <button type="submit" class="btn btn-warning w-100">تنفيذ الاسترداد</button>
                                </form>
                            </div>
                        </div>
                    @endif

                    <div class="admin-detail-card">
                        <div class="admin-detail-card__header">
                            <h6><i class="las la-sticky-note me-1 text-primary"></i> ملاحظات الطلب</h6>
                        </div>
                        <div class="admin-detail-card__body admin-order-panel-form">
                            @forelse($order->notes as $note)
                                <div class="admin-order-note">
                                    <div class="admin-order-note__meta">
                                        {{ $note->created_at->format('Y-m-d H:i') }} — {{ $note->user->name ?? 'النظام' }}
                                        @if($note->is_customer_visible)
                                            <span class="admin-table__badge admin-table__badge--success ms-1">مرئية للعميل</span>
                                        @endif
                                    </div>
                                    <p class="admin-order-note__text">{{ $note->note }}</p>
                                </div>
                            @empty
                                <div class="admin-detail-empty py-2">
                                    <i class="las la-sticky-note"></i>
                                    <p class="mb-0 small">لا توجد ملاحظات.</p>
                                </div>
                            @endforelse
                            <form action="{{ route('admin.orders.notes.store', $order) }}" method="POST" class="mt-3 pt-3 border-top">
                                @csrf
                                <textarea name="note" rows="2" class="form-control mb-2" placeholder="ملاحظة جديدة..." required></textarea>
                                <div class="form-check mb-2">
                                    <input type="checkbox" name="is_customer_visible" value="1" class="form-check-input" id="noteVisible">
                                    <label class="form-check-label" for="noteVisible">مرئية للعميل</label>
                                </div>
                                <button type="submit" class="btn btn-outline-secondary btn-sm w-100">إضافة ملاحظة</button>
                            </form>
                        </div>
                    </div>

                    <div class="admin-detail-card">
                        <div class="admin-detail-card__header">
                            <h6><i class="las la-exchange-alt me-1 text-primary"></i> طلبات المرتجع</h6>
                            @if($hasReturnable)
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#createReturnForm">
                                    إنشاء مرتجع
                                </button>
                            @endif
                        </div>
                        <div class="admin-detail-card__body admin-order-panel-form">
                            @if($order->returns->isNotEmpty())
                                @foreach($order->returns as $ret)
                                    <div class="admin-order-return">
                                        <div class="admin-order-return__info">
                                            <span class="admin-table__badge @if($ret->status === 'pending') admin-table__badge--warning @elseif($ret->status === 'approved') admin-table__badge--success @else admin-table__badge--danger @endif">
                                                @if($ret->status === 'pending') قيد الانتظار
                                                @elseif($ret->status === 'approved') معتمد
                                                @else مرفوض
                                                @endif
                                            </span>
                                            <div class="small text-muted mt-1">{{ $ret->created_at->format('Y-m-d H:i') }}</div>
                                            @if($ret->reason)
                                                <div class="small mt-1">{{ Str::limit($ret->reason, 60) }}</div>
                                            @endif
                                        </div>
                                        <a href="{{ route('admin.order-returns.show', $ret) }}" class="btn btn-sm btn-primary flex-shrink-0">عرض</a>
                                    </div>
                                @endforeach
                            @else
                                <div class="admin-detail-empty py-2">
                                    <i class="las la-undo"></i>
                                    <p class="mb-0 small">لا توجد طلبات مرتجع.</p>
                                </div>
                            @endif

                            @if($hasReturnable)
                                <div class="collapse mt-3 pt-3 border-top" id="createReturnForm">
                                    <h6 class="mb-3 fs-13 fw-bold">إنشاء طلب مرتجع جديد</h6>
                                    <form action="{{ route('admin.order-returns.store', $order) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">سبب المرتجع (اختياري)</label>
                                            <input type="text" name="reason" class="form-control" placeholder="مثل: عيب، تغيير رأي">
                                        </div>
                                        <div class="admin-order-table-wrap mb-3">
                                            <table class="admin-table table-sm mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>المنتج</th>
                                                        <th>الطلب</th>
                                                        <th>مرتجع</th>
                                                        <th>الكمية</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $idx = 0; @endphp
                                                    @foreach($order->items as $item)
                                                        @php $already = $returnedQty[$item->id] ?? 0; $max = $item->quantity - $already; @endphp
                                                        @if($max > 0)
                                                            <tr>
                                                                <td>
                                                                    <span class="small fw-semibold">{{ $item->product_name }}</span>
                                                                    @if($item->variant_description)
                                                                        <br><span class="admin-order-item__variant">{{ $item->variant_description }}</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $item->quantity }}</td>
                                                                <td>{{ $already }}</td>
                                                                <td>
                                                                    <input type="hidden" name="items[{{ $idx }}][order_item_id]" value="{{ $item->id }}">
                                                                    <input type="number" name="items[{{ $idx }}][quantity]" min="1" max="{{ $max }}" value="{{ $max }}" class="form-control form-control-sm" style="width: 72px;">
                                                                </td>
                                                            </tr>
                                                            @php $idx++; @endphp
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">إرسال طلب المرتجع</button>
                                    </form>
                                </div>
                            @elseif($order->returns->isNotEmpty())
                                <p class="text-muted small mb-0 mt-2">تم إرجاع كل بنود هذا الطلب مسبقاً.</p>
                            @endif
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
@stop
