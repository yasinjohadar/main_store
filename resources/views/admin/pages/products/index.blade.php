@extends('admin.layouts.master')

@section('page-title')
    قائمة المنتجات
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
                    <span class="admin-page-banner-icon"><i class="las la-box"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>كافة المنتجات</h1>
                        <p>إدارة كتالوج المتجر والأسعار والمخزون</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('admin.products.create') }}" class="admin-btn admin-btn-light">
                        <i class="las la-plus"></i>
                        إضافة منتج جديد
                    </a>
                </div>
            </div>

            <div class="admin-report-stats">
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-blue">
                    <span class="admin-report-stat-icon"><i class="las la-box"></i></span>
                    <span class="admin-report-stat-label">إجمالي المنتجات</span>
                    <span class="admin-report-stat-value">{{ $productStats['total'] ?? 0 }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-green">
                    <span class="admin-report-stat-icon"><i class="las la-check-circle"></i></span>
                    <span class="admin-report-stat-label">نشطة</span>
                    <span class="admin-report-stat-value">{{ $productStats['active'] ?? 0 }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-cyan">
                    <span class="admin-report-stat-icon"><i class="las la-file-alt"></i></span>
                    <span class="admin-report-stat-label">مسودات</span>
                    <span class="admin-report-stat-value">{{ $productStats['draft'] ?? 0 }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-amber">
                    <span class="admin-report-stat-icon"><i class="las la-exclamation-triangle"></i></span>
                    <span class="admin-report-stat-label">مخزون منخفض</span>
                    <span class="admin-report-stat-value">{{ $productStats['low_stock'] ?? 0 }}</span>
                </div>
            </div>

            <div class="admin-page-card admin-table-card">
                <div class="card-toolbar admin-products-toolbar">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <form id="bulk-products-form" action="{{ route('admin.products.bulk-update') }}" method="POST" class="d-flex align-items-center gap-2">
                            @csrf
                            <select name="action" class="form-select admin-filter-select">
                                <option value="">إجراء جماعي...</option>
                                <option value="activate">تفعيل المنتجات المحددة</option>
                                <option value="draft">تحويل إلى مسودة</option>
                                <option value="hide">إخفاء من المتجر</option>
                            </select>
                            <button type="submit" class="admin-btn admin-btn-secondary admin-btn-sm" onclick="return confirm('تأكيد تنفيذ الإجراء الجماعي على المنتجات المحددة؟');">تطبيق</button>
                        </form>
                        <button type="button" id="btn-compare-selected" class="admin-btn admin-btn-secondary admin-btn-sm" disabled
                            data-compare-url="{{ route('admin.products.compare') }}">
                            <i class="las la-balance-scale"></i>
                            مقارنة المنتجات
                        </button>
                    </div>

                    <form action="{{ route('admin.products.index') }}" method="GET"
                        class="admin-filters admin-table-filter-form"
                        data-admin-table-target="#admin-table-products"
                        data-admin-table-autosubmit>
                        <div class="search-input-wrap">
                            <i class="las la-search"></i>
                            <input type="text" name="query" class="form-control"
                                placeholder="بحث بالاسم أو SKU" value="{{ request('query') }}" autocomplete="off">
                        </div>
                        <select name="status" class="form-select admin-filter-select">
                            <option value="">كل الحالات</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                            <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>أرشيف</option>
                        </select>
                        <select name="category_id" class="form-select admin-filter-select">
                            <option value="">كل التصنيفات</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
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
                        <a href="{{ route('admin.products.index') }}" class="admin-btn admin-btn-secondary admin-btn-sm" data-admin-table-reset>مسح</a>
                    </form>
                </div>

                <div class="admin-table-wrap" id="admin-table-products" data-admin-table>
                    @include('admin.pages.products.partials.table-body')
                </div>
            </div>

        </div>
    </div>

    <script type="application/json" id="admin-product-categories-json">
        @json($categories->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->values())
    </script>
@stop

@section('script')
<script src="{{ asset('assets/js/admin-products-bulk.js') }}"></script>
<script src="{{ asset('assets/js/admin-product-inline-edit.js') }}"></script>
@stop
