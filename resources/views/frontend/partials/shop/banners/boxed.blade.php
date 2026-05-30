<section class="store-shop-banner store-shop-banner--boxed shop-boxed-banner appear-animate mb-6"
    style="--banner-accent: {{ $accent }}; background-image: url('{{ $bannerImage }}');">
    <div class="store-shop-banner--boxed__content banner-content">
        @if($badge)
            <span class="store-shop-banner__badge">{{ $badge }}</span>
        @endif
        <p class="store-shop-banner__subtitle">{{ $subtitle }}</p>
        <h2 class="store-shop-banner__title">{{ $title }}</h2>
        @if($description)
            <p class="store-shop-banner__desc">{{ $description }}</p>
        @endif
        <a href="{{ $bannerLink }}" class="store-shop-banner__btn">
            <span>اكتشف الآن</span>
            <i class="w-icon-long-arrow-left"></i>
        </a>
    </div>
</section>
