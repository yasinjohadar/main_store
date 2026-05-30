    <div class="mobile-menu-wrapper">
        <div class="mobile-menu-overlay"></div>

        <a href="#" class="mobile-menu-close"><i class="close-icon"></i></a>

        <div class="mobile-menu-container scrollable">
            <form action="{{ route('frontend.shop.index') }}" method="get" class="input-wrapper">
                <input type="text" class="form-control" name="search" autocomplete="off" placeholder="ابحث..."
                    required />
                <button class="btn btn-search" type="submit">
                    <i class="w-icon-search"></i>
                </button>
            </form>
            <div class="tab">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a href="#main-menu" class="nav-link active">القائمة الرئيسية</a>
                    </li>
                    <li class="nav-item">
                        <a href="#categories" class="nav-link">التصنيفات</a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="main-menu">
                    <ul class="mobile-menu">
                        <li><a href="{{ route('frontend.home') }}">الرئيسية</a></li>
                        <li><a href="{{ route('frontend.shop.index') }}">المتجر</a></li>
                        <li><a href="{{ route('frontend.blog.index') }}">المدونة</a></li>
                        <li>
                            <a href="#">الصفحات</a>
                            <ul>
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
                </div>
                <div class="tab-pane" id="categories">
                    @php
                        $categoryIcons = [
                            'w-icon-tshirt2', 'w-icon-home', 'w-icon-electronics', 'w-icon-furniture',
                            'w-icon-heartbeat', 'w-icon-gift', 'w-icon-gamepad', 'w-icon-ice-cream',
                            'w-icon-ios', 'w-icon-camera', 'w-icon-ruby', 'w-icon-category',
                        ];
                    @endphp
                    <ul class="mobile-menu">
                        @forelse($navCategories ?? [] as $category)
                            <li>
                                <a href="{{ route('frontend.category.show', $category->slug) }}">
                                    <i class="{{ $categoryIcons[$loop->index % count($categoryIcons)] }}"></i>{{ $category->name }}
                                </a>
                            </li>
                        @empty
                            <li>
                                <a href="{{ route('frontend.shop.index') }}">
                                    <i class="w-icon-category"></i>المتجر
                                </a>
                            </li>
                        @endforelse
                        <li>
                            <a href="{{ route('frontend.shop.index') }}"
                                class="font-weight-bold text-primary text-uppercase ls-25">
                                عرض كل التصنيفات<i class="w-icon-angle-left"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
