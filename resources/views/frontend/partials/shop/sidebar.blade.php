@php
    $filterRoute = $filterRoute ?? route('frontend.shop.index');
    $activeCategory = $activeCategory ?? null;
@endphp

<aside class="sidebar shop-sidebar sticky-sidebar-wrapper sidebar-fixed {{ $sidebarClass ?? 'store-shop-sidebar' }}">
    <div class="sidebar-overlay"></div>
    <a class="sidebar-close" href="#"><i class="close-icon"></i></a>

    <div class="sidebar-content scrollable">
        <div class="sticky-sidebar">
            <div class="filter-actions store-shop-sidebar__header">
                <div class="store-shop-sidebar__header-title">
                    <span class="store-shop-sidebar__header-icon"><i class="w-icon-category"></i></span>
                    <label>الفلاتر</label>
                </div>
                <a href="{{ $filterRoute }}" class="store-shop-sidebar__clear filter-clean">
                    <span>مسح الكل</span>
                </a>
            </div>

            <div class="widget widget-collapsible store-shop-sidebar__widget">
                <h3 class="widget-title store-shop-sidebar__widget-title">
                    <span class="store-shop-sidebar__widget-icon"><i class="w-icon-category"></i></span>
                    <label>التصنيفات</label>
                </h3>
                <ul class="widget-body filter-items search-ul store-shop-sidebar__list">
                    <li>
                        <a href="{{ route('frontend.shop.index') }}"
                            class="store-shop-filter-link {{ ! $activeCategory ? 'active' : '' }}">
                            جميع المنتجات
                            <i class="w-icon-long-arrow-left store-shop-filter-link__arrow"></i>
                        </a>
                    </li>
                    @foreach($categories ?? [] as $cat)
                        <li>
                            <a href="{{ route('frontend.category.show', $cat->slug) }}"
                                class="store-shop-filter-link {{ $activeCategory && $activeCategory->id === $cat->id ? 'active' : '' }}">
                                {{ $cat->name }}
                                <i class="w-icon-long-arrow-left store-shop-filter-link__arrow"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="widget widget-collapsible store-shop-sidebar__widget">
                <h3 class="widget-title store-shop-sidebar__widget-title">
                    <span class="store-shop-sidebar__widget-icon"><i class="w-icon-money"></i></span>
                    <label>السعر</label>
                </h3>
                <div class="widget-body store-shop-sidebar__price">
                    <form class="price-range store-shop-sidebar__price-form" method="GET" action="{{ $filterRoute }}">
                        @foreach(request()->except(['min_price', 'max_price', 'page']) as $key => $value)
                            @if(is_string($value))
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <div class="store-shop-sidebar__price-fields">
                            <div class="store-shop-sidebar__price-field">
                                <small>من</small>
                                <input type="number" name="min_price" class="min_price text-center"
                                    placeholder="0" value="{{ request('min_price') }}" min="0">
                            </div>
                            <span class="delimiter store-shop-sidebar__price-delimiter">—</span>
                            <div class="store-shop-sidebar__price-field">
                                <small>إلى</small>
                                <input type="number" name="max_price" class="max_price text-center"
                                    placeholder="9999" value="{{ request('max_price') }}" min="0">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-rounded store-shop-sidebar__price-btn">
                            <span>تطبيق</span>
                            <i class="w-icon-long-arrow-left"></i>
                        </button>
                    </form>
                </div>
            </div>

            @if(($brands ?? collect())->isNotEmpty())
                <div class="widget widget-collapsible store-shop-sidebar__widget">
                    <h3 class="widget-title store-shop-sidebar__widget-title">
                        <span class="store-shop-sidebar__widget-icon"><i class="w-icon-tag"></i></span>
                        <label>العلامة التجارية</label>
                    </h3>
                    <ul class="widget-body filter-items item-check mt-1 store-shop-sidebar__brands">
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
        </div>
    </div>
</aside>
