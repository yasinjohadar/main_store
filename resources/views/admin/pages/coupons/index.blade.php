@extends('admin.layouts.master')

@section('page-title')
    الكوبونات
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <h5 class="page-title fs-21 mb-1">الكوبونات</h5>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card admin-table-card">
                        <div class="admin-table-toolbar admin-table-toolbar--split">
                            <div class="admin-table-toolbar__row">
                                <div class="admin-table-toolbar__start d-flex flex-wrap gap-2">
                                    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary btn-sm">
                                        <i class="las la-plus me-1"></i> إضافة كوبون
                                    </a>
                                    <form action="{{ route('admin.coupons.mark-expired') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-sm">تعليم المنتهية</button>
                                    </form>
                                    <a href="{{ route('admin.coupons.usage-report') }}" class="btn btn-info btn-sm" target="_blank">
                                        <i class="las la-chart-bar me-1"></i> تقرير الاستخدام
                                    </a>
                                </div>

                                <form action="{{ route('admin.coupons.index') }}" method="GET"
                                    class="admin-table-filter-form d-flex align-items-center gap-2 flex-wrap ms-auto"
                                    data-admin-table-target="#admin-table-coupons"
                                    data-admin-table-autosubmit>
                                    <input type="text" name="search" class="form-control"
                                        style="min-width: 180px; max-width: 220px;"
                                        placeholder="بحث بالكود أو الاسم" value="{{ request('search') }}">

                                    <select name="status" class="form-select" style="width: auto;">
                                        <option value="">كل الحالات</option>
                                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
                                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>منتهي</option>
                                    </select>

                                    <select name="type" class="form-select" style="width: auto;">
                                        <option value="">كل أنواع الخصم</option>
                                        <option value="percentage" {{ request('type') === 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
                                        <option value="fixed_amount" {{ request('type') === 'fixed_amount' ? 'selected' : '' }}>مبلغ ثابت</option>
                                        <option value="buy_x_get_y" {{ request('type') === 'buy_x_get_y' ? 'selected' : '' }}>اشترِ واحصل على</option>
                                    </select>

                                    <input type="date" name="expires_from" class="form-control" style="width: auto;" value="{{ request('expires_from') }}" title="انتهاء من">
                                    <input type="date" name="expires_to" class="form-control" style="width: auto;" value="{{ request('expires_to') }}" title="انتهاء إلى">

                                    <select name="per_page" class="form-select" style="width: auto;">
                                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 / صفحة</option>
                                        <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 / صفحة</option>
                                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 / صفحة</option>
                                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / صفحة</option>
                                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 / صفحة</option>
                                    </select>

                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="las la-search"></i>
                                    </button>
                                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-light btn-sm" data-admin-table-reset>مسح</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body p-0" id="admin-table-coupons" data-admin-table>
                            @include('admin.pages.coupons.partials.table-body')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
<script src="{{ asset('assets/js/admin-coupon-inline-edit.js') }}"></script>
@stop
