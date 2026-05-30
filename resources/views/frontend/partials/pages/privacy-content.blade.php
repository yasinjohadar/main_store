@php
    use App\Services\SiteSettingsService as SiteSettings;

    $siteName = site_setting(SiteSettings::KEY_SITE_NAME, config('app.name'));

    $sections = [
        [
            'id' => 'privacy-intro',
            'icon' => 'w-icon-verification',
            'title' => 'مقدمة',
            'paragraphs' => [
                'توضّح سياسة الخصوصية هذه كيفية جمع ' . $siteName . ' لمعلوماتك الشخصية واستخدامها وحمايتها عند استخدام الموقع والخدمات المرتبطة به.',
            ],
        ],
        [
            'id' => 'privacy-collect',
            'icon' => 'w-icon-user',
            'title' => 'المعلومات التي نجمعها',
            'paragraphs' => [
                'قد نجمع بيانات مثل الاسم، البريد الإلكتروني، رقم الجوال، عنوان الشحن، وتفاصيل الدفع عند التسجيل أو تقديم طلب أو التواصل معنا.',
                'نُسجّل أيضاً بيانات تقنية مثل عنوان IP ونوع المتصفح لتحسين الأمان وتجربة الاستخدام.',
            ],
        ],
        [
            'id' => 'privacy-use',
            'icon' => 'w-icon-headphone',
            'title' => 'كيف نستخدم معلوماتك',
            'paragraphs' => [
                'نستخدم بياناتك لمعالجة الطلبات، تقديم الدعم، تحسين الخدمات، وإرسال إشعارات مهمة متعلقة بحسابك أو مشترياتك.',
                'لن نبيع بياناتك الشخصية لأطراف ثالثة لأغراض تسويقية دون موافقتك الصريحة.',
            ],
        ],
        [
            'id' => 'privacy-security',
            'icon' => 'w-icon-verification',
            'title' => 'أمان البيانات',
            'paragraphs' => [
                'نطبّق إجراءات تقنية وتنظيمية مناسبة لحماية بياناتك من الوصول أو التعديل أو الإفصاح غير المصرّح به.',
                'رغم جهودنا، لا يمكن ضمان أمان مطلق لأي نقل عبر الإنترنت.',
            ],
        ],
        [
            'id' => 'privacy-rights',
            'icon' => 'w-icon-heart',
            'title' => 'حقوقك',
            'paragraphs' => [
                'يمكنك طلب الوصول إلى بياناتك أو تصحيحها أو حذفها بالتواصل معنا عبر صفحة اتصل بنا، وفقاً للأنظمة المعمول بها.',
            ],
        ],
    ];
@endphp

<div class="page-content store-legal-page__content mb-10 pb-2">
    <div class="container">
        <section class="store-legal-intro">
            <p class="store-legal-intro__eyebrow">حماية بياناتك</p>
            <h2 class="store-legal-intro__title">سياسة الخصوصية</h2>
            <p class="store-legal-intro__text">
                نلتزم باحترام خصوصيتك وشرح كيفية التعامل مع معلوماتك في {{ $siteName }}.
            </p>
            <p class="store-legal-intro__updated">آخر تحديث: {{ now()->translatedFormat('F Y') }}</p>
        </section>

        <div class="row gutter-lg store-legal-layout">
            <aside class="col-lg-4 col-xl-3 mb-6 mb-lg-0">
                <nav class="store-legal-nav" aria-label="فهرس سياسة الخصوصية">
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
                لأي استفسار حول الخصوصية أو معالجة البيانات، يسعدنا مساعدتك.
            </p>
            <div class="store-legal-footer__links">
                <a href="{{ route('frontend.contact') }}" class="store-legal-footer__link">
                    <i class="w-icon-envelop-closed"></i>
                    اتصل بنا
                </a>
                <a href="{{ route('frontend.terms') }}" class="store-legal-footer__link">
                    <i class="w-icon-list"></i>
                    الشروط والأحكام
                </a>
                <a href="{{ route('frontend.faq') }}" class="store-legal-footer__link">
                    <i class="w-icon-comments"></i>
                    الأسئلة الشائعة
                </a>
            </div>
        </section>
    </div>
</div>
