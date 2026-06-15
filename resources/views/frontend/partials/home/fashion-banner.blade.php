@php
    use App\Models\HomeProductBlock;

    $block = $homeProductBlocks->get(HomeProductBlock::KEY_FASHION_BANNER);
    if (! $block?->is_active || ! $block->banner_image_url) {
        return;
    }

    $discount = $block->getSetting('discount_percent', '25');
    $promoCode = $block->getSetting('promo_code', 'SAVE25');
    $promoTextRaw = $block->getSetting('promo_text', 'استخدم الكود {code} للحصول على أفضل عرض.');
    $promoParts = explode('{code}', (string) $promoTextRaw, 2);
    $shopUrl = $block->resolvedShopUrl();
@endphp

<section class="home-fashion-banner appear-animate mb-9"
    style="--home-fashion-bg: {{ $block->banner_bg_color ?? '#383839' }};">
    <div class="home-fashion-banner__media"
        style="background-image: url('{{ $block->banner_image_url }}');"></div>
    <div class="home-fashion-banner__shade" aria-hidden="true"></div>

    <div class="home-fashion-banner__content">
        <div class="home-fashion-banner__discount" style="animation-delay: 0.05s;">
            <span class="home-fashion-banner__discount-value">{{ $discount }}</span>
            <span class="home-fashion-banner__discount-meta">
                <span class="home-fashion-banner__discount-unit">%</span>
                <span class="home-fashion-banner__discount-label">خصم</span>
            </span>
        </div>

        <div class="home-fashion-banner__panel" style="animation-delay: 0.15s;">
            @if($block->banner_title)
                <h2 class="home-fashion-banner__title">{{ $block->banner_title }}</h2>
            @endif

            @if($promoTextRaw)
                <p class="home-fashion-banner__promo">
                    @if(count($promoParts) === 2)
                        {{ $promoParts[0] }}
                        <span class="home-fashion-banner__code">{{ $promoCode }}</span>
                        {{ $promoParts[1] }}
                    @else
                        {{ $promoTextRaw }}
                    @endif
                </p>
            @endif
        </div>

        <a href="{{ $shopUrl }}" class="btn btn-primary home-fashion-banner__cta" style="animation-delay: 0.25s;">
            <span>تسوق الآن</span>
            <i class="w-icon-long-arrow-left" aria-hidden="true"></i>
        </a>
    </div>
</section>
