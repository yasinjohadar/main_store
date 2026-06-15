@if(($homePromoBanners ?? collect())->isNotEmpty())
<div class="home-promo-grid appear-animate">
    @foreach($homePromoBanners as $index => $promo)
        <a href="{{ $promo->button_url ?: route('frontend.shop.index') }}" class="home-promo-card home-promo-card--{{ $promo->tone }}"
            style="--promo-delay: {{ $index * 0.12 }}s;">
            <span class="home-promo-card__media">
                <img src="{{ $promo->image_url }}" alt="{{ $promo->title }}"
                    width="610" height="280" loading="lazy">
            </span>
            <span class="home-promo-card__overlay"></span>
            <span class="home-promo-card__content">
                @if($promo->badge)
                    <span class="home-promo-card__badge">{{ $promo->badge }}</span>
                @endif
                <span class="home-promo-card__title">{{ $promo->title }}</span>
                @if($promo->subtitle)
                    <span class="home-promo-card__subtitle">{{ $promo->subtitle }}</span>
                @endif
                @if($promo->price_text)
                    <span class="home-promo-card__price">
                        {{ $promo->price_label }}
                        <strong>{{ $promo->price_text }}</strong>
                    </span>
                @endif
                <span class="home-promo-card__cta">
                    تسوق الآن
                    <i class="w-icon-long-arrow-left"></i>
                </span>
            </span>
        </a>
    @endforeach
</div>
@endif
