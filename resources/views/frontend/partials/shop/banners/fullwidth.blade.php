<section class="store-shop-banner store-shop-banner--fullwidth shop-default-banner banner d-flex align-items-center mb-6 appear-animate"
    style="background-image: url('{{ $bannerImage }}'); background-color: {{ $accent }};">
    <div class="container banner-content store-shop-banner--fullwidth__content">
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
