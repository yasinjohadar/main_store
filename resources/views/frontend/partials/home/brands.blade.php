@php
    $brandsEyebrow = $section->resolvedEyebrow($sectionDefaults['eyebrow'] ?? 'شركاء النجاح');
    $brandsTitle = $section->resolvedTitle($sectionDefaults['title'] ?? 'عملاؤنا');
    $brandsSubtitle = $section->resolvedSubtitle($sectionDefaults['subtitle'] ?? 'نختار لك منتجات أفضل العلامات التجارية بعناية');
    $brands = $homeBrands ?? collect();
    // Repeat the logos so one pass is wider than the screen, then render it twice for a seamless loop
    $marqueeSet = collect(range(1, max(1, (int) ceil(10 / max(1, $brands->count())))))->flatMap(fn () => $brands);
@endphp

@if($brands->isNotEmpty())
<section class="home-brands appear-animate mb-9">
    <span class="home-brands__blob home-brands__blob--a" aria-hidden="true"></span>
    <span class="home-brands__blob home-brands__blob--b" aria-hidden="true"></span>

    <div class="home-brands__header">
        @if($brandsEyebrow)
            <span class="home-brands__eyebrow">{{ $brandsEyebrow }}</span>
        @endif
        <h2 class="home-brands__title">{{ $brandsTitle }}</h2>
        @if($brandsSubtitle)
            <p class="home-brands__subtitle">{{ $brandsSubtitle }}</p>
        @endif
        <span class="home-brands__count">
            <strong>{{ $brands->count() }}</strong>
            {{ $brands->count() > 2 && $brands->count() < 11 ? 'علامات تجارية' : 'علامة تجارية' }} في متجرنا
        </span>
    </div>

    <div class="home-brands__marquee">
        <div class="home-brands__track" style="--marquee-duration: {{ max(20, $marqueeSet->count() * 4) }}s;">
            @foreach([false, true] as $isClone)
                <ul class="home-brands__set" @if($isClone) aria-hidden="true" @endif>
                    @foreach($marqueeSet as $brand)
                        <li class="home-brand-card">
                            @if($brand->url)
                                <a href="{{ $brand->url }}" title="{{ $brand->name }}" @if($isClone || $loop->index >= $brands->count()) tabindex="-1" @endif>
                                    <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" width="410" height="186" loading="lazy">
                                </a>
                            @else
                                <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" width="410" height="186" loading="lazy">
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endforeach
        </div>
    </div>
</section>
@endif
