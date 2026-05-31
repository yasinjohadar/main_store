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
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">كافة المنتجات</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card admin-table-card">
                        <div class="admin-table-toolbar admin-table-toolbar--split">
                            <div class="admin-table-toolbar__row">
                                <div class="admin-table-toolbar__start">
                                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                                        <i class="las la-plus me-1"></i> إضافة منتج جديد
                                    </a>
                                    <form id="bulk-products-form" action="{{ route('admin.products.bulk-update') }}" method="POST" class="d-flex align-items-center gap-2 flex-wrap">
                                        @csrf
                                        <select name="action" class="form-select form-select-sm" style="width: 180px;">
                                            <option value="">إجراء جماعي...</option>
                                            <option value="activate">تفعيل المنتجات المحددة</option>
                                            <option value="draft">تحويل إلى مسودة</option>
                                            <option value="hide">إخفاء من المتجر</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-outline-primary" onclick="return confirm('تأكيد تنفيذ الإجراء الجماعي على المنتجات المحددة؟');">تطبيق</button>
                                    </form>
                                    <button type="button" id="btn-compare-selected" class="btn btn-sm btn-outline-success" disabled
                                        data-compare-url="{{ route('admin.products.compare') }}">
                                        <i class="las la-balance-scale me-1"></i> مقارنة المنتجات
                                    </button>
                                </div>

                                <form action="{{ route('admin.products.index') }}" method="GET"
                                    class="admin-table-filter-form d-flex align-items-center gap-2 flex-wrap ms-auto"
                                    data-admin-table-target="#admin-table-products"
                                    data-admin-table-autosubmit>
                                    <input type="text" name="query" class="form-control" style="min-width: 200px; max-width: 260px;"
                                        placeholder="بحث بالاسم أو SKU" value="{{ request('query') }}">
                                    <select name="status" class="form-select" style="width: auto;">
                                        <option value="">كل الحالات</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                                        <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>أرشيف</option>
                                    </select>
                                    <select name="category_id" class="form-select" style="width: auto;">
                                        <option value="">كل التصنيفات</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
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
                                    <a href="{{ route('admin.products.index') }}" class="btn btn-light btn-sm" data-admin-table-reset>مسح</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body p-0" id="admin-table-products" data-admin-table>
                            @include('admin.pages.products.partials.table-body')
                        </div>
                    </div>
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
