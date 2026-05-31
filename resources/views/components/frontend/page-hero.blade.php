@props([
    'title',
    'subtitle' => null,
    'eyebrow' => null,
    'icon' => null,
    'breadcrumbItems' => [],
    'variant' => 'default',
    'embedded' => false,
])

@php
    $items = $breadcrumbItems ?? [];
    $heroClass = 'store-page-hero store-page-hero--' . ($variant === 'soft' ? 'soft' : 'default');
    if ($embedded) {
        $heroClass .= ' store-page-hero--embedded';
    }
@endphp

<section class="{{ $heroClass }}" aria-labelledby="store-page-hero-title">
    @unless($embedded)
        <div class="container">
    @endunless
        <div class="store-page-hero__card">
            <div class="store-page-hero__bg" aria-hidden="true">
                <span class="store-page-hero__mesh"></span>
                <span class="store-page-hero__decor store-page-hero__decor--1"></span>
                <span class="store-page-hero__decor store-page-hero__decor--2"></span>
                <span class="store-page-hero__decor store-page-hero__decor--3"></span>
                <span class="store-page-hero__decor store-page-hero__decor--4"></span>
            </div>

            <div class="store-page-hero__inner">
                <div class="store-page-hero__body">
                    @if(! empty($items))
                        <nav class="store-page-hero__breadcrumb" aria-label="مسار التصفح">
                            <ol class="store-page-hero__breadcrumb-list">
                                <li class="store-page-hero__breadcrumb-item">
                                    <a href="{{ route('frontend.home') }}" class="store-page-hero__breadcrumb-link">
                                        <i class="w-icon-home"></i>
                                        <span>الرئيسية</span>
                                    </a>
                                </li>
                                @foreach($items as $item)
                                    @if(! empty($item['url']))
                                        <li class="store-page-hero__breadcrumb-item">
                                            <a href="{{ $item['url'] }}" class="store-page-hero__breadcrumb-link">{{ $item['label'] }}</a>
                                        </li>
                                    @else
                                        <li class="store-page-hero__breadcrumb-item store-page-hero__breadcrumb-item--active" aria-current="page">
                                            {{ $item['label'] }}
                                        </li>
                                    @endif
                                @endforeach
                            </ol>
                        </nav>
                    @endif

                    <div class="store-page-hero__content">
                        @if($icon)
                            <span class="store-page-hero__icon" aria-hidden="true"><i class="{{ $icon }}"></i></span>
                        @endif

                        <div class="store-page-hero__text">
                            @if($eyebrow)
                                <p class="store-page-hero__eyebrow">{{ $eyebrow }}</p>
                            @endif
                            <h1 id="store-page-hero-title" class="store-page-hero__title page-title mb-0">{{ $title }}</h1>
                            @if($subtitle)
                                <p class="store-page-hero__subtitle">{{ $subtitle }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                @if(isset($slot) && ! $slot->isEmpty())
                    <div class="store-page-hero__extra">
                        {{ $slot }}
                    </div>
                @endif
            </div>
        </div>
    @unless($embedded)
        </div>
    @endunless
</section>
