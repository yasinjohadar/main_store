@php
    $slides = [
        [
            'bg' => asset('frontend/assets/images/demos/demo-rtl/sliders/slide-1.jpg'),
            'bg_color' => '#e8edf3',
            'product' => asset('frontend/assets/images/demos/demo-rtl/sliders/shoes.png'),
            'badge' => 'خصم 30%',
            'subtitle' => 'أحذية رجالية مخصصة',
            'title' => 'للجري والرياضة',
            'desc' => 'تصاميم خفيفة ومريحة لأداء يومي استثنائي',
            'accent' => '#336699',
        ],
        [
            'bg' => asset('frontend/assets/images/demos/demo-rtl/sliders/slide-2.jpg'),
            'bg_color' => '#eef1f5',
            'product' => asset('frontend/assets/images/demos/demo-rtl/sliders/men.png'),
            'badge' => 'عرض الأسبوع',
            'subtitle' => 'تسلق الجبال',
            'title' => 'حقائب ظهر عصرية',
            'desc' => 'العرض ساري حتى نهاية هذا الأسبوع',
            'accent' => '#0f766e',
        ],
        [
            'bg' => asset('frontend/assets/images/demos/demo-rtl/sliders/slide-3.jpg'),
            'bg_color' => '#f2f3f5',
            'product' => asset('frontend/assets/images/demos/demo-rtl/sliders/skate.png'),
            'badge' => 'الأكثر مبيعاً',
            'subtitle' => 'مجموعة رائجة',
            'title' => 'أدوات التزلج',
            'desc' => 'اختيارات شبابية بأسعار تنافسية',
            'accent' => '#c2410c',
        ],
    ];
@endphp

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
            @foreach($slides as $index => $slide)
                <div class="swiper-slide banner banner-fixed intro-slide home-hero__slide intro-slide{{ $index + 1 }}"
                    style="background-color: {{ $slide['bg_color'] }};">
                    <div class="home-hero__bg" style="background-image: url('{{ $slide['bg'] }}');"></div>
                    <div class="home-hero__overlay"></div>

                    <div class="container home-hero__container">
                        <figure class="slide-image home-hero__figure skrollable slide-animate">
                            <img src="{{ $slide['product'] }}" alt="{{ $slide['title'] }}" class="home-hero__product-img"
                                data-bottom-top="transform: translateY(8vh);"
                                data-top-bottom="transform: translateY(-8vh);"
                                width="480" height="520" loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
                        </figure>

                        <div class="banner-content home-hero__content y-50 text-right">
                            <span class="home-hero__badge slide-animate"
                                style="--hero-accent: {{ $slide['accent'] }};"
                                data-animation-options="{ 'name': 'fadeInRightShorter', 'duration': '1s', 'delay': '.1s' }">
                                {{ $slide['badge'] }}
                            </span>

                            <p class="home-hero__eyebrow slide-animate"
                                data-animation-options="{ 'name': 'fadeInRightShorter', 'duration': '1s', 'delay': '.2s' }">
                                {{ $slide['subtitle'] }}
                            </p>

                            <h3 class="banner-title home-hero__title slide-animate"
                                data-animation-options="{ 'name': 'fadeInRightShorter', 'duration': '1s', 'delay': '.35s' }">
                                {{ $slide['title'] }}
                            </h3>

                            <p class="home-hero__desc slide-animate"
                                data-animation-options="{ 'name': 'fadeInRightShorter', 'duration': '1s', 'delay': '.5s' }">
                                {{ $slide['desc'] }}
                            </p>

                            <a href="{{ route('frontend.shop.index') }}"
                                class="btn home-hero__btn slide-animate"
                                style="--hero-accent: {{ $slide['accent'] }};"
                                data-animation-options="{ 'name': 'fadeInRightShorter', 'duration': '1s', 'delay': '.65s' }">
                                <span>تسوق الآن</span>
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
