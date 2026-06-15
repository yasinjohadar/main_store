@php
    use App\Services\SiteSettingsService as SiteSettings;

    $siteName = site_setting(SiteSettings::KEY_SITE_NAME, config('app.name'));

    $sections = [
        [
            'id' => 'terms-intro',
            'icon' => 'w-icon-list',
            'title' => 'مقدمة',
            'paragraphs' => [
                'باستخدامك لموقع ' . $siteName . ' وخدماته، فإنك توافق على الالتزام بهذه الشروط والأحكام. يُرجى قراءتها بعناية قبل إتمام أي عملية شراء.',
            ],
        ],
        [
            'id' => 'terms-use',
            'icon' => 'w-icon-map-marker',
            'title' => 'استخدام الموقع',
            'paragraphs' => [
                'تتعهد باستخدام الموقع لأغراض مشروعة فقط، وعدم الإضرار بحقوق الآخرين أو تعطيل عمل المنصة.',
                'يُحظر نسخ المحتوى أو إعادة بيع المنتجات دون إذن كتابي مسبق من الإدارة.',
            ],
        ],
        [
            'id' => 'terms-orders',
            'icon' => 'w-icon-orders',
            'title' => 'الطلبات والدفع',
            'paragraphs' => [
                'جميع الطلبات خاضعة للتوفر والتأكيد. قد تتغير الأسعار ووصف المنتجات دون إشعار مسبق.',
                'يجب استلام الدفع أو تأكيده قبل معالجة الطلب وشحنه.',
            ],
        ],
        [
            'id' => 'terms-shipping',
            'icon' => 'w-icon-truck',
            'title' => 'الشحن والإرجاع',
            'paragraphs' => [
                'مدة التوصيل تقديرية وقد تختلف حسب المنطقة وطريقة الشحن.',
                'تُطبَّق سياسة الإرجاع كما هو موضّح في صفحة الأسئلة الشائعة وقد تختلف حسب نوع المنتج.',
            ],
        ],
        [
            'id' => 'terms-liability',
            'icon' => 'w-icon-verification',
            'title' => 'حدود المسؤولية',
            'paragraphs' => [
                'لا نتحمل المسؤولية عن أي أضرار غير مباشرة أو عرضية ناتجة عن استخدام الموقع أو المنتجات، في الحدود التي يسمح بها النظام المعمول به.',
            ],
        ],
    ];
@endphp

<div class="page-content store-legal-page__content mb-10 pb-2">
    <div class="container">
        <section class="store-legal-intro">
            <div class="store-legal-intro__inner">
                <p class="store-legal-intro__eyebrow">الشروط القانونية</p>
                <p class="store-legal-intro__text">
                    توضّح هذه الصفحة حقوقك والتزاماتك عند التسوق من {{ $siteName }}.
                </p>
                <p class="store-legal-intro__updated">
                    <i class="w-icon-calendar" aria-hidden="true"></i>
                    آخر تحديث: {{ now()->translatedFormat('F Y') }}
                </p>
            </div>
        </section>

        <div class="row gutter-lg store-legal-layout">
            <aside class="col-lg-4 col-xl-3 mb-6 mb-lg-0">
                <nav class="store-legal-nav" aria-label="فهرس الشروط">
                    <p class="store-legal-nav__label">محتويات الصفحة</p>
                    <ul class="store-legal-nav__list">
                        @foreach($sections as $index => $section)
                            <li>
                                <a href="#{{ $section['id'] }}" class="store-legal-nav__link">
                                    <span class="store-legal-nav__num">{{ $index + 1 }}</span>
                                    <span>{{ $section['title'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </aside>

            <div class="col-lg-8 col-xl-9">
                <div class="store-legal-body">
                    @foreach($sections as $section)
                        <article id="{{ $section['id'] }}" class="store-legal-block">
                            <header class="store-legal-block__head">
                                <span class="store-legal-block__icon"><i class="{{ $section['icon'] }}"></i></span>
                                <h3 class="store-legal-block__title">{{ $section['title'] }}</h3>
                            </header>
                            <div class="store-legal-block__content">
                                @foreach($section['paragraphs'] as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>

        <section class="store-legal-footer">
            <p class="store-legal-footer__note">
                للاستفسارات القانونية أو التجارية، يمكنك التواصل معنا عبر صفحة اتصل بنا.
            </p>
            <div class="store-legal-footer__links">
                <a href="{{ route('frontend.contact') }}" class="store-legal-footer__link">
                    <i class="w-icon-envelop-closed"></i>
                    اتصل بنا
                </a>
                <a href="{{ route('frontend.faq') }}" class="store-legal-footer__link">
                    <i class="w-icon-comments"></i>
                    الأسئلة الشائعة
                </a>
                <a href="{{ route('frontend.privacy') }}" class="store-legal-footer__link">
                    <i class="w-icon-verification"></i>
                    سياسة الخصوصية
                </a>
            </div>
        </section>
    </div>
</div>
