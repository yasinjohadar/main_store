@extends('admin.layouts.master')

@section('page-title')
    العملاء
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="las la-user-friends"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>العملاء</h1>
                        <p>إدارة بيانات العملاء وسجل الطلبات والإنفاق</p>
                    </div>
                </div>
            </div>

            <div class="admin-report-stats">
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-blue">
                    <span class="admin-report-stat-icon"><i class="las la-user-friends"></i></span>
                    <span class="admin-report-stat-label">إجمالي العملاء</span>
                    <span class="admin-report-stat-value">{{ number_format($customerStats['total'] ?? 0) }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-green">
                    <span class="admin-report-stat-icon"><i class="las la-shopping-bag"></i></span>
                    <span class="admin-report-stat-label">لديهم طلبات</span>
                    <span class="admin-report-stat-value">{{ number_format($customerStats['with_orders'] ?? 0) }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-cyan">
                    <span class="admin-report-stat-icon"><i class="las la-coins"></i></span>
                    <span class="admin-report-stat-label">إجمالي الإنفاق</span>
                    <span class="admin-report-stat-value">{{ number_format($customerStats['total_spent'] ?? 0, 2) }} ر.س</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-amber">
                    <span class="admin-report-stat-icon"><i class="las la-user-plus"></i></span>
                    <span class="admin-report-stat-label">جدد هذا الشهر</span>
                    <span class="admin-report-stat-value">{{ number_format($customerStats['new_this_month'] ?? 0) }}</span>
                </div>
            </div>

            <div class="admin-page-card">
                <div class="card-toolbar">
                    <form action="{{ route('admin.customers.index') }}" method="GET" class="admin-filters">
                        <div class="search-input-wrap">
                            <i class="las la-search"></i>
                            <input type="text" name="search" class="form-control"
                                placeholder="الاسم أو البريد أو الجوال" value="{{ request('search') }}" autocomplete="off">
                        </div>

                        <select name="has_orders" class="form-select admin-filter-select">
                            <option value="">كل حالات الطلبات</option>
                            <option value="1" {{ request('has_orders') === '1' ? 'selected' : '' }}>لديه طلبات</option>
                            <option value="0" {{ request('has_orders') === '0' ? 'selected' : '' }}>بدون طلبات</option>
                        </select>

                        <input type="number" name="min_total" class="form-control admin-filter-select" min="0" step="0.01"
                            placeholder="إجمالي إنفاق من" value="{{ request('min_total') }}">

                        <div class="admin-filter-dates">
                            <input type="date" name="registered_from" class="form-control" value="{{ request('registered_from') }}" title="تسجيل من">
                            <span class="admin-filter-dates-sep">—</span>
                            <input type="date" name="registered_to" class="form-control" value="{{ request('registered_to') }}" title="تسجيل إلى">
                        </div>

                        <button type="submit" class="admin-btn admin-btn-primary admin-btn-sm">
                            <i class="las la-search"></i>
                        </button>
                        <a href="{{ route('admin.customers.index') }}" class="admin-btn admin-btn-secondary admin-btn-sm">مسح</a>
                    </form>
                </div>

                <div class="admin-table-wrap">
                    <div class="table-responsive">
                        <table class="admin-data-table">
                            <thead>
                                <tr>
                                    <th>العميل</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الجوال</th>
                                    <th>رصيد النقاط</th>
                                    <th>عدد الطلبات</th>
                                    <th>إجمالي الإنفاق</th>
                                    <th>تاريخ التسجيل</th>
                                    <th style="width: 110px;">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                    <tr>
                                        <td>
                                            <div class="admin-user-cell">
                                                <span class="admin-avatar-initial">{{ mb_substr($customer->name, 0, 1) }}</span>
                                                <a href="{{ route('admin.customers.show', $customer) }}" class="admin-user-link">
                                                    {{ $customer->name }}
                                                </a>
                                            </div>
                                        </td>
                                        <td>{{ $customer->email }}</td>
                                        <td>
                                            @if($customer->phone)
                                                <span class="admin-phone-cell">{{ $customer->phone }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td><span class="admin-badge admin-badge-role">{{ number_format($customer->loyalty_points_balance ?? 0, 0) }} نقطة</span></td>
                                        <td>{{ $customer->orders_count ?? 0 }}</td>
                                        <td class="fw-bold">{{ number_format($customer->total_spent ?? 0, 2) }} ر.س</td>
                                        <td class="text-muted">{{ $customer->created_at?->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.customers.show', $customer) }}" class="admin-btn admin-btn-secondary admin-btn-sm">
                                                عرض الملف
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">
                                            <div class="admin-empty-state">
                                                <i class="las la-user-friends"></i>
                                                لا يوجد عملاء مطابقون لشروط البحث
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="admin-table-footer">
                    {{ $customers->links() }}
                </div>
            </div>

        </div>
    </div>
@stop
