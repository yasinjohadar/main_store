@if(isset($heroSlides) && $heroSlides->isNotEmpty())
<section class="intro-section home-hero">
    <div class="swiper-container home-hero__swiper swiper-theme nav-inner pg-inner swiper-nav-lg animation-slider pg-xxl-hide nav-xxl-show nav-hide"
        data-swiper-options="{
        'slidesPerView': 1,
        'effect': 'fade',
        'fadeEffect': { 'crossFade': true },
        'speed': 900,
        'autoplay': { 'delay': 7000, 'disableOnInteraction': false },
        'pagination': { 'el': '.home-hero__pagination', 'clickable': true }
    }">
        <div class="swiper-wrapper">
            @foreach($heroSlides as $index => $slide)
                <div class="swiper-slide banner banner-fixed intro-slide home-hero__slide intro-slide{{ $index + 1 }}"
                    style="background-color: {{ $slide->resolved_background_color }};">
                    @if($slide->background_image_url)
                        <div class="home-hero__bg" style="background-image: url('{{ $slide->background_image_url }}');"></div>
                    @endif
                    <div class="home-hero__overlay"></div>

                    <div class="container home-hero__container">
                        @if($slide->product_image_url)
                            <figure class="slide-image home-hero__figure skrollable slide-animate">
                                <img src="{{ $slide->product_image_url }}" alt="{{ $slide->title }}" class="home-hero__product-img"
                                    data-bottom-top="transform: translateY(8vh);"
                                    data-top-bottom="transform: translateY(-8vh);"
                                    width="480" height="520" loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
                            </figure>
                        @endif

                        <div class="banner-content home-hero__content y-50 text-right">
                            @if($slide->badge)
                                <span class="home-hero__badge slide-animate"
                                    style="--hero-accent: {{ $slide->resolved_accent_color }};"
                                    data-animation-options="{ 'name': 'fadeInRightShorter', 'duration': '1s', 'delay': '.1s' }">
                                    {{ $slide->badge }}
                                </span>
                            @endif

                            @if($slide->subtitle)
                                <p class="home-hero__eyebrow slide-animate"
                                    data-animation-options="{ 'name': 'fadeInRightShorter', 'duration': '1s', 'delay': '.2s' }">
                                    {{ $slide->subtitle }}
                                </p>
                            @endif

                            <h3 class="banner-title home-hero__title slide-animate"
                                data-animation-options="{ 'name': 'fadeInRightShorter', 'duration': '1s', 'delay': '.35s' }">
                                {{ $slide->title }}
                            </h3>

                            @if($slide->description)
                                <p class="home-hero__desc slide-animate"
                                    data-animation-options="{ 'name': 'fadeInRightShorter', 'duration': '1s', 'delay': '.5s' }">
                                    {{ $slide->description }}
                                </p>
                            @endif

                            <a href="{{ $slide->button_url }}"
                                class="btn home-hero__btn slide-animate"
                                style="--hero-accent: {{ $slide->resolved_accent_color }};"
                                data-animation-options="{ 'name': 'fadeInRightShorter', 'duration': '1s', 'delay': '.65s' }">
                                <span>{{ $slide->button_text }}</span>
                                <i class="w-icon-long-arrow-left"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="home-hero__pagination swiper-pagination"></div>
        <button class="swiper-button-next home-hero__nav appear-animate" aria-label="الشريحة التالية"></button>
        <button class="swiper-button-prev home-hero__nav appear-animate" aria-label="الشريحة السابقة"></button>
    </div>
</section>
@endif
