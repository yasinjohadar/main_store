        <!-- Start::app-sidebar -->
        <aside class="app-sidebar sticky" id="sidebar">

            @php
                use App\Services\SiteSettingsService;
                $adminStoreName = site_setting(SiteSettingsService::KEY_SITE_NAME, config('app.name'));
                $adminStoreInitials = mb_strtoupper(mb_substr(trim($adminStoreName), 0, 2));
            @endphp

            <!-- Start::main-sidebar-header -->
            <div class="main-sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="header-logo admin-sidebar-brand" title="{{ $adminStoreName }}">
                    <span class="admin-sidebar-brand__full desktop-logo">{{ $adminStoreName }}</span>
                    <span class="admin-sidebar-brand__short toggle-logo">{{ $adminStoreInitials }}</span>
                </a>
            </div>
            <!-- End::main-sidebar-header -->

            <!-- Start::main-sidebar -->
            <div class="main-sidebar" id="sidebar-scroll">

                <!-- Start::nav -->
                <nav class="main-menu-container nav nav-pills flex-column sub-open">
                    <div class="slide-left" id="slide-left">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"> <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path> </svg>
                    </div>
                    <ul class="main-menu">
                        <!-- الواجهة الخارجية -->
                        <li class="slide">
                            <a href="{{ route('frontend.home') }}" class="side-menu__item admin-sidebar-frontend-link" target="_blank" rel="noopener noreferrer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                    <polyline points="15 3 21 3 21 9"/>
                                    <line x1="10" y1="14" x2="21" y2="3"/>
                                </svg>
                                <span class="side-menu__label">الواجهة الخارجية</span>
                            </a>
                        </li>

                        <!-- Start::slide__category -->
                        <li class="slide__category"><span class="category-name">مركز الإدارة</span></li>
                        <!-- End::slide__category -->

                        <!-- Start::slide — الرئيسية -->
                        <li class="slide {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('admin.dashboard') }}" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/><path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/></svg>
                                <span class="side-menu__label">الصفحة الرئيسية</span>
                            </a>
                        </li>

                        <!-- المستخدمون والصلاحيات -->
                        <li class="slide has-sub {{ request()->routeIs('roles.*', 'users.*', 'admin.customers.*') ? 'open active' : '' }}">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                <span class="side-menu__label">المستخدمون والصلاحيات</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                <li class="slide {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                    <a href="{{ route('roles.index') }}" class="side-menu__item">الصلاحيات</a>
                                </li>
                                <li class="slide {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                    <a href="{{ route('users.index') }}" class="side-menu__item">المستخدمون</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.customers.index') }}" class="side-menu__item">العملاء</a>
                                </li>
                            </ul>
                        </li>

                        <!-- المنتجات والمخزون -->
                        <li class="slide has-sub {{ request()->routeIs('admin.categories.*', 'admin.brands.*', 'admin.products.*', 'admin.attributes.*', 'admin.inventory.*') ? 'open active' : '' }}">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                                <span class="side-menu__label">المنتجات والمخزون</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                <li class="slide {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.categories.index') }}" class="side-menu__item">التصنيفات</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.brands.index') }}" class="side-menu__item">الماركات</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.products.index') }}" class="side-menu__item">المنتجات</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.attributes.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.attributes.index') }}" class="side-menu__item">سمات المنتجات</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.inventory.index') }}" class="side-menu__item">المخزون</a>
                                </li>
                            </ul>
                        </li>

                        <!-- المبيعات والطلبات -->
                        <li class="slide has-sub {{ request()->routeIs('admin.orders.*', 'admin.order-returns.*') ? 'open active' : '' }}">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                <span class="side-menu__label">المبيعات والطلبات</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                <li class="slide {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.orders.index') }}" class="side-menu__item">الطلبات</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.order-returns.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.order-returns.index') }}" class="side-menu__item">طلبات المرتجع</a>
                                </li>
                            </ul>
                        </li>

                        <!-- العروض والدفع -->
                        <li class="slide has-sub {{ request()->routeIs('admin.coupons.*', 'admin.loyalty.*', 'admin.payment-methods.*', 'admin.payments.*', 'admin.stripe-settings.*') ? 'open active' : '' }}">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 12V8H6a2 2 0 0 1-2-2c0-1.1.9-2 2-2h12v4"/>
                                    <path d="M4 6v12c0 1.1.9 2 2 2h14v-4"/>
                                    <path d="M18 12a2 2 0 0 0-2 2c0 1.1.9 2 2 2h4v-4h-4z"/>
                                </svg>
                                <span class="side-menu__label">العروض والدفع</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                <li class="slide {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.coupons.index') }}" class="side-menu__item">الكوبونات</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.loyalty.settings.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.loyalty.settings.index') }}" class="side-menu__item">إعدادات نقاط الولاء</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.loyalty.transactions.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.loyalty.transactions.index') }}" class="side-menu__item">سجل حركات النقاط</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.payment-methods.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.payment-methods.index') }}" class="side-menu__item">وسائل الدفع</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.payments.index') }}" class="side-menu__item">سجل المدفوعات</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.stripe-settings.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.stripe-settings.index') }}" class="side-menu__item">إعدادات Stripe</a>
                                </li>
                            </ul>
                        </li>

                        <!-- التسويق والتقارير -->
                        <li class="slide has-sub {{ request()->routeIs('admin.reviews.*', 'admin.review-settings.*', 'admin.wishlists.*', 'admin.marketing.*', 'admin.reports.*') ? 'open active' : '' }}">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 3v18h18"/>
                                    <path d="M7 16l4-8 4 5 5-9"/>
                                </svg>
                                <span class="side-menu__label">التسويق والتقارير</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                <li class="slide {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.reviews.index') }}" class="side-menu__item">آراء العملاء</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.review-settings.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.review-settings.index') }}" class="side-menu__item">إعدادات التقييمات</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.wishlists.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.wishlists.index') }}" class="side-menu__item">المفضلة</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.marketing.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.marketing.dashboard') }}" class="side-menu__item">التسويق والتحليلات</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.reports.dashboard') }}" class="side-menu__item">التقارير</a>
                                </li>
                            </ul>
                        </li>

                        <!-- الشحن والمالية -->
                        <li class="slide has-sub {{ request()->routeIs('admin.shipping.*', 'admin.tax.*', 'admin.currencies.*') ? 'open active' : '' }}">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 3h18v13H3z"/>
                                    <path d="M16 21H8a2 2 0 0 1-2-2v-3h12v3a2 2 0 0 1-2 2z"/>
                                    <path d="M7 21v-2"/>
                                    <path d="M17 21v-2"/>
                                </svg>
                                <span class="side-menu__label">الشحن والمالية</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                <li class="slide {{ request()->routeIs('admin.shipping.zones.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.shipping.zones.index') }}" class="side-menu__item">مناطق وطرق الشحن</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.tax.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.tax.index') }}" class="side-menu__item">فئات ومعدلات الضرائب</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.currencies.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.currencies.index') }}" class="side-menu__item">العملات</a>
                                </li>
                            </ul>
                        </li>

                        <!-- إعدادات النظام -->
                        <li class="slide has-sub {{ request()->routeIs('admin.site-settings.*', 'admin.store-settings.*', 'admin.integration-settings.*', 'admin.system-status.*', 'admin.activity-log.*') ? 'open active' : '' }}">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"/>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                                </svg>
                                <span class="side-menu__label">إعدادات النظام</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                <li class="slide {{ request()->routeIs('admin.site-settings.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.site-settings.index') }}" class="side-menu__item">إعدادات الموقع</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.store-settings.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.store-settings.index') }}" class="side-menu__item">إعدادات المتجر</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.integration-settings.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.integration-settings.index') }}" class="side-menu__item">التكاملات والدفع</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.system-status.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.system-status.index') }}" class="side-menu__item">حالة النظام</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.activity-log.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.activity-log.index') }}" class="side-menu__item">سجل النشاط</a>
                                </li>
                            </ul>
                        </li>

                        <!-- المحتوى -->
                        <li class="slide has-sub {{ request()->routeIs('admin.blog.*', 'admin.home-slides.*', 'admin.homepage.*') ? 'open active' : '' }}">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                    <line x1="8" y1="7" x2="18" y2="7"/>
                                    <line x1="8" y1="11" x2="18" y2="11"/>
                                </svg>
                                <span class="side-menu__label">المحتوى والمدونة</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                <li class="slide {{ request()->routeIs('admin.blog.posts.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.blog.posts.index') }}" class="side-menu__item">المقالات</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.blog.ai-posts.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.blog.ai-posts.create') }}" class="side-menu__item">إنشاء مقال بالذكاء الاصطناعي</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.blog.categories.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.blog.categories.index') }}" class="side-menu__item">تصنيفات المدونة</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.homepage.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.homepage.index') }}" class="side-menu__item">الصفحة الرئيسية</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.home-slides.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.home-slides.index') }}" class="side-menu__item">سلاider الرئيسية</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.blog.tags.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.blog.tags.index') }}" class="side-menu__item">وسوم المدونة</a>
                                </li>
                            </ul>
                        </li>

                        <!-- البنية التحتية -->
                        <li class="slide has-sub {{ request()->routeIs('admin.settings.email.*', 'admin.email-templates.*', 'admin.storage.*', 'admin.storage-disk-mappings.*', 'admin.backups.*', 'admin.backup-schedules.*', 'admin.backup-storage.*') ? 'open active' : '' }}">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                                    <line x1="12" y1="22.08" x2="12" y2="12"/>
                                </svg>
                                <span class="side-menu__label">البنية التحتية</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                <li class="slide {{ request()->routeIs('admin.settings.email.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.settings.email.index') }}" class="side-menu__item">إعدادات البريد (SMTP)</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.email-templates.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.email-templates.index') }}" class="side-menu__item">قوالب الإيميلات</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.storage.index', 'admin.storage.create', 'admin.storage.analytics', 'admin.storage.show', 'admin.storage.edit') ? 'active' : '' }}">
                                    <a href="{{ route('admin.storage.index') }}" class="side-menu__item">التخزين السحابي</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.storage-disk-mappings.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.storage-disk-mappings.index') }}" class="side-menu__item">ربط الأقراص</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.backups.*', 'admin.backup-schedules.*', 'admin.backup-storage.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.backups.index') }}" class="side-menu__item">النسخ الاحتياطي</a>
                                </li>
                            </ul>
                        </li>

                        <!-- التواصل والذكاء الاصطناعي -->
                        <li class="slide has-sub {{ request()->routeIs('admin.whatsapp*', 'admin.ai.*') ? 'open active' : '' }}">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                                    <path d="M2 17l10 5 10-5"/>
                                    <path d="M2 12l10 5 10-5"/>
                                </svg>
                                <span class="side-menu__label">التواصل والذكاء الاصطناعي</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                <li class="slide {{ request()->routeIs('admin.whatsapp-messages.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.whatsapp-messages.index') }}" class="side-menu__item">رسائل واتساب</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.whatsapp-settings.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.whatsapp-settings.index') }}" class="side-menu__item">إعدادات Meta API</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.whatsapp-web.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.whatsapp-web.connect') }}" class="side-menu__item">واتساب ويب</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.whatsapp-web-settings.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.whatsapp-web-settings.index') }}" class="side-menu__item">إعدادات واتساب ويب</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.ai.models.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.ai.models.index') }}" class="side-menu__item">نماذج AI</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.ai.settings.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.ai.settings.index') }}" class="side-menu__item">إعدادات AI</a>
                                </li>
                            </ul>
                        </li>

                        <!-- End::slide -->


                    </ul>
                    <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"> <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path> </svg></div>
                </nav>
                <!-- End::nav -->

            </div>
            <!-- End::main-sidebar -->

        </aside>
        <!-- End::app-sidebar -->
