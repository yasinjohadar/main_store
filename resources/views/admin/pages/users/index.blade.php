@extends('admin.layouts.master')

@section('page-title')
    قائمة المستخدمون
@stop



@section('css')
@stop

@section('content')
    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @endif

    @if (\Session::has('error'))
        <div class="alert alert-danger">
            <ul>
                <li>{!! \Session::get('error') !!}</li>
            </ul>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <!-- Start::app-content -->
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">

            <!-- Page banner -->
            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="las la-users"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>كافة المستخدمين</h1>
                        <p>إدارة حسابات النظام والأدوار والصلاحيات</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('users.create') }}" class="admin-btn admin-btn-light">
                        <i class="las la-user-plus"></i>
                        إنشاء مستخدم جديد
                    </a>
                </div>
            </div>

            <!-- Stat tiles -->
            <div class="admin-report-stats">
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-blue">
                    <span class="admin-report-stat-icon"><i class="las la-users"></i></span>
                    <span class="admin-report-stat-label">إجمالي المستخدمين</span>
                    <span class="admin-report-stat-value">{{ $userStats['total'] ?? 0 }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-green">
                    <span class="admin-report-stat-icon"><i class="las la-user-check"></i></span>
                    <span class="admin-report-stat-label">حسابات مفعّلة</span>
                    <span class="admin-report-stat-value">{{ $userStats['active'] ?? 0 }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-amber">
                    <span class="admin-report-stat-icon"><i class="las la-user-slash"></i></span>
                    <span class="admin-report-stat-label">حسابات موقوفة</span>
                    <span class="admin-report-stat-value">{{ $userStats['inactive'] ?? 0 }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-red">
                    <span class="admin-report-stat-icon"><i class="las la-user-plus"></i></span>
                    <span class="admin-report-stat-label">جدد هذا الشهر</span>
                    <span class="admin-report-stat-value">{{ $userStats['new_this_month'] ?? 0 }}</span>
                </div>
            </div>

            <!-- Table card -->
            <div class="admin-page-card admin-table-card">
                <div class="card-toolbar">
                    <form action="{{ route('users.index') }}" method="GET"
                        class="admin-filters admin-table-filter-form"
                        data-admin-table-target="#admin-table-users"
                        data-admin-table-autosubmit>
                        <div class="search-input-wrap">
                            <i class="las la-search"></i>
                            <input type="text" name="query" class="form-control"
                                placeholder="بحث بالاسم أو الإيميل أو الهاتف..." value="{{ request('query') }}" autocomplete="off">
                        </div>

                        <select name="is_active" class="form-select admin-filter-select">
                            <option value="">كل حالات التفعيل</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>مفعّل</option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>موقوف</option>
                        </select>

                        <select name="per_page" class="form-select admin-filter-select" title="عدد العناصر في الصفحة">
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 / صفحة</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 / صفحة</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / صفحة</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 / صفحة</option>
                        </select>

                        <button type="submit" class="admin-btn admin-btn-primary admin-btn-sm">
                            <i class="las la-search"></i>
                        </button>
                        <a href="{{ route('users.index') }}" class="admin-btn admin-btn-secondary admin-btn-sm" data-admin-table-reset>مسح</a>
                    </form>
                </div>

                <div class="admin-table-wrap" id="admin-table-users" data-admin-table>
                    @include('admin.pages.users.partials.table-body')
                </div>
            </div>

        </div>
    </div>
    <!-- End::app-content -->

@stop

@section('script')
<script src="{{ asset('assets/js/admin-user-toggle.js') }}"></script>
@stop
