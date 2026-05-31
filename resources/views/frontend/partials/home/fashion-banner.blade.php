@php
    use App\Models\HomeProductBlock;

    $block = $homeProductBlocks->get(HomeProductBlock::KEY_FASHION_BANNER);
    if (! $block?->is_active || ! $block->banner_image_url) {
        return;
    }

    $discount = $block->getSetting('discount_percent', '25');
    $promoCode = $block->getSetting('promo_code', 'SAVE25');
    $promoTextRaw = $block->getSetting('promo_text', 'استخدم الكود {code} للحصول على أفضل عرض.');
    $codeSpan = '<span class="text-dark bg-white font-weight-bold ls-50 pl-1 pr-1 d-inline-block">' . e($promoCode) . '</span>';
    $promoParts = explode('{code}', (string) $promoTextRaw, 2);
    $promoHtml = count($promoParts) === 2
        ? e($promoParts[0]) . $codeSpan . e($promoParts[1])
        : e($promoTextRaw);
    $shopUrl = $block->resolvedShopUrl();
@endphp

<div class="banner banner-fashion appear-animate br-sm mb-9"
    style="background-image: url({{ $block->banner_image_url }}); background-color: {{ $block->banner_bg_color ?? '#383839' }};">
    <div class="banner-content align-items-center">
        <div class="content-left d-flex align-items-center mb-3">
            <div class="banner-price-info font-weight-bolder text-secondary text-uppercase lh-1 ls-25">
                <sup class="font-weight-bold">%</sup><sub class="font-weight-bold ls-25">خصم</sub>
                {{ $discount }}
            </div>
            <hr class="banner-divider bg-white mt-0 mb-0 ml-8 mr-8">
        </div>
        <div class="content-right d-flex align-items-center flex-1 flex-wrap">
            <div class="banner-info mb-0 mr-auto pl-4 mb-3">
                @if($block->banner_title)
                    <h3 class="banner-title text-white font-weight-bolder text-uppercase ls-25">{{ $block->banner_title }}</h3>
                @endif
                @if($promoTextRaw)
                    <p class="text-white mb-0">{!! $promoHtml !!}</p>
                @endif
            </div>
            <a href="{{ $shopUrl }}"
                class="btn btn-white btn-outline btn-rounded btn-icon-left mb-3">
                تسوق الآن<i class="w-icon-long-arrow-left"></i>
            </a>
        </div>
    </div>
</div>
