@php
    $promos = [
        [
            'image' => asset('frontend/assets/images/demos/demo-rtl/banners/banner-1.jpg'),
            'bg' => '#dfe3e0',
            'tone' => 'light',
            'badge' => 'خصم حتى 20%',
            'title' => 'ملابس رياضية',
            'subtitle' => 'مجموعة جديدة',
            'price' => '170 ر.س',
            'price_label' => 'يبدأ من',
            'url' => route('frontend.shop.index'),
        ],
        [
            'image' => asset('frontend/assets/images/demos/demo-rtl/banners/banner-2.jpg'),
            'bg' => '#3d3d3d',
            'tone' => 'dark',
            'badge' => 'وصل حديثاً',
            'title' => 'إكسسوارات',
            'subtitle' => 'مجموعة مميزة',
            'price' => '90 ر.س',
            'price_label' => 'ابتداءً من',
            'url' => route('frontend.shop.index'),
        ],
    ];
@endphp

<div class="home-promo-grid appear-animate">
    @foreach($promos as $index => $promo)
        <a href="{{ $promo['url'] }}" class="home-promo-card home-promo-card--{{ $promo['tone'] }}"
            style="--promo-delay: {{ $index * 0.12 }}s;">
            <span class="home-promo-card__media">
                <img src="{{ $promo['image'] }}" alt="{{ $promo['title'] }}"
                    width="610" height="280" loading="lazy" style="background-color: {{ $promo['bg'] }};">
            </span>
            <span class="home-promo-card__overlay"></span>
            <span class="home-promo-card__content">
                <span class="home-promo-card__badge">{{ $promo['badge'] }}</span>
                <span class="home-promo-card__title">{{ $promo['title'] }}</span>
                <span class="home-promo-card__subtitle">{{ $promo['subtitle'] }}</span>
                <span class="home-promo-card__price">
                    {{ $promo['price_label'] }}
                    <strong>{{ $promo['price'] }}</strong>
                </span>
                <span class="home-promo-card__cta">
                    تسوق الآن
                    <i class="w-icon-long-arrow-left"></i>
                </span>
            </span>
        </a>
    @endforeach
</div>
