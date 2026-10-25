@php
    use App\Models\HomeProductBlock;

    $block = $homeProductBlocks->get(HomeProductBlock::KEY_PRODUCT_2);
    if (! $block?->is_active) {
        return;
    }
    $products = $blockProducts[HomeProductBlock::KEY_PRODUCT_2] ?? collect();
@endphp

@include('frontend.partials.home.product-wrapper-section', [
    'title' => $block->section_title ?? 'الإلكترونيات',
    'products' => $products,
    'bannerImage' => $block->banner_image_url,
    'bannerBg' => $block->banner_bg_color ?? '#252525',
    'bannerSubtitle' => $block->banner_subtitle,
    'bannerTitle' => $block->banner_title,
    'shopUrl' => $block->resolvedShopUrl(),
    'wrapperClass' => $block->wrapper_class ?? 'product-wrapper-1 appear-animate mb-8',
    'variant' => 'band',
])
