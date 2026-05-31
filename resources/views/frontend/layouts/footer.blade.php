        <footer class="footer store-footer appear-animate" data-animation-options="{'name': 'fadeIn'}">
            <div class="store-footer__newsletter">
                <div class="container">
                    <div class="store-footer__newsletter-inner">
                        <div class="store-footer__newsletter-text">
                            <span class="store-footer__eyebrow">ابقَ على اطلاع</span>
                            <h4 class="store-footer__newsletter-title">اشترك في نشرتنا البريدية</h4>
                            <p class="store-footer__newsletter-desc">عروض حصرية، منتجات جديدة، وخصومات تصل إلى بريدك مباشرة.</p>
                        </div>
                        <form action="#" method="post" class="store-footer__newsletter-form">
                            @csrf
                            <div class="store-footer__newsletter-field">
                                <i class="w-icon-envelop2"></i>
                                <input type="email" class="form-control" name="email"
                                    placeholder="أدخل بريدك الإلكتروني" required>
                            </div>
                            <button class="store-footer__newsletter-btn" type="submit">
                                <span>اشتراك</span>
                                <i class="w-icon-long-arrow-left"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="store-footer__main">
                <div class="container">
                    <div class="row gutter-lg">
                        <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                            <div class="store-footer__brand">
                                @php($footerSiteName = site_setting(\App\Services\SiteSettingsService::KEY_SITE_NAME, config('app.name')))
                                @php($footerLogoUrl = site_setting_url(\App\Services\SiteSettingsService::KEY_SITE_LOGO))
                                <a href="{{ route('frontend.home') }}" class="store-footer__logo" title="{{ $footerSiteName }}">
                                    @if($footerLogoUrl)
                                        <img src="{{ $footerLogoUrl }}" alt="{{ $footerSiteName }}" width="130" height="40" loading="lazy" style="object-fit: contain; max-height: 40px;">
                                    @else
                                        <span class="store-site-logo store-site-logo--footer">{{ $footerSiteName }}</span>
                                    @endif
                                </a>
                                <p class="store-footer__about">
                                    متجرك الإلكتروني الموثوق — منتجات مختارة بعناية، شحن سريع، ودعم عملاء على مدار الساعة.
                                </p>
                                <a href="tel:920000000" class="store-footer__phone">
                                    <span class="store-footer__phone-icon"><i class="w-icon-call"></i></span>
                                    <span>
                                        <small>اتصل بنا</small>
                                        <strong>920000000</strong>
                                    </span>
                                </a>
                                <div class="store-footer__social">
                                    <a href="#" class="store-footer__social-link" aria-label="فيسبوك"><i class="w-icon-facebook"></i></a>
                                    <a href="#" class="store-footer__social-link" aria-label="انستغرام"><i class="w-icon-instagram"></i></a>
                                    <a href="#" class="store-footer__social-link" aria-label="تويتر"><i class="w-icon-twitter"></i></a>
                                    <a href="#" class="store-footer__social-link" aria-label="يوتيوب"><i class="w-icon-youtube"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-lg-2 mb-4 mb-lg-0">
                            <div class="store-footer__widget">
                                <h4 class="store-footer__widget-title">روابط سريعة</h4>
                                <ul class="store-footer__links">
                                    <li><a href="{{ route('frontend.shop.index') }}">المتجر</a></li>
                                    <li><a href="{{ route('frontend.blog.index') }}">المدونة</a></li>
                                    <li><a href="{{ route('frontend.about') }}">من نحن</a></li>
                                    <li><a href="{{ route('frontend.contact') }}">اتصل بنا</a></li>
                                    <li><a href="{{ route('frontend.faq') }}">الأسئلة الشائعة</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-6 col-lg-2 mb-4 mb-lg-0">
                            <div class="store-footer__widget">
                                <h4 class="store-footer__widget-title">حسابي</h4>
                                <ul class="store-footer__links">
                                    <li><a href="{{ route('frontend.account') }}">لوحة التحكم</a></li>
                                    <li><a href="{{ route('frontend.cart.index') }}">السلة</a></li>
                                    <li><a href="{{ route('frontend.wishlist') }}">المفضلة</a></li>
                                    <li><a href="{{ route('frontend.compare') }}">المقارنة</a></li>
                                    @guest
                                        <li><a href="{{ route('login') }}">تسجيل الدخول</a></li>
                                    @endguest
                                </ul>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="store-footer__widget">
                                <h4 class="store-footer__widget-title">التصنيفات</h4>
                                <ul class="store-footer__categories">
                                    @forelse(($navCategories ?? collect())->take(6) as $category)
                                        <li>
                                            <a href="{{ route('frontend.category.show', $category->slug) }}">
                                                {{ $category->name }}
                                            </a>
                                        </li>
                                    @empty
                                        <li><a href="{{ route('frontend.shop.index') }}">المتجر</a></li>
                                    @endforelse
                                </ul>
                                <a href="{{ route('frontend.shop.index') }}" class="store-footer__view-all">
                                    عرض جميع التصنيفات<i class="w-icon-long-arrow-left"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="store-footer__bottom">
                <div class="container">
                    <div class="store-footer__bottom-inner">
                        <p class="store-footer__copyright">
                            &copy; {{ date('Y') }} {{ config('app.name') }}. جميع الحقوق محفوظة.
                        </p>
                        <div class="store-footer__payments">
                            <img src="{{ asset('frontend/assets/images/payment.png') }}" alt="طرق الدفع"
                                width="159" height="25" loading="lazy">
                        </div>
                    </div>
                </div>
            </div>
        </footer>
