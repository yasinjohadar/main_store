@php
    $typeLabels = [
        'percentage' => 'نسبة مئوية',
        'fixed_amount' => 'مبلغ ثابت',
        'buy_x_get_y' => 'اشترِ واحصل على',
    ];
    $statusClasses = [
        'active' => 'admin-table__badge--success',
        'inactive' => 'admin-table__badge--secondary',
        'expired' => 'admin-table__badge--danger',
    ];
    $statusLabels = [
        'active' => 'نشط',
        'inactive' => 'غير نشط',
        'expired' => 'منتهي',
    ];
    $applicableLabels = [
        'entire_store' => 'المتجر كامل',
        'specific_products' => 'منتجات محددة',
        'specific_categories' => 'تصنيفات محددة',
    ];
@endphp

<div class="table-responsive admin-table-wrap">
    <table class="table admin-table table-striped table-hover align-middle table-nowrap mb-0">
        <thead>
            <tr>
                <th scope="col" style="width: 48px;">#</th>
                <th scope="col">الكود</th>
                <th scope="col">الاسم</th>
                <th scope="col">النوع</th>
                <th scope="col">القيمة</th>
                <th scope="col">الاستخدام</th>
                <th scope="col" style="width: 110px;">الحالة</th>
                <th scope="col">البداية</th>
                <th scope="col">النهاية</th>
                <th scope="col">ينطبق على</th>
                <th scope="col" style="width: 120px;">العمليات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($coupons as $coupon)
                @php
                    $updateUrl = route('admin.coupons.inline-update', $coupon);
                    $statusClass = $statusClasses[$coupon->status] ?? 'admin-table__badge--secondary';
                    $statusLabel = $statusLabels[$coupon->status] ?? $coupon->status;
                    $typeLabel = $typeLabels[$coupon->type] ?? $coupon->type;
                    $valueFormatted = $coupon->type === 'percentage'
                        ? rtrim(rtrim(number_format((float) $coupon->value, 2), '0'), '.') . '%'
                        : number_format((float) $coupon->value, 2) . ' ر.س';
                    $used = (int) ($coupon->usages_count ?? 0);
                    $usageDisplay = $coupon->usage_limit !== null ? "{$used} / {$coupon->usage_limit}" : "{$used} / —";
                    $applicableTo = $coupon->applicable_to ?? 'entire_store';
                    if ($applicableTo === 'specific_products') {
                        $applicableLabel = 'منتجات محددة (' . $coupon->products->count() . ')';
                    } elseif ($applicableTo === 'specific_categories') {
                        $applicableLabel = 'تصنيفات محددة (' . $coupon->categories->count() . ')';
                    } else {
                        $applicableLabel = 'المتجر كامل';
                    }
                @endphp
                <tr data-coupon-row="{{ $coupon->id }}">
                    <th scope="row" class="admin-table__index">{{ $coupons->firstItem() + $loop->index }}</th>
                    <td>
                        <code class="admin-table__sku admin-table__editable admin-table__editable--code"
                              role="button"
                              tabindex="0"
                              data-inline-field="code"
                              data-update-url="{{ $updateUrl }}"
                              data-original="{{ e($coupon->code) }}"
                              title="انقر لتعديل الكود">{{ $coupon->code }}</code>
                    </td>
                    <td>
                        <span class="admin-table__editable admin-table__editable--name fw-semibold"
                              role="button"
                              tabindex="0"
                              data-inline-field="name"
                              data-update-url="{{ $updateUrl }}"
                              data-original="{{ e($coupon->name) }}"
                              title="انقر لتعديل الاسم">{{ $coupon->name }}</span>
                    </td>
                    <td>
                        <span class="admin-table__badge admin-table__badge--role admin-table__editable admin-table__editable--type"
                              role="button"
                              tabindex="0"
                              data-inline-field="type"
                              data-update-url="{{ $updateUrl }}"
                              data-original="{{ $coupon->type }}"
                              title="انقر لتغيير النوع">{{ $typeLabel }}</span>
                    </td>
                    <td>
                        <span class="admin-table__price admin-table__editable admin-table__editable--value"
                              role="button"
                              tabindex="0"
                              data-inline-field="value"
                              data-coupon-type="{{ $coupon->type }}"
                              data-update-url="{{ $updateUrl }}"
                              data-original="{{ $coupon->value }}"
                              title="انقر لتعديل القيمة">{{ $valueFormatted }}</span>
                    </td>
                    <td>
                        <span class="admin-table__muted">{{ $used }} /</span>
                        <span class="admin-table__editable admin-table__editable--usage-limit"
                              role="button"
                              tabindex="0"
                              data-inline-field="usage_limit"
                              data-update-url="{{ $updateUrl }}"
                              data-original="{{ $coupon->usage_limit ?? '' }}"
                              title="انقر لتعديل حد الاستخدام">{{ $coupon->usage_limit ?? '—' }}</span>
                    </td>
                    <td>
                        <span class="admin-table__badge {{ $statusClass }} admin-table__editable admin-table__editable--status"
                              role="button"
                              tabindex="0"
                              data-inline-field="status"
                              data-update-url="{{ $updateUrl }}"
                              data-original="{{ $coupon->status }}"
                              data-status-label="{{ $statusLabel }}"
                              title="انقر لتغيير الحالة">{{ $statusLabel }}</span>
                    </td>
                    <td>
                        <span class="admin-table__editable admin-table__editable--starts-at admin-table__muted"
                              role="button"
                              tabindex="0"
                              data-inline-field="starts_at"
                              data-update-url="{{ $updateUrl }}"
                              data-original="{{ $coupon->starts_at?->format('Y-m-d') ?? '' }}"
                              title="انقر لتعديل تاريخ البداية">{{ $coupon->starts_at ? $coupon->starts_at->format('Y-m-d') : '—' }}</span>
                    </td>
                    <td>
                        <span class="admin-table__editable admin-table__editable--expires-at admin-table__muted"
                              role="button"
                              tabindex="0"
                              data-inline-field="expires_at"
                              data-update-url="{{ $updateUrl }}"
                              data-original="{{ $coupon->expires_at?->format('Y-m-d') ?? '' }}"
                              title="انقر لتعديل تاريخ الانتهاء">{{ $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '—' }}</span>
                    </td>
                    <td>
                        <span class="admin-table__badge admin-table__badge--role admin-table__editable admin-table__editable--applicable"
                              role="button"
                              tabindex="0"
                              data-inline-field="applicable_to"
                              data-update-url="{{ $updateUrl }}"
                              data-original="{{ $applicableTo }}"
                              title="انقر لتغيير نطاق التطبيق">{{ $applicableLabel }}</span>
                    </td>
                    <td>
                        <div class="admin-table__actions">
                            <a class="admin-table__action admin-table__action--edit"
                               href="{{ route('admin.coupons.edit', $coupon) }}"
                               title="تعديل الكوبون">
                                <i class="las la-pen"></i>
                            </a>
                            <x-admin.delete-trigger
                                :action="route('admin.coupons.destroy', $coupon)"
                                title="حذف الكوبون"
                                :item="$coupon->code"
                                message="هل تريد حذف هذا الكوبون؟"
                                button-class="btn-danger btn-sm"
                                icon="las la-trash"
                                title-attr="حذف الكوبون"
                            />
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11">
                        <div class="admin-table-empty">
                            <i class="las la-ticket-alt"></i>
                            <p class="mb-2 fw-medium">لا توجد كوبونات</p>
                            <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary btn-sm">إضافة كوبون</a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="p-3 border-top admin-table-pagination">
    {{ $coupons->withQueryString()->links() }}
</div>
