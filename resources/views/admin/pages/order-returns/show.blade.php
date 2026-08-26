@extends('admin.layouts.master')

@section('page-title')
    طلب مرتجع #{{ $orderReturn->id }}
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="las la-undo-alt"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>طلب مرتجع #{{ $orderReturn->id }}</h1>
                        <p>
                            @if($orderReturn->status === 'pending')
                                <span class="admin-badge admin-badge-warning">قيد الانتظار</span>
                            @elseif($orderReturn->status === 'approved')
                                <span class="admin-badge admin-badge-success">معتمد</span>
                            @else
                                <span class="admin-badge admin-badge-danger">مرفوض</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('admin.orders.show', $orderReturn->order) }}" class="admin-btn admin-btn-secondary">
                        <i class="las la-shopping-bag"></i>
                        عرض الطلب الأصلي
                    </a>
                    <a href="{{ route('admin.order-returns.index') }}" class="admin-btn admin-btn-light">
                        <i class="las la-arrow-right"></i>
                        قائمة طلبات المرتجع
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="admin-page-card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title mb-0">الطلب الأصلي</div>
                            <a href="{{ route('admin.orders.show', $orderReturn->order) }}" class="admin-user-link">{{ $orderReturn->order->order_number }}</a>
                        </div>
                        <div class="card-body">
                            <p class="mb-1"><strong>السبب:</strong> {{ $orderReturn->reason ?: '—' }}</p>
                            <p class="mb-1"><strong>طالب المرتجع:</strong> {{ $orderReturn->requestedByUser->name ?? '—' }}</p>
                            <p class="mb-0"><strong>تاريخ الطلب:</strong> {{ $orderReturn->requested_at ? $orderReturn->requested_at->format('Y-m-d H:i') : $orderReturn->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>

                    <div class="admin-page-card">
                        <div class="card-header"><div class="card-title">بنود المرتجع</div></div>
                        <div class="admin-table-wrap">
                            <table class="admin-data-table">
                                <thead>
                                    <tr>
                                        <th>المنتج</th>
                                        <th>الكمية المرتجعة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orderReturn->items as $ri)
                                        <tr>
                                            <td>
                                                {{ $ri->orderItem->product_name ?? '—' }}
                                                @if($ri->orderItem->variant_description)
                                                    <br><small class="text-muted">{{ $ri->orderItem->variant_description }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $ri->quantity }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    @if($orderReturn->status === 'pending')
                        <div class="admin-page-card mb-3">
                            <div class="card-header"><div class="card-title">اعتماد / رفض</div></div>
                            <div class="card-body">
                                <form action="{{ route('admin.order-returns.approve', $orderReturn) }}" method="POST" class="mb-3">
                                    @csrf
                                    <div class="mb-2">
                                        <label class="admin-form-label">ملاحظة إدارية (اختياري)</label>
                                        <textarea name="admin_note" rows="2" class="form-control" placeholder="ملاحظة عند الاعتماد">{{ old('admin_note', $orderReturn->admin_note) }}</textarea>
                                    </div>
                                    <button type="submit" class="admin-btn admin-btn-primary w-100">اعتماد طلب المرتجع واسترجاع المخزون</button>
                                </form>
                                <form action="{{ route('admin.order-returns.reject', $orderReturn) }}" method="POST">
                                    @csrf
                                    <div class="mb-2">
                                        <label class="admin-form-label">ملاحظة (اختياري)</label>
                                        <textarea name="admin_note" rows="2" class="form-control" placeholder="سبب الرفض">{{ old('admin_note') }}</textarea>
                                    </div>
                                    <button type="submit" class="admin-btn admin-btn-danger w-100">رفض طلب المرتجع</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="admin-page-card mb-3">
                            <div class="card-header"><div class="card-title">معالجة الطلب</div></div>
                            <div class="card-body">
                                @if($orderReturn->processed_at)
                                    <p class="mb-1"><strong>تاريخ المعالجة:</strong> {{ $orderReturn->processed_at->format('Y-m-d H:i') }}</p>
                                    <p class="mb-0"><strong>معالج بواسطة:</strong> {{ $orderReturn->processedByUser->name ?? '—' }}</p>
                                @endif
                                @if($orderReturn->admin_note)
                                    <hr>
                                    <p class="mb-0"><strong>ملاحظة إدارية:</strong><br>{{ $orderReturn->admin_note }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
