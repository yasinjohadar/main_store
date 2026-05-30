<section class="home-brands appear-animate mb-9">
    <div class="home-brands__header">
        <span class="home-brands__eyebrow">شركاء النجاح</span>
        <h2 class="home-brands__title">عملاؤنا</h2>
    </div>

    <div class="swiper home-brands__swiper">
        <div class="swiper-container swiper-theme" data-swiper-options="{
            'spaceBetween': 16,
            'slidesPerView': 2,
            'autoplay': { 'delay': 3500, 'disableOnInteraction': false },
            'breakpoints': {
                '576': { 'slidesPerView': 3, 'spaceBetween': 18 },
                '768': { 'slidesPerView': 4, 'spaceBetween': 20 },
                '992': { 'slidesPerView': 5, 'spaceBetween': 22 },
                '1200': { 'slidesPerView': 6, 'spaceBetween': 24 }
            }
        }">
            <div class="swiper-wrapper">
                @foreach(range(1, 12) as $i)
                    <div class="swiper-slide">
                        <div class="home-brand-card">
                            <img src="{{ asset('frontend/assets/images/demos/demo1/brands/' . $i . '.png') }}"
                                alt="Brand {{ $i }}" width="410" height="186" loading="lazy">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
