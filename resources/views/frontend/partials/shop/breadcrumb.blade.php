@props([
    'title' => 'المتجر',
    'category' => null,
])

<nav class="breadcrumb-nav store-shop-breadcrumb" aria-label="مسار التصفح">
    <div class="container">
        <ol class="breadcrumb bb-no store-shop-breadcrumb__list">
            <li class="store-shop-breadcrumb__item">
                <a href="{{ route('frontend.home') }}" class="store-shop-breadcrumb__link">
                    <i class="w-icon-home"></i>
                    <span>الرئيسية</span>
                </a>
            </li>
            @if($category)
                <li class="store-shop-breadcrumb__item">
                    <a href="{{ route('frontend.shop.index') }}" class="store-shop-breadcrumb__link">المتجر</a>
                </li>
                <li class="store-shop-breadcrumb__item store-shop-breadcrumb__item--active" aria-current="page">
                    {{ $category->name }}
                </li>
            @else
                <li class="store-shop-breadcrumb__item store-shop-breadcrumb__item--active" aria-current="page">
                    {{ $title }}
                </li>
            @endif
        </ol>
    </div>
</nav>
