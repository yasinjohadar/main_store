@extends('admin.layouts.master')

@section('page-title')
    قائمة التصنيفات
@stop

@section('css')
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
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">كافة التصنيفات</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card admin-table-card">
                        <div class="admin-table-toolbar">
                            @can('category-create')
                                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
                                    <i class="las la-plus me-1"></i> إضافة تصنيف جديد
                                </a>
                            @endcan

                            <form action="{{ route('admin.categories.index') }}" method="GET"
                                class="admin-table-filter-form d-flex align-items-center gap-2 flex-wrap ms-auto"
                                data-admin-table-target="#admin-table-categories"
                                data-admin-table-autosubmit>
                                <input type="text" name="query" class="form-control" style="min-width: 220px; max-width: 280px;"
                                    placeholder="بحث بالاسم أو الوصف" value="{{ request('query') }}">

                                <select name="status" class="form-select" style="width: auto;">
                                    <option value="">كل الحالات</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                </select>

                                <select name="parent_id" class="form-select" style="width: auto;">
                                    <option value="">كل التصنيفات</option>
                                    <option value="null" {{ request('parent_id') == 'null' ? 'selected' : '' }}>رئيسية فقط</option>
                                    @foreach($parentCategories as $parent)
                                        <option value="{{ $parent->id }}" {{ request('parent_id') == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <select name="per_page" class="form-select" style="width: auto;" title="عدد العناصر في الصفحة">
                                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 / صفحة</option>
                                    <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 / صفحة</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 / صفحة</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / صفحة</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 / صفحة</option>
                                </select>

                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="las la-search"></i>
                                </button>
                                <a href="{{ route('admin.categories.index') }}" class="btn btn-light btn-sm" data-admin-table-reset>مسح</a>
                            </form>
                        </div>

                        <div class="card-body p-0" id="admin-table-categories" data-admin-table>
                            @include('admin.pages.categories.partials.table-body')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
