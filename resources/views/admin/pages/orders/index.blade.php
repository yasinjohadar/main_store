@extends('admin.layouts.master')

@section('page-title')
    الطلبات
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
                    <span class="admin-page-banner-icon"><i class="las la-shopping-bag"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>الطلبات</h1>
                        <p>متابعة طلبات المتجر وحالاتها</p>
                    </div>
                </div>
            </div>

            <div class="admin-report-stats">
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-blue">
                    <span class="admin-report-stat-icon"><i class="las la-shopping-bag"></i></span>
                    <span class="admin-report-stat-label">إجمالي الطلبات</span>
                    <span class="admin-report-stat-value">{{ number_format($orderStats['total'] ?? 0) }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-amber">
                    <span class="admin-report-stat-icon"><i class="las la-clock"></i></span>
                    <span class="admin-report-stat-label">قيد الانتظار</span>
                    <span class="admin-report-stat-value">{{ number_format($orderStats['pending'] ?? 0) }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-green">
                    <span class="admin-report-stat-icon"><i class="las la-calendar-day"></i></span>
                    <span class="admin-report-stat-label">طلبات اليوم</span>
                    <span class="admin-report-stat-value">{{ number_format($orderStats['today'] ?? 0) }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-cyan">
                    <span class="admin-report-stat-icon"><i class="las la-coins"></i></span>
                    <span class="admin-report-stat-label">إجمالي المبيعات</span>
                    <span class="admin-report-stat-value">{{ $currencyService->format($orderStats['total_sales'] ?? 0) }}</span>
                </div>
            </div>

            <div class="admin-page-card admin-table-card">
                <div class="card-toolbar">
                    <form action="{{ route('admin.orders.index') }}" method="GET"
                        class="admin-filters admin-table-filter-form"
                        data-admin-table-target="#admin-table-orders"
                        data-admin-table-autosubmit>
                        <div class="search-input-wrap">
                            <i class="las la-search"></i>
                            <input type="text" name="order_number" class="form-control"
                                placeholder="رقم الطلب" value="{{ request('order_number') }}" autocomplete="off">
                        </div>

                        <select name="status" class="form-select admin-filter-select">
                            <option value="">كل الحالات</option>
                            @foreach($statuses as $s)
                                <option value="{{ $s->slug }}" {{ request('status') == $s->slug ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>

                        <div class="admin-filter-dates">
                            <input type="date" name="from" class="form-control" value="{{ request('from') }}" title="من تاريخ">
                            <span class="admin-filter-dates-sep">—</span>
                            <input type="date" name="to" class="form-control" value="{{ request('to') }}" title="إلى تاريخ">
                        </div>

                        <select name="per_page" class="form-select admin-filter-select" title="عدد العناصر في الصفحة">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 / صفحة</option>
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 / صفحة</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 / صفحة</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / صفحة</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 / صفحة</option>
                        </select>

                        <button type="submit" class="admin-btn admin-btn-primary admin-btn-sm">
                            <i class="las la-search"></i>
                        </button>
                        <a href="{{ route('admin.orders.index') }}" class="admin-btn admin-btn-secondary admin-btn-sm" data-admin-table-reset>مسح</a>
                    </form>
                </div>

                <div class="admin-table-wrap" id="admin-table-orders" data-admin-table>
                    @include('admin.pages.orders.partials.table-body')
                </div>
            </div>

        </div>
    </div>

    @php
        $orderStatusesJson = $statuses->map(function ($s) {
            return [
                'id' => $s->id,
                'name' => $s->name,
                'color' => $s->color,
                'slug' => $s->slug,
            ];
        })->values();
    @endphp

    <script type="application/json" id="admin-order-statuses-json">
        @json($orderStatusesJson)
    </script>
@stop

@section('script')
<script src="{{ asset('assets/js/admin-order-inline-status.js') }}"></script>
@stop
