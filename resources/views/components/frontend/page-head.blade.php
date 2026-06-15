@props([
    'title',
    'subtitle' => null,
    'breadcrumbItems' => [],
    'embedded' => false,
])

@php
    $headClass = 'store-page-head' . ($embedded ? ' store-page-head--embedded' : '');
    $titleId = $embedded ? 'store-page-head-title-embedded' : 'store-page-head-title';
@endphp

@if($embedded)
    <div class="{{ $headClass }}" aria-labelledby="{{ $titleId }}">
@else
    <section class="{{ $headClass }}" aria-labelledby="{{ $titleId }}">
@endif
        @unless($embedded)
            <div class="container">
        @endunless
            <nav class="store-page-head__breadcrumb" aria-label="مسار التصفح">
                <ol class="store-page-head__breadcrumb-list">
                    <li class="store-page-head__breadcrumb-item">
                        <a href="{{ route('frontend.home') }}" class="store-page-head__breadcrumb-link">
                            <i class="w-icon-home" aria-hidden="true"></i>
                            <span>الرئيسية</span>
                        </a>
                    </li>
                    @foreach($breadcrumbItems as $item)
                        @if(! empty($item['url']))
                            <li class="store-page-head__breadcrumb-item">
                                <a href="{{ $item['url'] }}" class="store-page-head__breadcrumb-link">{{ $item['label'] }}</a>
                            </li>
                        @else
                            <li class="store-page-head__breadcrumb-item store-page-head__breadcrumb-item--active" aria-current="page">
                                {{ $item['label'] }}
                            </li>
                        @endif
                    @endforeach
                </ol>
            </nav>

            <div class="store-page-head__main">
                <div class="store-page-head__text">
                    <h1 id="{{ $titleId }}" class="store-page-head__title page-title mb-0">{{ $title }}</h1>
                    @if($subtitle)
                        <p class="store-page-head__subtitle">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
        @unless($embedded)
            </div>
        @endunless
@if($embedded)
    </div>
@else
    </section>
@endif
