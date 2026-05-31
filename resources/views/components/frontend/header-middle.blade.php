            <div class="header-middle">
                @php($storePhone = '920000000')
                <div class="container">
                    <div class="header-left mr-md-4">
                        <a href="#" class="mobile-menu-toggle  w-icon-hamburger" aria-label="menu-toggle">
                        </a>
                        @php($siteName = site_setting(\App\Services\SiteSettingsService::KEY_SITE_NAME, config('app.name')))
                        @php($siteLogoUrl = site_setting_url(\App\Services\SiteSettingsService::KEY_SITE_LOGO))
                        <a href="{{ route('frontend.home') }}" class="logo ml-lg-0" title="{{ $siteName }}">
                            @if($siteLogoUrl)
                                <img src="{{ $siteLogoUrl }}" alt="{{ $siteName }}" width="144" height="45" />
                            @else
                                <span class="store-site-logo">{{ $siteName }}</span>
                            @endif
                        </a>
                        <form method="get" action="{{ route('frontend.shop.index') }}"
                            class="header-search hs-expanded hs-round d-none d-md-flex input-wrapper store-header-search">
                            <div class="store-header-search__category" data-search-category-picker>
                                <input type="hidden" name="category" value="{{ request('category', '') }}"
                                    data-search-category-input>
                                <button type="button" class="store-header-search__category-toggle"
                                    data-search-category-toggle aria-haspopup="listbox" aria-expanded="false">
                                    <i class="w-icon-category"></i>
                                    <span class="store-header-search__category-label" data-search-category-label>{{ ($navCategories ?? collect())->firstWhere('slug', request('category'))?->name ?? 'جميع التصنيفات' }}</span>
                                    <i class="w-icon-angle-down store-header-search__category-chevron"></i>
                                </button>
                                <div class="store-header-search__category-menu" data-search-category-menu role="listbox">
                                    <button type="button" class="store-header-search__category-option{{ request('category') ? '' : ' is-active' }}"
                                        data-value="" role="option">
                                        جميع التصنيفات
                                    </button>
                                    @foreach($navCategories ?? [] as $category)
                                        <button type="button"
                                            class="store-header-search__category-option{{ request('category') === $category->slug ? ' is-active' : '' }}"
                                            data-value="{{ $category->slug }}" role="option">
                                            {{ $category->name }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            <input type="text" class="form-control store-header-search__input" name="search" id="search"
                                placeholder="ابحث عن منتج..." value="{{ request('search') }}" autocomplete="off" />
                            <button class="btn btn-search store-header-search__btn" type="submit" aria-label="بحث">
                                <i class="w-icon-search"></i>
                                <span>بحث</span>
                            </button>
                        </form>
                    </div>
                    <div class="header-right ml-4 store-header-tools">
                        <a href="tel:{{ $storePhone ?? '920000000' }}" class="header-call store-header-tools__item store-header-tools__item--call d-xs-show">
                            <span class="store-header-tools__icon">
                                <i class="w-icon-call"></i>
                            </span>
                            <span class="store-header-tools__text d-none d-lg-flex">
                                <span class="store-header-tools__text-sub">دردشة مباشرة أو:</span>
                                <span class="store-header-tools__text-main">{{ $storePhone ?? '920000000' }}</span>
                            </span>
                        </a>
                        <a class="wishlist link store-header-tools__item d-xs-show" href="{{ route('frontend.wishlist') }}">
                            <span class="store-header-tools__icon">
                                <i class="w-icon-heart">
                                    <span class="wishlist-count">{{ $wishlistCount ?? 0 }}</span>
                                </i>
                            </span>
                            <span class="wishlist-label store-header-tools__label d-none d-lg-block">المفضلة</span>
                        </a>
                        <a class="compare link store-header-tools__item d-xs-show" href="{{ route('frontend.compare') }}">
                            <span class="store-header-tools__icon">
                                <i class="w-icon-compare">
                                    <span class="compare-count">{{ $compareCount ?? 0 }}</span>
                                </i>
                            </span>
                            <span class="compare-label store-header-tools__label d-none d-lg-block">المقارنة</span>
                        </a>
                        <div class="dropdown cart-dropdown cart-offcanvas mr-0 mr-lg-2 store-header-tools__cart">
                            <div class="cart-overlay"></div>
                            <a href="#" class="cart-toggle link store-header-tools__item">
                                <span class="store-header-tools__icon">
                                    <i class="w-icon-cart">
                                        <span class="cart-count">{{ $cartCount ?? 0 }}</span>
                                    </i>
                                </span>
                                <span class="cart-label store-header-tools__label d-none d-lg-block">السلة</span>
                            </a>
                            <div class="dropdown-box store-cart-dropdown-panel">
                                <div class="store-cart-dropdown-panel__header">
                                    <div class="store-cart-dropdown-panel__headline">
                                        <div class="store-cart-dropdown-panel__title-row">
                                            <span class="store-cart-dropdown-panel__icon"><i class="w-icon-cart"></i></span>
                                            <span class="store-cart-dropdown-panel__title">سلة التسوق</span>
                                        </div>
                                        <span class="store-cart-dropdown-panel__count" data-cart-count-label>
                                            {{ $cartCount ?? 0 }} {{ ($cartCount ?? 0) === 1 ? 'منتج' : 'منتجات' }}
                                        </span>
                                    </div>
                                    <a href="#" class="store-cart-dropdown-panel__close btn-close cart-close" aria-label="إغلاق">
                                        <span>إغلاق</span>
                                        <i class="w-icon-long-arrow-left"></i>
                                    </a>
                                </div>

                                <div data-cart-dropdown-root>
                                    @include('components.frontend.cart-dropdown-content')
                                </div>
                            </div>
                            <!-- End of Dropdown Box -->
                        </div>
                    </div>
                </div>
            </div>