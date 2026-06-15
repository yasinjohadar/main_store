@php
    use App\Services\SiteSettingsService as SiteSettings;

    $siteName = site_setting(SiteSettings::KEY_SITE_NAME, config('app.name'));

    $sections = [
        [
            'icon' => 'w-icon-truck',
            'title' => 'الشحن والتوصيل',
            'items' => [
                [
                    'id' => 'faq-ship-1',
                    'q' => 'ما طرق الشحن المتاحة؟',
                    'a' => 'نوفر شحن سريع داخل المملكة عبر شركاء موثوقين، مع خيارات توصيل عادية أو سريعة حسب منطقتك وتوفر المنتج.',
                ],
                [
                    'id' => 'faq-ship-2',
                    'q' => 'كم يستغرق وصول الطلب؟',
                    'a' => 'عادةً بين 2–5 أيام عمل داخل المدن الرئيسية. قد تختلف المدة حسب الموقع وطريقة الشحن المختارة.',
                ],
                [
                    'id' => 'faq-ship-3',
                    'q' => 'هل تشحنون دولياً؟',
                    'a' => 'حالياً نركز على التوصيل داخل المملكة. للطلبات الدولية تواصل مع الدعم وسنساعدك حسب الإمكانية.',
                ],
            ],
        ],
        [
            'icon' => 'w-icon-money',
            'title' => 'الدفع',
            'items' => [
                [
                    'id' => 'faq-pay-1',
                    'q' => 'ما طرق الدفع المقبولة؟',
                    'a' => 'نقبل بطاقات مدى والفيزا والماستر كارد والدفع الإلكتروني الآمن عبر بوابات معتمدة.',
                ],
                [
                    'id' => 'faq-pay-2',
                    'q' => 'هل الشراء عبر الإنترنت آمن؟',
                    'a' => 'نعم. جميع المعاملات مشفّرة ببروتوكول SSL ولا نخزّن بيانات بطاقتك على خوادمنا.',
                ],
            ],
        ],
        [
            'icon' => 'w-icon-orders',
            'title' => 'الطلبات والإرجاع',
            'items' => [
                [
                    'id' => 'faq-order-1',
                    'q' => 'كيف أقدّم طلباً؟',
                    'a' => 'اختر المنتج، أضفه للسلة، أكمل بيانات الشحن والدفع، ثم أكّد الطلب. ستصلك رسالة تأكيد على بريدك.',
                ],
                [
                    'id' => 'faq-order-2',
                    'q' => 'كيف ألغي أو أعدّل طلبي؟',
                    'a' => 'يمكن التعديل أو الإلغاء قبل شحن الطلب من صفحة «طلباتي» أو بالتواصل مع الدعم مع ذكر رقم الطلب.',
                ],
                [
                    'id' => 'faq-order-3',
                    'q' => 'كيف أتتبع طلبي؟',
                    'a' => 'من صفحة تتبع الطلب أدخل رقم الطلب والبريد الإلكتروني، أو راجع حالة الطلب من حسابك.',
                ],
                [
                    'id' => 'faq-order-4',
                    'q' => 'كيف أُرجع منتجاً؟',
                    'a' => 'خلال 30 يوماً من الاستلام يمكنك طلب الإرجاع من حسابك أو عبر الدعم. يجب أن يكون المنتج بحالته الأصلية.',
                ],
            ],
        ],
    ];
@endphp

<div class="page-content store-faq-page__content mb-10 pb-2">
    <div class="container">
        <section class="store-faq-intro">
            <p class="store-faq-intro__eyebrow">مركز المساعدة</p>
            <h2 class="store-faq-intro__title">أسئلة شائعة</h2>
            <p class="store-faq-intro__text">
                إجابات واضحة عن التسوق، الشحن، الدفع، والإرجاع في {{ $siteName }}.
            </p>
        </section>

        @foreach($sections as $section)
            <section class="store-faq-section">
                <header class="store-faq-section__head">
                    <span class="store-faq-section__icon"><i class="{{ $section['icon'] }}"></i></span>
                    <h3 class="store-faq-section__title">{{ $section['title'] }}</h3>
                </header>

                <div class="accordion store-faq-accordion">
                    @foreach($section['items'] as $item)
                        <div class="card store-faq-accordion__item">
                            <div class="card-header store-faq-accordion__header">
                                <a href="#{{ $item['id'] }}" class="store-faq-accordion__trigger expand">
                                    <span class="store-faq-accordion__question">{{ $item['q'] }}</span>
                                    <span class="store-faq-accordion__chevron-wrap" aria-hidden="true">
                                        <i class="w-icon-angle-down store-faq-accordion__chevron"></i>
                                    </span>
                                </a>
                            </div>
                            <div id="{{ $item['id'] }}" class="card-body store-faq-accordion__body collapsed">
                                <p class="mb-0">{{ $item['a'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach

        <section class="store-faq-cta">
            <div class="store-faq-cta__inner">
                <div>
                    <h3 class="store-faq-cta__title">لم تجد إجابتك؟</h3>
                    <p class="store-faq-cta__text">فريق الدعم جاهز لمساعدتك في أي وقت.</p>
                </div>
                <div class="store-faq-cta__actions">
                    <a href="{{ route('frontend.contact') }}" class="store-faq-cta__btn store-faq-cta__btn--primary">
                        <i class="w-icon-envelop-closed"></i>
                        اتصل بنا
                    </a>
                    <a href="{{ route('frontend.track-order') }}" class="store-faq-cta__btn store-faq-cta__btn--outline">
                        <i class="w-icon-truck"></i>
                        تتبع طلب
                    </a>
                </div>
            </div>
        </section>
    </div>
</div>
