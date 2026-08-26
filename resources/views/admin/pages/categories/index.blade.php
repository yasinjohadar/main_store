@extends('admin.layouts.master')

@section('page-title')
    قائمة التصنيفات
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

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="las la-folder-open"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>كافة التصنيفات</h1>
                        <p>إدارة تصنيفات المتجر الرئيسية والفرعية</p>
                    </div>
                </div>
                @can('category-create')
                    <div class="admin-page-banner-actions">
                        <a href="{{ route('admin.categories.create') }}" class="admin-btn admin-btn-light">
                            <i class="las la-plus"></i>
                            إضافة تصنيف جديد
                        </a>
                    </div>
                @endcan
            </div>

            <div class="admin-report-stats">
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-blue">
                    <span class="admin-report-stat-icon"><i class="las la-folder"></i></span>
                    <span class="admin-report-stat-label">إجمالي التصنيفات</span>
                    <span class="admin-report-stat-value">{{ $categoryStats['total'] ?? 0 }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-green">
                    <span class="admin-report-stat-icon"><i class="las la-check-circle"></i></span>
                    <span class="admin-report-stat-label">نشطة</span>
                    <span class="admin-report-stat-value">{{ $categoryStats['active'] ?? 0 }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-cyan">
                    <span class="admin-report-stat-icon"><i class="las la-sitemap"></i></span>
                    <span class="admin-report-stat-label">رئيسية</span>
                    <span class="admin-report-stat-value">{{ $categoryStats['parents'] ?? 0 }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-amber">
                    <span class="admin-report-stat-icon"><i class="las la-layer-group"></i></span>
                    <span class="admin-report-stat-label">فرعية</span>
                    <span class="admin-report-stat-value">{{ $categoryStats['children'] ?? 0 }}</span>
                </div>
            </div>

            <div class="admin-page-card admin-table-card">
                <div class="card-toolbar">
                    <form action="{{ route('admin.categories.index') }}" method="GET"
                        class="admin-filters admin-table-filter-form"
                        data-admin-table-target="#admin-table-categories"
                        data-admin-table-autosubmit>
                        <div class="search-input-wrap">
                            <i class="las la-search"></i>
                            <input type="text" name="query" class="form-control"
                                placeholder="بحث بالاسم أو الوصف" value="{{ request('query') }}" autocomplete="off">
                        </div>

                        <select name="status" class="form-select admin-filter-select">
                            <option value="">كل الحالات</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                        </select>

                        <select name="parent_id" class="form-select admin-filter-select">
                            <option value="">كل التصنيفات</option>
                            <option value="null" {{ request('parent_id') == 'null' ? 'selected' : '' }}>رئيسية فقط</option>
                            @foreach($parentCategories as $parent)
                                <option value="{{ $parent->id }}" {{ request('parent_id') == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->name }}
                                </option>
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
                        <a href="{{ route('admin.categories.index') }}" class="admin-btn admin-btn-secondary admin-btn-sm" data-admin-table-reset>مسح</a>
                    </form>
                </div>

                <div class="admin-table-wrap" id="admin-table-categories" data-admin-table>
                    @include('admin.pages.categories.partials.table-body')
                </div>
            </div>

        </div>
    </div>
@stop
