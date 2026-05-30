@php
    $filterRoute = $filterRoute ?? route('frontend.shop.index');
    $activeCategory = $activeCategory ?? null;
@endphp

<nav class="store-shop-horizontal-filters toolbox-left">
    <div class="toolbox-item select-menu store-shop-horizontal-filters__item">
        <a class="select-menu-toggle" href="#">التصنيفات</a>
        <ul class="filter-items store-shop-horizontal-filters__menu">
            <li>
                <a href="{{ route('frontend.shop.index') }}" class="{{ ! $activeCategory ? 'active' : '' }}">جميع المنتجات</a>
            </li>
            @foreach($categories ?? [] as $cat)
                <li>
                    <a href="{{ route('frontend.category.show', $cat->slug) }}"
                        class="{{ $activeCategory && $activeCategory->id === $cat->id ? 'active' : '' }}">
                        {{ $cat->name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="toolbox-item select-menu store-shop-horizontal-filters__item">
        <a class="select-menu-toggle" href="#">السعر</a>
        <ul class="filter-items store-shop-horizontal-filters__menu">
            <li>
                <form class="price-range store-shop-sidebar__price-form" method="GET" action="{{ $filterRoute }}">
                    @foreach(request()->except(['min_price', 'max_price', 'page']) as $key => $value)
                        @if(is_string($value))
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <div class="store-shop-sidebar__price-fields">
                        <input type="number" name="min_price" class="min_price text-center" placeholder="من" value="{{ request('min_price') }}" min="0">
                        <span class="delimiter">—</span>
                        <input type="number" name="max_price" class="max_price text-center" placeholder="إلى" value="{{ request('max_price') }}" min="0">
                    </div>
                    <button type="submit" class="btn btn-primary btn-rounded store-shop-sidebar__price-btn">تطبيق</button>
                </form>
            </li>
        </ul>
    </div>

    @if(($brands ?? collect())->isNotEmpty())
        <div class="toolbox-item select-menu store-shop-horizontal-filters__item">
            <a class="select-menu-toggle" href="#">العلامة</a>
            <ul class="filter-items item-check store-shop-horizontal-filters__menu">
                @foreach($brands as $brand)
                    <li class="{{ request('brand') === $brand->slug ? 'active' : '' }}">
                        <a href="{{ request()->fullUrlWithQuery(['brand' => $brand->slug, 'page' => null]) }}">
                            {{ $brand->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <a href="{{ $filterRoute }}" class="filter-clean store-shop-horizontal-filters__clear">مسح الكل</a>
</nav>
