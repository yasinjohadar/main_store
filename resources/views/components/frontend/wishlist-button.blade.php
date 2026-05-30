@props(['productId'])

@php
    $inWishlist = in_array((int) $productId, $wishlistIds ?? [], true);
@endphp

<button type="button"
    {{ $attributes->class([
        'btn-product-icon',
        'btn-add-wishlist',
        'added' => $inWishlist,
        'w-icon-heart-full' => $inWishlist,
        'w-icon-heart' => ! $inWishlist,
    ]) }}
    data-product-id="{{ $productId }}"
    title="{{ $inWishlist ? 'إزالة من المفضلة' : 'أضف للمفضلة' }}"
    aria-pressed="{{ $inWishlist ? 'true' : 'false' }}">
</button>
