@php
    $isHome = request()->routeIs('frontend.home');
    $categoryIcons = [
        'w-icon-tshirt2', 'w-icon-home', 'w-icon-electronics', 'w-icon-furniture',
        'w-icon-heartbeat', 'w-icon-gift', 'w-icon-gamepad', 'w-icon-ice-cream',
        'w-icon-ios', 'w-icon-camera', 'w-icon-ruby', 'w-icon-category',
    ];
    $pagesActive = request()->routeIs([
        'frontend.about', 'frontend.contact', 'frontend.faq', 'frontend.privacy', 'frontend.terms',
        'frontend.wishlist', 'frontend.cart.*', 'frontend.checkout.*', 'frontend.account*', 'frontend.compare',
    ]);
@endphp
            <div class="header-bottom sticky-content fix-top sticky-header store-header-bar{{ $isHome ? ' show-dropdown has-dropdown' : '' }}">
                <div class="container">
                    <div class="inner-wrap store-header-bar__inner">
                        <div class="header-left store-header-bar__left">
                            <div class="dropdown category-dropdown has-border store-category-nav" data-visible="true">
                                <a href="#" class="category-toggle store-category-nav__toggle{{ $isHome ? ' text-dark' : '' }}" role="button" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="{{ $isHome ? 'true' : 'false' }}" data-display="static"
                                    title="تصفح التصنيفات">
                                    <span class="store-category-nav__toggle-icon"><i class="w-icon-category"></i></span>
                                    <span class="store-category-nav__toggle-text">تصفح التصنيفات</span>
                                </a>

                                <div class="dropdown-box store-category-nav__panel">
                                    <div class="store-category-nav__panel-head">
                                        <strong>أقسام المتجر</strong>
                                        <span>تصفح جميع الفئات</span>
                                    </div>
                                    <ul class="menu vertical-menu category-menu store-category-nav__menu">
                                        @forelse($navCategories ?? [] as $category)
                                            <li class="store-category-nav__item">
                                                <a href="{{ route('frontend.category.show', $category->slug) }}" class="store-category-nav__link">
                                                    <span class="store-category-nav__icon">
                                                        <i class="{{ $categoryIcons[$loop->index % count($categoryIcons)] }}"></i>
                                                    </span>
                                                    <span class="store-category-nav__label">{{ $category->name }}</span>
                                                    <i class="w-icon-angle-left store-category-nav__arrow"></i>
                                                </a>
                                            </li>
                                        @empty
                                            <li class="store-category-nav__item">
                                                <a href="{{ route('frontend.shop.index') }}" class="store-category-nav__link">
                                                    <span class="store-category-nav__icon"><i class="w-icon-category"></i></span>
                                                    <span class="store-category-nav__label">المتجر</span>
                                                    <i class="w-icon-angle-left store-category-nav__arrow"></i>
                                                </a>
                                            </li>
                                        @endforelse
                                        <li class="store-category-nav__footer">
                                            <a href="{{ route('frontend.shop.index') }}" class="store-category-nav__view-all">
                                                عرض كل التصنيفات
                                                <i class="w-icon-angle-left"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <nav class="main-nav store-main-nav">
                                <ul class="menu active-underline store-main-nav__menu">
                                    <li class="{{ request()->routeIs('frontend.home') ? 'active' : '' }}">
                                        <a href="{{ route('frontend.home') }}">
                                            <i class="w-icon-home store-main-nav__icon"></i>
                                            <span>الرئيسية</span>
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('frontend.shop.*', 'frontend.category.*', 'frontend.product.*') ? 'active' : '' }}">
                                        <a href="{{ route('frontend.shop.index') }}">
                                            <i class="w-icon-store store-main-nav__icon"></i>
                                            <span>المتجر</span>
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('frontend.blog.*') ? 'active' : '' }}">
                                        <a href="{{ route('frontend.blog.index') }}">
                                            <i class="w-icon-comments store-main-nav__icon"></i>
                                            <span>المدونة</span>
                                        </a>
                                    </li>
                                    <li class="{{ $pagesActive ? 'active' : '' }}">
                                        <a href="#">
                                            <i class="w-icon-grid store-main-nav__icon"></i>
                                            <span>الصفحات</span>
                                        </a>
                                        <ul class="store-main-nav__submenu">
                                            <li><a href="{{ route('frontend.about') }}">من نحن</a></li>
                                            <li><a href="{{ route('frontend.contact') }}">اتصل بنا</a></li>
                                            <li><a href="{{ route('frontend.faq') }}">الأسئلة الشائعة</a></li>
                                            <li><a href="{{ route('frontend.privacy') }}">سياسة الخصوصية</a></li>
                                            <li><a href="{{ route('frontend.terms') }}">الشروط والأحكام</a></li>
                                            <li><a href="{{ route('frontend.wishlist') }}">المفضلة</a></li>
                                            <li><a href="{{ route('frontend.compare') }}">المقارنة</a></li>
                                            <li><a href="{{ route('frontend.cart.index') }}">السلة</a></li>
                                            <li><a href="{{ route('frontend.checkout.index') }}">إتمام الشراء</a></li>
                                            <li><a href="{{ route('frontend.account') }}">حسابي</a></li>
                                            @guest
                                                <li><a href="{{ route('login') }}">تسجيل الدخول</a></li>
                                            @endguest
                                        </ul>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                        <div class="header-right store-header-actions">
                            <a href="{{ route('frontend.track-order') }}" class="store-header-actions__link d-xl-show">
                                <span class="store-header-actions__icon"><i class="w-icon-map-marker"></i></span>
                                <span>تتبع الطلب</span>
                            </a>
                            <a href="{{ route('frontend.shop.index') }}" class="store-header-actions__link store-header-actions__link--accent">
                                <span class="store-header-actions__icon"><i class="w-icon-sale"></i></span>
                                <span>العروض</span>
                            </a>
                        </div>
                    </div>
                </div>