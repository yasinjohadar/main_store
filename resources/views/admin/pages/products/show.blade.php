@extends('admin.layouts.master')

@section('page-title')
    عرض المنتج: {{ $product->name }}
@stop

@section('content')
    @php
        $compareIds = collect(session('admin_product_compare', []))->map(fn ($id) => (int) $id);
        $statusLabels = [
            'active' => ['label' => 'نشط', 'class' => 'admin-table__badge--success'],
            'draft' => ['label' => 'مسودة', 'class' => 'admin-table__badge--secondary'],
            'archived' => ['label' => 'أرشيف', 'class' => 'admin-table__badge--danger'],
        ];
        $status = $statusLabels[$product->status] ?? ['label' => $product->status, 'class' => 'admin-table__badge--secondary'];
        $stockQty = $product->track_quantity ? (int) $product->stock_quantity : null;
        $lowThreshold = config('store.low_stock_threshold', 5);
        $primaryUrl = $product->primary_image_url;
    @endphp

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="admin-detail__header page-header-breadcrumb my-4">
                <div>
                    <h5 class="admin-detail__title">عرض المنتج</h5>
                    <p class="admin-detail__subtitle">
                        @if($product->category)
                            <a href="{{ route('admin.categories.show', $product->category) }}" class="text-muted text-decoration-none">
                                {{ $product->category->name }}
                            </a>
                            <span class="mx-1">/</span>
                        @endif
                        {{ $product->name }}
                    </p>
                </div>
                <div class="admin-detail__actions">
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary btn-sm">
                        <i class="las la-pen me-1"></i> تعديل
                    </a>
                    <form action="{{ route('admin.products.compare.add', $product) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-success btn-sm">
                            <i class="las la-balance-scale me-1"></i> إضافة للمقارنة
                        </button>
                    </form>
                    @if($compareIds->contains($product->id))
                        <form action="{{ route('admin.products.compare.remove', $product) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">إزالة من المقارنة</button>
                        </form>
                    @endif
                    @if($compareIds->count() >= 2)
                        <a href="{{ route('admin.products.compare') }}" class="btn btn-info btn-sm">
                            المقارنة ({{ $compareIds->count() }})
                        </a>
                    @endif
                    <a href="{{ route('admin.products.index') }}" class="btn btn-light btn-sm">
                        <i class="las la-arrow-right me-1"></i> العودة
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
                </div>
            @endif
            @if (session('info'))
                <div class="alert alert-info alert-dismissible fade show">{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
                </div>
            @endif

            <div class="admin-detail-card">
                <div class="admin-detail-card__body">
                    <div class="admin-detail-hero">
                        <div class="admin-detail-gallery">
                            <div class="admin-detail-gallery__main">
                                <img src="{{ $primaryUrl }}" alt="{{ $product->name }}" id="admin-product-main-image">
                            </div>
                            @if($product->images->count() > 0)
                                <div class="admin-detail-gallery__thumbs">
                                    @foreach($product->images as $img)
                                        <button type="button"
                                                class="admin-detail-gallery__thumb {{ $loop->first ? 'is-active' : '' }}"
                                                data-image-url="{{ $img->url }}"
                                                aria-label="صورة {{ $loop->iteration }}">
                                            <img src="{{ $img->url }}" alt="">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="admin-detail-profile">
                            <div class="admin-detail-profile__head">
                                <div>
                                    <h1 class="admin-detail-profile__name">{{ $product->name }}</h1>
                                    <div class="admin-detail-profile__badges">
                                        <span class="admin-table__badge {{ $status['class'] }}">{{ $status['label'] }}</span>
                                        @if($product->is_featured)
                                            <span class="admin-table__badge admin-table__badge--featured">مميز</span>
                                        @endif
                                        @if($product->is_visible)
                                            <span class="admin-table__badge admin-table__badge--success">ظاهر في المتجر</span>
                                        @else
                                            <span class="admin-table__badge admin-table__badge--secondary">مخفي</span>
                                        @endif
                                        @if($product->has_discount)
                                            <span class="admin-table__badge admin-table__badge--warning">-{{ $product->discount_percentage }}%</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="admin-detail-stats">
                                <div class="admin-detail-stat">
                                    <span class="admin-detail-stat__label">السعر</span>
                                    <span class="admin-detail-stat__value admin-detail-stat__value--price">
                                        {{ $currencyService->format((float) $product->effective_price) }}
                                    </span>
                                    @if($product->compare_at_price)
                                        <span class="admin-detail-stat__sub">
                                            <del>{{ $currencyService->format((float) $product->compare_at_price) }}</del>
                                        </span>
                                    @endif
                                </div>
                                <div class="admin-detail-stat">
                                    <span class="admin-detail-stat__label">المخزون</span>
                                    <span class="admin-detail-stat__value {{ $product->in_stock ? 'admin-detail-stat__value--success' : 'admin-detail-stat__value--danger' }}">
                                        @if($stockQty !== null)
                                            {{ $stockQty }}
                                        @else
                                            غير محدود
                                        @endif
                                    </span>
                                    @if($stockQty !== null)
                                        <span class="admin-detail-stat__sub">
                                            @if($stockQty <= 0)
                                                منتهي
                                            @elseif($stockQty <= $lowThreshold)
                                                مخزون منخفض
                                            @else
                                                متوفر
                                            @endif
                                        </span>
                                    @endif
                                </div>
                                <div class="admin-detail-stat">
                                    <span class="admin-detail-stat__label">المراجعات</span>
                                    <span class="admin-detail-stat__value">{{ $product->reviews->count() }}</span>
                                    @if($product->reviews->count() > 0)
                                        <span class="admin-detail-stat__sub">
                                            <i class="las la-star text-warning"></i>
                                            {{ number_format($product->reviews_avg_rating, 1) }} / 5
                                        </span>
                                    @endif
                                </div>
                                <div class="admin-detail-stat">
                                    <span class="admin-detail-stat__label">المتغيرات</span>
                                    <span class="admin-detail-stat__value">{{ $product->variants->count() }}</span>
                                </div>
                            </div>

                            <div class="admin-detail-meta">
                                <div class="admin-detail-meta__item">
                                    <span class="admin-detail-meta__label">SKU</span>
                                    <span class="admin-detail-meta__value">
                                        @if($product->sku)
                                            <code class="admin-table__sku">{{ $product->sku }}</code>
                                        @else
                                            <span class="admin-table__muted">—</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="admin-detail-meta__item">
                                    <span class="admin-detail-meta__label">التصنيف</span>
                                    <span class="admin-detail-meta__value">
                                        @if($product->category)
                                            <a href="{{ route('admin.categories.show', $product->category) }}">{{ $product->category->name }}</a>
                                        @else
                                            —
                                        @endif
                                    </span>
                                </div>
                                @if($product->brand)
                                    <div class="admin-detail-meta__item">
                                        <span class="admin-detail-meta__label">العلامة</span>
                                        <span class="admin-detail-meta__value">{{ $product->brand->name }}</span>
                                    </div>
                                @endif
                                <div class="admin-detail-meta__item">
                                    <span class="admin-detail-meta__label">الرابط</span>
                                    <span class="admin-detail-meta__value">
                                        @if($product->slug)
                                            <code class="admin-table__slug">{{ $product->slug }}</code>
                                        @else
                                            —
                                        @endif
                                    </span>
                                </div>
                                <div class="admin-detail-meta__item">
                                    <span class="admin-detail-meta__label">تاريخ الإنشاء</span>
                                    <span class="admin-detail-meta__value">{{ $product->created_at?->format('Y-m-d H:i') ?? '—' }}</span>
                                </div>
                                <div class="admin-detail-meta__item">
                                    <span class="admin-detail-meta__label">آخر تحديث</span>
                                    <span class="admin-detail-meta__value">{{ $product->updated_at?->format('Y-m-d H:i') ?? '—' }}</span>
                                </div>
                            </div>

                            @if($product->short_description)
                                <div class="mb-3">
                                    <div class="admin-detail-block__title">وصف مختصر</div>
                                    <p class="admin-detail-prose mb-0">{{ $product->short_description }}</p>
                                </div>
                            @endif

                            @if($product->description)
                                <div>
                                    <div class="admin-detail-block__title">الوصف الكامل</div>
                                    <div class="admin-detail-prose">{!! $product->description !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($product->variants->count() > 0)
                <div class="admin-detail-card admin-detail-section">
                    <div class="admin-detail-card__header">
                        <h6><i class="las la-layer-group me-1"></i> المتغيرات ({{ $product->variants->count() }})</h6>
                    </div>
                    <div class="admin-detail-card__body p-0">
                        <div class="table-responsive admin-table-wrap border-0 rounded-0">
                            <table class="table admin-table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>SKU</th>
                                        <th>السعر</th>
                                        <th>الكمية</th>
                                        <th>الخصائص</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->variants as $v)
                                        <tr>
                                            <td>
                                                @if($v->sku)
                                                    <code class="admin-table__sku">{{ $v->sku }}</code>
                                                @else
                                                    <span class="admin-table__muted">—</span>
                                                @endif
                                            </td>
                                            <td class="admin-table__price">
                                                {{ $v->effective_price ? $currencyService->format((float) $v->effective_price) : '—' }}
                                            </td>
                                            <td class="admin-table__index">{{ $v->stock_quantity }}</td>
                                            <td>{{ $v->attributeValues->pluck('value')->join(' / ') ?: '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <div class="admin-detail-card admin-detail-section">
                <div class="admin-detail-card__header">
                    <h6><i class="las la-history me-1"></i> تاريخ حركات المخزون</h6>
                    @if($product->stockMovements->isNotEmpty())
                        <span class="admin-table__muted small">آخر {{ min(20, $product->stockMovements->count()) }} حركة</span>
                    @endif
                </div>
                <div class="admin-detail-card__body p-0">
                    @if($product->stockMovements->isEmpty())
                        <div class="admin-detail-empty">
                            <i class="las la-box"></i>
                            <p class="mb-0">لم يتم تسجيل أي حركات مخزون لهذا المنتج بعد.</p>
                        </div>
                    @else
                        <div class="table-responsive admin-table-wrap border-0 rounded-0">
                            <table class="table admin-table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>التغيير</th>
                                        <th>السبب</th>
                                        <th>مرجع</th>
                                        <th>ملاحظات</th>
                                        <th>بواسطة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->stockMovements->sortByDesc('created_at')->take(20) as $movement)
                                        <tr>
                                            <td class="admin-table__muted">{{ $movement->created_at?->format('Y-m-d H:i') }}</td>
                                            <td>
                                                @php $q = (int) $movement->quantity_change; @endphp
                                                <span class="admin-table__badge {{ $q > 0 ? 'admin-table__badge--success' : 'admin-table__badge--danger' }}">
                                                    {{ $q > 0 ? '+' : '' }}{{ $q }}
                                                </span>
                                            </td>
                                            <td>{{ $movement->reason ?? '—' }}</td>
                                            <td>
                                                @if($movement->reference_type === \App\Models\Order::class && $movement->reference_id)
                                                    <a href="{{ route('admin.orders.show', $movement->reference_id) }}" class="small">
                                                        طلب #{{ optional(\App\Models\Order::find($movement->reference_id))->order_number ?? $movement->reference_id }}
                                                    </a>
                                                @else
                                                    <span class="admin-table__muted">—</span>
                                                @endif
                                            </td>
                                            <td class="small admin-table__muted">{{ $movement->note ? Str::limit($movement->note, 80) : '—' }}</td>
                                            <td class="small">{{ $movement->user?->name ?? 'النظام' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="admin-detail-card admin-detail-section">
                <div class="admin-detail-card__header">
                    <h6><i class="las la-star me-1"></i> المراجعات ({{ $product->reviews->count() }})</h6>
                    <a href="{{ route('admin.reviews.index', ['product_id' => $product->id]) }}" class="btn btn-sm btn-primary">
                        <i class="las la-external-link-alt me-1"></i> عرض المراجعات
                    </a>
                </div>
                @if($product->reviews->isEmpty())
                    <div class="admin-detail-card__body">
                        <div class="admin-detail-empty py-4">
                            <i class="las la-comment-dots"></i>
                            <p class="mb-0">لا توجد مراجعات لهذا المنتج حتى الآن.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop

@section('script')
<script>
(function () {
    var mainImg = document.getElementById('admin-product-main-image');
    if (!mainImg) return;

    document.querySelectorAll('.admin-detail-gallery__thumb').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var url = this.getAttribute('data-image-url');
            if (!url) return;
            mainImg.src = url;
            document.querySelectorAll('.admin-detail-gallery__thumb').forEach(function (t) {
                t.classList.remove('is-active');
            });
            this.classList.add('is-active');
        });
    });
})();
</script>
@stop
