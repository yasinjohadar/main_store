<div class="table-responsive admin-table-wrap">
    <table class="table admin-table table-striped table-hover align-middle table-nowrap mb-0">
        <thead>
            <tr>
                <th style="width: 40px;">
                    <input type="checkbox" class="admin-table__checkbox" id="select-all-products">
                </th>
                <th scope="col" style="width: 48px;">#</th>
                <th scope="col">المنتج</th>
                <th scope="col">SKU</th>
                <th scope="col">التصنيف</th>
                <th scope="col">السعر</th>
                <th scope="col">المخزون</th>
                <th scope="col" style="width: 100px;">الحالة</th>
                <th scope="col" style="width: 160px;">العمليات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                @php
                    $updateUrl = route('admin.products.inline-update', $product);
                    $statusClass = match ($product->status) {
                        'active' => 'admin-table__badge--success',
                        'draft' => 'admin-table__badge--secondary',
                        default => 'admin-table__badge--danger',
                    };
                    $statusLabel = match ($product->status) {
                        'active' => 'نشط',
                        'draft' => 'مسودة',
                        'archived' => 'أرشيف',
                        default => $product->status,
                    };
                @endphp
                <tr data-product-row="{{ $product->id }}">
                    <td>
                        <input type="checkbox" class="admin-table__checkbox" name="ids[]" form="bulk-products-form" value="{{ $product->id }}">
                    </td>
                    <th scope="row" class="admin-table__index">{{ $products->firstItem() + $loop->index }}</th>
                    <td>
                        <div class="admin-table__user">
                            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="admin-table__thumb">
                            <div class="min-w-0 flex-grow-1">
                                <div class="admin-table__name-row">
                                    <a href="{{ route('admin.products.show', $product) }}"
                                       class="admin-table__user-name admin-table__editable-name text-truncate"
                                       data-inline-field="name"
                                       data-update-url="{{ $updateUrl }}"
                                       data-original="{{ e($product->name) }}"
                                       title="عرض تفاصيل المنتج">
                                        {{ $product->name }}
                                    </a>
                                    <button type="button"
                                            class="admin-table__inline-edit-btn admin-table__name-edit"
                                            title="تعديل الاسم"
                                            aria-label="تعديل اسم المنتج">
                                        <i class="las la-pen"></i>
                                    </button>
                                </div>
                                @if($product->is_featured)
                                    <span class="admin-table__badge admin-table__badge--featured mt-1">مميز</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($product->sku)
                            <code class="admin-table__sku">{{ $product->sku }}</code>
                        @else
                            <span class="admin-table__muted">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="admin-table__badge admin-table__badge--role admin-table__editable admin-table__editable--category"
                              role="button"
                              tabindex="0"
                              data-inline-field="category_id"
                              data-update-url="{{ $updateUrl }}"
                              data-original="{{ $product->category_id ?? '' }}"
                              title="انقر لتغيير التصنيف">
                            {{ $product->category?->name ?? '—' }}
                        </span>
                    </td>
                    <td>
                        <span class="admin-table__price admin-table__editable admin-table__editable--price"
                              role="button"
                              tabindex="0"
                              data-inline-field="price"
                              data-update-url="{{ $updateUrl }}"
                              data-original="{{ $product->effective_price }}"
                              @if($product->variants_count > 0) title="السعر من المتغير الافتراضي — انقر للتعديل" @else title="انقر لتعديل السعر" @endif>
                            {{ $currencyService->format((float) $product->effective_price) }}
                        </span>
                    </td>
                    <td>
                        @if($product->track_quantity)
                            @php
                                $qty = (int) $product->stock_quantity;
                                $threshold = config('store.low_stock_threshold', 5);
                            @endphp
                            <span class="admin-table__index">{{ $qty }}</span>
                            @if($qty <= 0)
                                <span class="admin-table__badge admin-table__badge--danger ms-1">منتهي</span>
                            @elseif($qty <= $threshold)
                                <span class="admin-table__badge admin-table__badge--warning ms-1">منخفض</span>
                            @endif
                        @else
                            <span class="admin-table__muted">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="admin-table__badge {{ $statusClass }} admin-table__editable admin-table__editable--status"
                              role="button"
                              tabindex="0"
                              data-inline-field="status"
                              data-update-url="{{ $updateUrl }}"
                              data-original="{{ $product->status }}"
                              data-status-label="{{ $statusLabel }}"
                              title="انقر لتغيير الحالة">
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td>
                        <div class="admin-table__actions">
                            <a class="admin-table__action admin-table__action--view"
                                href="{{ route('admin.products.show', $product) }}"
                                title="عرض المنتج">
                                <i class="las la-eye"></i>
                            </a>
                            <a class="admin-table__action admin-table__action--edit"
                                href="{{ route('admin.products.edit', $product) }}"
                                title="تعديل المنتج">
                                <i class="las la-pen"></i>
                            </a>
                            <form action="{{ route('admin.products.duplicate', $product) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('استنساخ هذا المنتج؟');">
                                @csrf
                                <button type="submit" class="admin-table__action admin-table__action--duplicate border-0"
                                        title="استنساخ">
                                    <i class="las la-copy"></i>
                                </button>
                            </form>
                            <x-admin.delete-trigger
                                :action="route('admin.products.destroy', $product)"
                                title="حذف المنتج"
                                :item="$product->name"
                                message="هل تريد حذف هذا المنتج نهائياً؟"
                                button-class="btn-danger btn-sm"
                                icon="las la-trash"
                                title-attr="حذف المنتج"
                            />
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">
                        <div class="admin-table-empty">
                            <i class="las la-box-open"></i>
                            <p class="mb-0 fw-medium">لا توجد منتجات</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="p-3 border-top admin-table-pagination">
    {{ $products->withQueryString()->links() }}
</div>
