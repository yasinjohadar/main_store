<section class="store-shop-banner appear-animate mb-6" style="--banner-accent: {{ $accent }};">
    <div class="store-shop-banner__inner">
        <div class="store-shop-banner__content">
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
        <div class="store-shop-banner__media">
            <img src="{{ $bannerImage }}" alt="{{ $title }}" loading="lazy">
            <span class="store-shop-banner__media-overlay"></span>
            <span class="store-shop-banner__shine"></span>
        </div>
    </div>
</section>
