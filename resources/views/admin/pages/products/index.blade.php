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
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">إضافة منتج جديد</a>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3 flex-wrap">
                            <div class="flex-shrink-0 d-flex align-items-center gap-2">
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
                                <button type="button" id="btn-compare-selected" class="btn btn-sm btn-outline-success" disabled>مقارنة المنتجات</button>
                            </div>
                            <div class="flex-shrink-0 ms-auto">
                                <form action="{{ route('admin.products.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                                    <input style="width: 200px" type="text" name="query" class="form-control" placeholder="بحث بالاسم أو SKU" value="{{ request('query') }}">
                                    <select name="status" class="form-select" style="width: 140px;">
                                        <option value="">كل الحالات</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                                        <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>أرشيف</option>
                                    </select>
                                    <select name="category_id" class="form-select" style="width: 180px;">
                                        <option value="">كل التصنيفات</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-secondary">بحث</button>
                                    <a href="{{ route('admin.products.index') }}" class="btn btn-danger">مسح</a>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 40px;">
                                                <input type="checkbox" id="select-all-products">
                                            </th>
                                            <th style="width: 50px;">#</th>
                                            <th style="min-width: 70px;">الصورة</th>
                                            <th style="min-width: 200px;">الاسم</th>
                                            <th>SKU</th>
                                            <th>التصنيف</th>
                                            <th>السعر</th>
                                            <th>المخزون</th>
                                            <th>الحالة</th>
                                            <th style="min-width: 160px;">العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($products as $product)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="ids[]" form="bulk-products-form" value="{{ $product->id }}">
                                                </td>
                                                <td>{{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}</td>
                                                <td>
                                                    @if($product->primary_image_url)
                                                        <img src="{{ $product->primary_image_url }}" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.products.show', $product) }}" class="text-decoration-none">{{ $product->name }}</a>
                                                    @if($product->is_featured)
                                                        <span class="badge bg-warning text-dark ms-1">مميز</span>
                                                    @endif
                                                </td>
                                                <td><code>{{ $product->sku ?? '-' }}</code></td>
                                                <td>{{ $product->category->name ?? '-' }}</td>
                                                <td>{{ $currencyService->format((float) $product->effective_price) }}</td>
                                                <td>
                                                    @if($product->track_quantity)
                                                        @php
                                                            $qty = (int) $product->stock_quantity;
                                                            $threshold = config('store.low_stock_threshold', 5);
                                                        @endphp
                                                        {{ $qty }}
                                                        @if($qty <= 0)
                                                            <span class="badge bg-danger ms-1">منتهي</span>
                                                        @elseif($qty <= $threshold)
                                                            <span class="badge bg-warning text-dark ms-1">منخفض</span>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($product->status == 'active')
                                                        <span class="badge bg-success">نشط</span>
                                                    @elseif($product->status == 'draft')
                                                        <span class="badge bg-secondary">مسودة</span>
                                                    @else
                                                        <span class="badge bg-danger">أرشيف</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-info" title="عرض"><i class="bi bi-eye"></i></a>
                                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-primary" title="تعديل"><i class="bi bi-pencil"></i></a>
                                                    <form action="{{ route('admin.products.duplicate', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('استنساخ هذا المنتج؟');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="استنساخ"><i class="bi bi-files"></i></button>
                                                    </form>
                                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف المنتج؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="حذف"><i class="bi bi-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center py-4 text-muted">لا توجد منتجات</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">{{ $products->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@section('script')
<script>
    (function () {
        const selectAll = document.getElementById('select-all-products');
        const compareBtn = document.getElementById('btn-compare-selected');

        function getProductCheckboxes() {
            return Array.prototype.slice.call(document.querySelectorAll('input[name="ids[]"]'));
        }

        function updateCompareState() {
            if (!compareBtn) {
                return;
            }
            const checkboxes = getProductCheckboxes();
            const selectedIds = checkboxes.filter(cb => cb.checked).map(cb => cb.value);
            compareBtn.disabled = selectedIds.length < 2;
            compareBtn.dataset.selectedIds = JSON.stringify(selectedIds);
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                const checked = this.checked;
                getProductCheckboxes().forEach(function (cb) {
                    cb.checked = checked;
                });
                updateCompareState();
            });
        }

        getProductCheckboxes().forEach(function (cb) {
            cb.addEventListener('change', updateCompareState);
        });

        if (compareBtn) {
            compareBtn.addEventListener('click', function () {
                const ids = JSON.parse(this.dataset.selectedIds || '[]');
                if (!ids || ids.length < 2) {
                    return;
                }
                const params = new URLSearchParams();
                ids.forEach(function (id) {
                    params.append('ids[]', id);
                });
                window.location.href = "{{ route('admin.products.compare') }}" + '?' + params.toString();
            });
        }

        updateCompareState();
    })();
</script>
@endsection
@stop
