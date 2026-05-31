@php
    $typeLabels = [
        'select' => 'قائمة',
        'color' => 'لون',
        'image' => 'صورة',
    ];
    $selectedIds = $selectedAttributeIds ?? old('attribute_ids', []);
@endphp

<div class="product-attribute-picker">
    @forelse($attributes as $attr)
        @php
            $valuesPayload = $attr->values->map(fn ($v) => [
                'id' => $v->id,
                'value' => $v->value,
                'color_hex' => $v->color_hex,
            ])->values();
        @endphp
        <div class="product-attribute-picker__item">
            <div class="form-check mb-0">
                <input class="form-check-input product-attribute-cb"
                       type="checkbox"
                       name="attribute_ids[]"
                       value="{{ $attr->id }}"
                       id="product_attr_{{ $attr->id }}"
                       {{ in_array($attr->id, $selectedIds) ? 'checked' : '' }}
                       data-attribute-id="{{ $attr->id }}"
                       data-attribute-name="{{ e($attr->name) }}"
                       data-attribute-type="{{ $attr->type }}"
                       data-manage-url="{{ route('admin.attributes.values.index', $attr) }}"
                       data-values='@json($valuesPayload)'>
                <label class="form-check-label" for="product_attr_{{ $attr->id }}">
                    <span class="fw-semibold">{{ $attr->name }}</span>
                    <span class="admin-table__badge admin-table__badge--secondary ms-1">{{ $typeLabels[$attr->type] ?? $attr->type }}</span>
                    <span class="text-muted small">({{ $attr->values->count() }} {{ $attr->values->count() === 1 ? 'قيمة' : 'قيم' }})</span>
                </label>
            </div>
            <div class="product-attribute-picker__values d-none" data-attribute-panel="{{ $attr->id }}"></div>
        </div>
    @empty
        <p class="text-muted small mb-0">
            لا توجد سمات.
            <a href="{{ route('admin.attributes.index') }}">إنشاء سمات من هنا</a>
        </p>
    @endforelse
</div>
