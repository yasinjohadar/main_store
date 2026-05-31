@extends('admin.layouts.master')

@section('page-title')
    سجل المدفوعات
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                    <h4 class="mb-0">سجل المدفوعات</h4>
                    <p class="mb-0 text-muted">جميع محاولات الدفع عبر البوابات</p>
                </div>
                <a href="{{ route('admin.stripe-settings.index') }}" class="btn btn-outline-primary btn-sm">إعدادات Stripe</a>
            </div>

            <div class="card custom-card">
                <div class="card-header">
                    <form method="GET" class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label">الحالة</label>
                            <select name="status" class="form-select">
                                <option value="">الكل</option>
                                @foreach($statusLabels as $slug => $info)
                                    <option value="{{ $slug }}" @selected(request('status') === $slug)>{{ $info['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">وسيلة الدفع</label>
                            <select name="payment_method_id" class="form-select">
                                <option value="">الكل</option>
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method->id }}" @selected((int) request('payment_method_id') === $method->id)>{{ $method->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">رقم الطلب</label>
                            <input type="text" name="order_number" class="form-control" value="{{ request('order_number') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">معرف المعاملة</label>
                            <input type="text" name="transaction_id" class="form-control" value="{{ request('transaction_id') }}" dir="ltr">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">من تاريخ</label>
                            <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">إلى تاريخ</label>
                            <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-secondary btn-sm">تصفية</button>
                            <a href="{{ route('admin.payments.index') }}" class="btn btn-link btn-sm">إعادة تعيين</a>
                        </div>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="admin-table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الطلب</th>
                                    <th>المبلغ</th>
                                    <th>الوسيلة</th>
                                    <th>الحالة</th>
                                    <th>المعاملة</th>
                                    <th>التاريخ</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                    @php $badge = $statusLabels[$payment->status] ?? ['label' => $payment->status, 'class' => 'admin-table__badge--secondary']; @endphp
                                    <tr>
                                        <td>{{ $payment->id }}</td>
                                        <td>
                                            @if($payment->order)
                                                <a href="{{ route('admin.orders.show', $payment->order) }}">{{ $payment->order->order_number }}</a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>{{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}</td>
                                        <td>{{ $payment->paymentMethod?->name ?? '—' }}</td>
                                        <td><span class="admin-table__badge {{ $badge['class'] }}">{{ $badge['label'] }}</span></td>
                                        <td><code class="small">{{ \Illuminate\Support\Str::limit($payment->transaction_id ?? '—', 24) }}</code></td>
                                        <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-outline-primary">تفاصيل</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">لا توجد مدفوعات.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($payments->hasPages())
                    <div class="card-footer">{{ $payments->links() }}</div>
                @endif
            </div>
        </div>
    </div>
@stop
