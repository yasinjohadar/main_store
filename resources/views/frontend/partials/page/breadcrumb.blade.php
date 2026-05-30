@props(['items' => []])

<nav class="breadcrumb-nav store-shop-breadcrumb" aria-label="مسار التصفح">
    <div class="container">
        <ol class="breadcrumb bb-no store-shop-breadcrumb__list">
            <li class="store-shop-breadcrumb__item">
                <a href="{{ route('frontend.home') }}" class="store-shop-breadcrumb__link">
                    <i class="w-icon-home"></i>
                    <span>الرئيسية</span>
                </a>
            </li>
            @foreach($items as $item)
                @if(! empty($item['url']))
                    <li class="store-shop-breadcrumb__item">
                        <a href="{{ $item['url'] }}" class="store-shop-breadcrumb__link">{{ $item['label'] }}</a>
                    </li>
                @else
                    <li class="store-shop-breadcrumb__item store-shop-breadcrumb__item--active" aria-current="page">
                        {{ $item['label'] }}
                    </li>
                @endif
            @endforeach
        </ol>
    </div>
</nav>
