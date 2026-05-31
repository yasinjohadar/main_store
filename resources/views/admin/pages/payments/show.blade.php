@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الدفع #{{ $payment->id }}
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $badge = $statusLabels[$payment->status] ?? ['label' => $payment->status, 'class' => 'admin-table__badge--secondary'];
        $meta = $payment->metadata ?? [];
    @endphp

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                    <h4 class="mb-0">دفع #{{ $payment->id }}</h4>
                    @if($payment->order)
                        <p class="mb-0 text-muted">طلب <a href="{{ route('admin.orders.show', $payment->order) }}">{{ $payment->order->order_number }}</a></p>
                    @endif
                </div>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary btn-sm">العودة للسجل</a>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card custom-card mb-4">
                        <div class="card-header"><div class="card-title mb-0">ملخص العملية</div></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <span class="text-muted small d-block">الحالة</span>
                                    <span class="admin-table__badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-muted small d-block">المبلغ</span>
                                    <strong>{{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}</strong>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-muted small d-block">تاريخ الدفع</span>
                                    {{ $payment->paid_at?->format('Y-m-d H:i') ?? '—' }}
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted small d-block">وسيلة الدفع</span>
                                    {{ $payment->paymentMethod?->name ?? '—' }}
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted small d-block">معرف المعاملة</span>
                                    <code dir="ltr">{{ $payment->transaction_id ?? '—' }}</code>
                                </div>
                                @if($payment->isStripe())
                                    <div class="col-12">
                                        <span class="text-muted small d-block">Stripe</span>
                                        <div class="d-flex flex-wrap gap-2">
                                            @if($stripeLinks['session'])
                                                <a href="{{ $stripeLinks['session'] }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">جلسة Checkout</a>
                                            @endif
                                            @if($stripeLinks['payment'])
                                                <a href="{{ $stripeLinks['payment'] }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">Payment Intent</a>
                                            @endif
                                        </div>
                                        <div class="small text-muted mt-2 font-monospace" dir="ltr">
                                            session: {{ $stripeSessionId ?? '—' }}<br>
                                            intent: {{ $stripePaymentIntentId ?? '—' }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if(!empty($meta) || $payment->gateway_response)
                        <div class="card custom-card mb-4">
                            <div class="card-header"><div class="card-title mb-0">البيانات التقنية</div></div>
                            <div class="card-body">
                                @if(!empty($meta))
                                    <p class="small fw-semibold">metadata</p>
                                    <pre class="small bg-light p-3 rounded mb-3" dir="ltr">{{ json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @endif
                                @if($payment->gateway_response)
                                    <p class="small fw-semibold">gateway_response</p>
                                    <pre class="small bg-light p-3 rounded mb-0">{{ is_string($payment->gateway_response) ? $payment->gateway_response : json_encode($payment->gateway_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="card custom-card">
                        <div class="card-header"><div class="card-title mb-0">أحداث Webhook</div></div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="admin-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>النوع</th>
                                            <th>معرف الحدث</th>
                                            <th>الحالة</th>
                                            <th>التاريخ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($payment->events as $event)
                                            <tr>
                                                <td><code>{{ $event->event_type }}</code></td>
                                                <td class="font-monospace small" dir="ltr">{{ $event->event_id }}</td>
                                                <td>{{ $event->status }}</td>
                                                <td>{{ $event->created_at->format('Y-m-d H:i') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3">لا توجد أحداث مسجّلة بعد.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    @if($payment->order)
                        <div class="card custom-card mb-4">
                            <div class="card-header"><div class="card-title mb-0">الطلب</div></div>
                            <div class="card-body small">
                                <p class="mb-1"><strong>العميل:</strong> {{ $payment->order->user?->name ?? $payment->order->guest_email ?? '—' }}</p>
                                <p class="mb-1"><strong>إجمالي الطلب:</strong> {{ number_format((float) $payment->order->total, 2) }}</p>
                                <p class="mb-0"><strong>حالة الطلب:</strong> {{ $payment->order->status?->name ?? '—' }}</p>
                            </div>
                        </div>
                    @endif

                    @if($payment->status === 'completed' && ($refundableAmount ?? 0) > 0)
                        <div class="card custom-card">
                            <div class="card-header"><div class="card-title mb-0">استرداد</div></div>
                            <div class="card-body">
                                <p class="small text-muted">قابل للاسترداد على الطلب: <strong>{{ number_format($refundableAmount, 2) }}</strong></p>
                                <form method="POST" action="{{ route('admin.payments.refund', $payment) }}">
                                    @csrf
                                    @error('refund')<div class="alert alert-danger py-2 small">{{ $message }}</div>@enderror
                                    <div class="mb-2">
                                        <label class="form-label">المبلغ</label>
                                        <input type="number" step="0.01" name="amount" class="form-control" max="{{ $refundableAmount }}" value="{{ $refundableAmount }}" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">السبب</label>
                                        <input type="text" name="reason" class="form-control">
                                    </div>
                                    <div class="form-check mb-3">
                                        <input type="checkbox" name="restock" value="1" class="form-check-input" id="paymentRefundRestock">
                                        <label class="form-check-label" for="paymentRefundRestock">إرجاع المخزون</label>
                                    </div>
                                    <button type="submit" class="btn btn-warning w-100">تنفيذ الاسترداد</button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
