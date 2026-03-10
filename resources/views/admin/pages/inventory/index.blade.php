@extends('admin.layouts.master')

@section('page-title')
    إدارة المخزون
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
                    <h5 class="page-title fs-21 mb-1">إدارة المخزون</h5>
                    <p class="text-muted mb-0 small">
                        راقب كميات المنتجات، عدّل الكميات يدوياً، وتعرّف على المنتجات منخفضة المخزون.
                        (الحد الافتراضي لانخفاض المخزون: {{ $lowStockThreshold }})
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap gap-2 align-items-center">
                            <form action="{{ route('admin.inventory.index') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-center w-100">
                                <input type="text" name="query" class="form-control" style="max-width: 220px;" placeholder="بحث بالاسم أو SKU" value="{{ request('query') }}">
                                <select name="category_id" class="form-select" style="max-width: 200px;">
                                    <option value="">كل التصنيفات</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <select name="stock_status" class="form-select" style="max-width: 200px;">
                                    <option value="">كل حالات المخزون</option>
                                    <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>منتهي المخزون</option>
                                    <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>منخفض المخزون</option>
                                    <option value="in" {{ request('stock_status') == 'in' ? 'selected' : '' }}>مخزون جيد</option>
                                </select>
                                <button type="submit" class="btn btn-secondary">تصفية</button>
                                <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary">مسح</a>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th style="min-width: 220px;">المنتج</th>
                                            <th>التصنيف</th>
                                            <th>SKU</th>
                                            <th>الكمية الحالية</th>
                                            <th>حالة المخزون</th>
                                            <th style="width: 220px;">تعديل الكمية</th>
                                            <th style="width: 80px;">رابط المنتج</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($products as $product)
                                            @php
                                                $qty = (int) $product->stock_quantity;
                                                $statusLabel = 'جيد';
                                                $statusClass = 'bg-success';
                                                if ($qty <= 0) {
                                                    $statusLabel = 'منتهي';
                                                    $statusClass = 'bg-danger';
                                                } elseif ($qty <= $lowStockThreshold) {
                                                    $statusLabel = 'منخفض';
                                                    $statusClass = 'bg-warning text-dark';
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        @if($product->primary_image_url)
                                                            <img src="{{ $product->primary_image_url }}" alt="" style="width: 42px; height: 42px; object-fit: cover; border-radius: 4px;">
                                                        @endif
                                                        <div>
                                                            <div class="fw-semibold">{{ $product->name }}</div>
                                                            <div class="text-muted small">#{{ $product->id }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $product->category->name ?? '—' }}</td>
                                                <td><code>{{ $product->sku ?? '—' }}</code></td>
                                                <td>{{ $qty }}</td>
                                                <td>
                                                    <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                                </td>
                                                <td>
                                                    <form action="{{ route('admin.inventory.adjust', $product) }}" method="POST" class="d-flex align-items-center gap-1">
                                                        @csrf
                                                        <input type="number" name="new_quantity" min="0" class="form-control form-control-sm" style="width: 90px;" value="{{ $qty }}">
                                                        <input type="text" name="note" class="form-control form-control-sm" style="width: 160px;" placeholder="ملاحظة (اختياري)">
                                                        <button type="submit" class="btn btn-sm btn-primary">حفظ</button>
                                                    </form>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-secondary">تعديل</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4 text-muted">
                                                    لا توجد منتجات تتبع المخزون حالياً.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($products->hasPages())
                                <div class="mt-3">{{ $products->links() }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

