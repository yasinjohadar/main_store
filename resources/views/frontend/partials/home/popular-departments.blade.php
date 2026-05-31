@php
    $sectionTitle = $section->resolvedTitle($sectionDefaults['title'] ?? 'الأقسام الشائعة');
    $sectionSubtitle = $section->resolvedSubtitle($sectionDefaults['subtitle'] ?? 'اختر ما يناسبك من مجموعات منتقاة بعناية');
    $tabSettings = $section->getSetting('tabs', $sectionDefaults['settings']['tabs'] ?? []);
    $productMap = [
        'new_arrivals' => $newArrivals,
        'best_seller' => $bestSellers,
        'top_rated' => $topRated,
        'featured' => $featuredProducts,
    ];
    $tabs = [];
    foreach ($tabSettings as $tab) {
        $key = $tab['key'] ?? '';
        $tabId = 'tab-' . str_replace('_', '-', $key);
        $tabs[$tabId] = [
            'label' => $tab['label'] ?? $key,
            'icon' => $tab['icon'] ?? 'w-icon-star',
            'products' => $productMap[$key] ?? collect(),
            'active' => ! empty($tab['active']),
        ];
    }
    if ($tabs && ! collect($tabs)->contains(fn ($t) => $t['active'])) {
        $firstKey = array_key_first($tabs);
        $tabs[$firstKey]['active'] = true;
    }
@endphp

<section class="home-product-tabs-section appear-animate mt-10 pt-1 mb-2">
    <div class="home-product-tabs-section__head text-center mb-5">
        <h2 class="title justify-content-center ls-normal mb-2">{{ $sectionTitle }}</h2>
        @if($sectionSubtitle)
            <p class="home-product-tabs-section__subtitle mb-0">{{ $sectionSubtitle }}</p>
        @endif
    </div>

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
