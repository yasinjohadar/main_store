@extends('admin.layouts.master')

@section('page-title')
    طلبات المرتجع
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
                        <h1>طلبات المرتجع</h1>
                        <p>مراجعة طلبات الإرجاع والموافقة عليها أو رفضها</p>
                    </div>
                </div>
            </div>

            <div class="admin-report-stats">
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-blue">
                    <span class="admin-report-stat-icon"><i class="las la-undo-alt"></i></span>
                    <span class="admin-report-stat-label">إجمالي المرتجعات</span>
                    <span class="admin-report-stat-value">{{ $returnStats['total'] ?? 0 }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-amber">
                    <span class="admin-report-stat-icon"><i class="las la-clock"></i></span>
                    <span class="admin-report-stat-label">قيد الانتظار</span>
                    <span class="admin-report-stat-value">{{ $returnStats['pending'] ?? 0 }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-green">
                    <span class="admin-report-stat-icon"><i class="las la-check-circle"></i></span>
                    <span class="admin-report-stat-label">معتمدة</span>
                    <span class="admin-report-stat-value">{{ $returnStats['approved'] ?? 0 }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-red">
                    <span class="admin-report-stat-icon"><i class="las la-times-circle"></i></span>
                    <span class="admin-report-stat-label">مرفوضة</span>
                    <span class="admin-report-stat-value">{{ $returnStats['rejected'] ?? 0 }}</span>
                </div>
            </div>

            <div class="admin-page-card">
                <div class="card-toolbar">
                    <form action="{{ route('admin.order-returns.index') }}" method="GET" class="admin-filters">
                        <select name="status" class="form-select admin-filter-select">
                            <option value="">كل الحالات</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>معتمد</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوض</option>
                        </select>
                        <button type="submit" class="admin-btn admin-btn-primary admin-btn-sm">تصفية</button>
                        <a href="{{ route('admin.order-returns.index') }}" class="admin-btn admin-btn-secondary admin-btn-sm">مسح</a>
                    </form>
                </div>

                <div class="admin-table-wrap">
                    <div class="table-responsive">
                        <table class="admin-data-table">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th>الطلب</th>
                                    <th>طالب المرتجع</th>
                                    <th>الحالة</th>
                                    <th>السبب</th>
                                    <th>التاريخ</th>
                                    <th style="width: 100px;">إجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($returns as $r)
                                    <tr>
                                        <td class="row-number">{{ $r->id }}</td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $r->order) }}" class="admin-user-link">{{ $r->order->order_number }}</a>
                                        </td>
                                        <td>{{ $r->requestedByUser->name ?? '—' }}</td>
                                        <td>
                                            @if($r->status === 'pending')
                                                <span class="admin-badge admin-badge-warning">قيد الانتظار</span>
                                            @elseif($r->status === 'approved')
                                                <span class="admin-badge admin-badge-success">معتمد</span>
                                            @else
                                                <span class="admin-badge admin-badge-danger">مرفوض</span>
                                            @endif
                                        </td>
                                        <td class="text-muted">{{ Str::limit($r->reason, 40) ?: '—' }}</td>
                                        <td class="text-muted">{{ $r->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.order-returns.show', $r) }}" class="admin-btn admin-btn-primary admin-btn-sm">عرض</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="admin-empty-state">
                                                <i class="las la-undo-alt"></i>
                                                لا توجد طلبات مرتجع
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($returns->hasPages())
                    <div class="admin-table-footer">
                        {{ $returns->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
@stop
