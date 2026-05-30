@php
    $features = [
        [
            'icon' => 'w-icon-truck',
            'tone' => 'blue',
            'title' => 'شحن وإرجاع مجاني',
            'desc' => 'لجميع الطلبات فوق 99 ر.س',
        ],
        [
            'icon' => 'w-icon-bag',
            'tone' => 'teal',
            'title' => 'دفع آمن',
            'desc' => 'نضمن لك عملية دفع آمنة',
        ],
        [
            'icon' => 'w-icon-money',
            'tone' => 'amber',
            'title' => 'ضمان استرداد الأموال',
            'desc' => 'خلال 30 يوماً',
        ],
        [
            'icon' => 'w-icon-chat',
            'tone' => 'violet',
            'title' => 'دعم العملاء',
            'desc' => 'تواصل معنا على مدار الساعة',
        ],
    ];
@endphp

<div class="home-features appear-animate">
    @foreach($features as $index => $feature)
        <article class="home-feature-card home-feature-card--{{ $feature['tone'] }}"
            style="--feature-delay: {{ $index * 0.08 }}s;">
            <span class="home-feature-card__icon-wrap">
                <i class="{{ $feature['icon'] }}"></i>
            </span>
            <div class="home-feature-card__body">
                <h4 class="home-feature-card__title">{{ $feature['title'] }}</h4>
                <p class="home-feature-card__desc">{{ $feature['desc'] }}</p>
            </div>
            <span class="home-feature-card__shine" aria-hidden="true"></span>
        </article>
    @endforeach
</div>
