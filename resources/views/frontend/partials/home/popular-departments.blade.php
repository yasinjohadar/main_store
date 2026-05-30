<section class="home-product-tabs-section appear-animate mt-10 pt-1 mb-2">
    <div class="home-product-tabs-section__head text-center mb-5">
        <h2 class="title justify-content-center ls-normal mb-2">الأقسام الشائعة</h2>
        <p class="home-product-tabs-section__subtitle mb-0">اختر ما يناسبك من مجموعات منتقاة بعناية</p>
    </div>

    @php
        $tabs = [
            'tab-new-arrivals' => [
                'label' => 'وصل حديثاً',
                'icon' => 'w-icon-star',
                'products' => $newArrivals,
                'active' => true,
            ],
            'tab-best-seller' => [
                'label' => 'الأكثر مبيعاً',
                'icon' => 'w-icon-sale',
                'products' => $bestSellers,
                'active' => false,
            ],
            'tab-top-rated' => [
                'label' => 'الأعلى تقييماً',
                'icon' => 'w-icon-star-full',
                'products' => $topRated,
                'active' => false,
            ],
            'tab-featured' => [
                'label' => 'مميز',
                'icon' => 'w-icon-heart-full',
                'products' => $featuredProducts,
                'active' => false,
            ],
        ];
    @endphp

    <div class="tab home-product-tabs appear-animate text-center">
        <ul class="nav nav-tabs home-product-tabs__nav justify-content-center" role="tablist">
            @foreach($tabs as $tabId => $tab)
                <li class="nav-item">
                    <a class="nav-link home-product-tabs__link {{ $tab['active'] ? 'active' : '' }}"
                        href="#{{ $tabId }}" role="tab">
                        <span class="home-product-tabs__icon"><i class="{{ $tab['icon'] }}"></i></span>
                        <span class="home-product-tabs__label">{{ $tab['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="tab-content product-wrapper home-product-tabs__content appear-animate">
        @foreach($tabs as $tabId => $tab)
            <div class="tab-pane {{ $tab['active'] ? 'active in' : '' }} pt-4" id="{{ $tabId }}">
                @if($tab['products']->isNotEmpty())
                    <div class="row cols-xl-5 cols-md-4 cols-sm-3 cols-2">
                        @foreach($tab['products'] as $product)
                            <x-frontend.product-card :product="$product" />
                        @endforeach
                    </div>
                @else
                    <div class="home-product-tabs__empty">
                        <span class="home-product-tabs__empty-icon"><i class="{{ $tab['icon'] }}"></i></span>
                        <p class="mb-0">لا توجد منتجات في «{{ $tab['label'] }}» حالياً.</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</section>
