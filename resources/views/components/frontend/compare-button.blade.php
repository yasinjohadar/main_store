@props(['productId'])

@php
    $inCompare = in_array((int) $productId, $compareIds ?? [], true);
@endphp

<button type="button"
    {{ $attributes->class([
        'btn-product-icon',
        'btn-add-compare',
        'added' => $inCompare,
        'w-icon-check-solid' => $inCompare,
        'w-icon-compare' => ! $inCompare,
    ]) }}
    data-product-id="{{ $productId }}"
    title="{{ $inCompare ? 'إزالة من المقارنة' : 'أضف للمقارنة' }}"
    aria-pressed="{{ $inCompare ? 'true' : 'false' }}">
</button>
