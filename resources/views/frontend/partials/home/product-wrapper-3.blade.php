@php
    use App\Models\HomeProductBlock;

    $block = $homeProductBlocks->get(HomeProductBlock::KEY_PRODUCT_3);
    if (! $block?->is_active) {
        return;
    }
    $products = $blockProducts[HomeProductBlock::KEY_PRODUCT_3] ?? collect();
@endphp

@include('frontend.partials.home.product-wrapper-section', [
    'title' => $block->section_title ?? 'المنزل والمطبخ',
    'products' => $products,
    'bannerImage' => $block->banner_image_url,
    'bannerBg' => $block->banner_bg_color ?? '#EAEFF3',
    'bannerSubtitle' => $block->banner_subtitle,
    'bannerTitle' => $block->banner_title,
    'shopUrl' => $block->resolvedShopUrl(),
    'wrapperClass' => $block->wrapper_class ?? 'product-wrapper-1 product-wrapper-home appear-animate mb-7',
    'variant' => 'wide',
])
