@php
    $filterRoute = $filterRoute ?? route('frontend.shop.index');
    $activeCategory = $activeCategory ?? null;
    $layout = $layout ?? session('shop_layout', []);
    $showSidebarToggle = in_array($layout['sidebar'] ?? 'left', ['left', 'right', 'offcanvas'], true);
@endphp

<nav class="toolbox sticky-toolbox sticky-content fix-top store-shop-toolbar{{ ($layout['filters'] ?? 'vertical') === 'horizontal' ? ' store-shop-toolbar--horizontal' : '' }}">
    @if(($layout['filters'] ?? 'vertical') === 'horizontal')
        @include('frontend.partials.shop.filters.horizontal', compact('categories', 'brands', 'filterRoute', 'activeCategory', 'layout'))
    @endif

    <div class="store-shop-toolbar__row">
    <div class="toolbox-left store-shop-toolbar__left">
        @if($showSidebarToggle && ($layout['filters'] ?? 'vertical') === 'vertical')
            <a href="#" class="btn btn-primary btn-outline btn-rounded left-sidebar-toggle btn-icon-left d-block d-lg-none store-shop-toolbar__filters-btn">
                <i class="w-icon-category"></i><span>الفلاتر</span>
            </a>
        @endif

        <form method="GET" action="{{ $filterRoute }}" class="toolbox-item toolbox-sort select-box text-dark d-flex align-items-center store-shop-toolbar__sort">
            @foreach(request()->except(['sort', 'page']) as $key => $value)
                @if(is_string($value))
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach
            <label class="mb-0 store-shop-toolbar__label">ترتيب حسب:</label>
            <select name="sort" class="form-control store-shop-toolbar__select" onchange="this.form.submit()">
                <option value="">الترتيب الافتراضي</option>
                <option value="popularity" @selected(request('sort') === 'popularity')>الأكثر شعبية</option>
                <option value="rating" @selected(request('sort') === 'rating')>الأعلى تقييماً</option>
                <option value="date" @selected(request('sort') === 'date')>الأحدث</option>
                <option value="price-low" @selected(request('sort') === 'price-low')>السعر: من الأقل للأعلى</option>
                <option value="price-high" @selected(request('sort') === 'price-high')>السعر: من الأعلى للأقل</option>
            </select>
        </form>
    </div>

    <div class="toolbox-right store-shop-toolbar__right">
        <form method="GET" action="{{ $filterRoute }}" class="toolbox-item toolbox-show select-box d-inline store-shop-toolbar__per-page">
            @foreach(request()->except(['per_page', 'page']) as $key => $value)
                @if(is_string($value))
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach
            <label class="store-shop-toolbar__label store-shop-toolbar__label--inline">عرض</label>
            <select name="per_page" class="form-control store-shop-toolbar__select" onchange="this.form.submit()">
                @foreach([9, 12, 24, 36] as $count)
                    <option value="{{ $count }}" @selected((int) request('per_page', 12) === $count)>{{ $count }}</option>
                @endforeach
            </select>
        </form>

        @if(($layout['view'] ?? 'grid') === 'grid')
            <div class="toolbox-item store-shop-toolbar__cols d-none d-md-flex">
                <label class="store-shop-toolbar__label store-shop-toolbar__label--inline">أعمدة</label>
                @foreach([3, 4, 5, 6] as $col)
                    <a href="{{ shop_query(['cols' => $col]) }}"
                        class="store-shop-toolbar__col-btn{{ shop_layout_active('cols', $col, $layout) ? ' is-active' : '' }}"
                        title="{{ $col }} أعمدة">{{ $col }}</a>
                @endforeach
            </div>
        @endif

        <div class="toolbox-item toolbox-layout store-shop-toolbar__layout">
            <a href="{{ shop_query(['view' => 'grid']) }}"
                class="icon-mode-grid btn-layout store-shop-toolbar__layout-btn{{ shop_layout_active('view', 'grid', $layout) ? ' active' : '' }}"
                title="عرض شبكي">
                <i class="w-icon-grid"></i>
            </a>
            <a href="{{ shop_query(['view' => 'list']) }}"
                class="icon-mode-list btn-layout store-shop-toolbar__layout-btn{{ shop_layout_active('view', 'list', $layout) ? ' active' : '' }}"
                title="عرض قائمة">
                <i class="w-icon-list"></i>
            </a>
        </div>

        <div class="toolbox-item store-shop-toolbar__options dropdown">
            <button type="button" class="store-shop-toolbar__options-toggle" data-toggle="dropdown" aria-expanded="false" title="خيارات العرض">
                <i class="w-icon-category"></i>
            </button>
            <div class="dropdown-box store-shop-toolbar__options-menu">
                <span class="store-shop-toolbar__options-title">تخطيط الصفحة</span>
                <div class="store-shop-toolbar__options-group">
                    <span>الفلاتر</span>
                    <a href="{{ shop_query(['sidebar' => 'left', 'filters' => 'vertical']) }}" class="{{ shop_layout_active('sidebar', 'left', $layout) ? 'is-active' : '' }}">يسار</a>
                    <a href="{{ shop_query(['sidebar' => 'right', 'filters' => 'vertical']) }}" class="{{ shop_layout_active('sidebar', 'right', $layout) ? 'is-active' : '' }}">يمين</a>
                    <a href="{{ shop_query(['sidebar' => 'offcanvas', 'filters' => 'vertical']) }}" class="{{ shop_layout_active('sidebar', 'offcanvas', $layout) ? 'is-active' : '' }}">منبثق</a>
                    <a href="{{ shop_query(['sidebar' => 'none', 'filters' => 'horizontal']) }}" class="{{ shop_layout_active('sidebar', 'none', $layout) ? 'is-active' : '' }}">بدون</a>
                </div>
                <div class="store-shop-toolbar__options-group">
                    <span>البانر</span>
                    <a href="{{ shop_query(['banner' => 'sidebar']) }}" class="{{ shop_layout_active('banner', 'sidebar', $layout) ? 'is-active' : '' }}">مع الشريط</a>
                    <a href="{{ shop_query(['banner' => 'boxed']) }}" class="{{ shop_layout_active('banner', 'boxed', $layout) ? 'is-active' : '' }}">مربّع</a>
                    <a href="{{ shop_query(['banner' => 'fullwidth']) }}" class="{{ shop_layout_active('banner', 'fullwidth', $layout) ? 'is-active' : '' }}">كامل</a>
                </div>
                <div class="store-shop-toolbar__options-group">
                    <span>التحميل</span>
                    <a href="{{ shop_query(['load' => 'pagination']) }}" class="{{ shop_layout_active('load', 'pagination', $layout) ? 'is-active' : '' }}">صفحات</a>
                    <a href="{{ shop_query(['load' => 'infinite']) }}" class="{{ shop_layout_active('load', 'infinite', $layout) ? 'is-active' : '' }}">لا نهائي</a>
                </div>
                <div class="store-shop-toolbar__options-group">
                    <span>فلاتر أفقية</span>
                    <a href="{{ shop_query(['filters' => 'horizontal', 'sidebar' => 'none']) }}" class="{{ shop_layout_active('filters', 'horizontal', $layout) ? 'is-active' : '' }}">تفعيل</a>
                    <a href="{{ shop_query(['filters' => 'vertical', 'sidebar' => 'left']) }}" class="{{ shop_layout_active('filters', 'vertical', $layout) ? 'is-active' : '' }}">إيقاف</a>
                </div>
            </div>
        </div>
    </div>
    </div>
</nav>
