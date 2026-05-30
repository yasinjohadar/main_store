@php
    use App\Services\SiteSettingsService as SiteSettings;

    $contactEmail = site_setting(SiteSettings::KEY_SITE_CONTACT_EMAIL, config('mail.from.address', 'info@example.com'));
    $contactPhone = site_setting(SiteSettings::KEY_SITE_CONTACT_PHONE, '920000000');
    $contactAddress = site_setting(SiteSettings::KEY_SITE_ADDRESS, 'المملكة العربية السعودية');
    $whatsapp = site_setting(SiteSettings::KEY_SITE_WHATSAPP_NUMBER);
    $siteName = site_setting(SiteSettings::KEY_SITE_NAME, config('app.name'));

    $infoCards = [
        [
            'icon' => 'w-icon-envelop-closed',
            'title' => 'البريد الإلكتروني',
            'value' => $contactEmail,
            'link' => 'mailto:' . $contactEmail,
            'accent' => '#336699',
        ],
        [
            'icon' => 'w-icon-headphone',
            'title' => 'الهاتف',
            'value' => $contactPhone,
            'link' => 'tel:' . preg_replace('/\s+/', '', $contactPhone),
            'accent' => '#0d9488',
        ],
        [
            'icon' => 'w-icon-map-marker',
            'title' => 'العنوان',
            'value' => $contactAddress,
            'link' => null,
            'accent' => '#7c3aed',
        ],
        [
            'icon' => 'w-icon-fax',
            'title' => 'ساعات الدعم',
            'value' => 'السبت - الخميس، 9 ص - 6 م',
            'link' => null,
            'accent' => '#ea580c',
        ],
    ];

    $faqs = [
        [
            'id' => 'contact-faq-1',
            'q' => 'كيف يمكنني إلغاء طلبي؟',
            'a' => 'يمكنك إلغاء الطلب من صفحة «طلباتي» قبل شحنه، أو التواصل مع فريق الدعم عبر هذا النموذج مع ذكر رقم الطلب.',
            'open' => true,
        ],
        [
            'id' => 'contact-faq-2',
            'q' => 'كيف أتتبع طلبي؟',
            'a' => 'استخدم صفحة تتبع الطلب وأدخل رقم الطلب والبريد الإلكتروني لمتابعة حالة الشحن.',
            'open' => false,
        ],
        [
            'id' => 'contact-faq-3',
            'q' => 'ما طرق الدفع المتاحة؟',
            'a' => 'نوفر الدفع الإلكتروني الآمن وبطاقات مدى والفيزا والماستر كارد حسب ما يظهر عند إتمام الشراء.',
            'open' => false,
        ],
        [
            'id' => 'contact-faq-4',
            'q' => 'متى سأستلم رداً على رسالتي؟',
            'a' => 'نرد عادة خلال 24 ساعة عمل. للاستفسارات العاجلة يُفضّل الاتصال الهاتفي أو الواتساب.',
            'open' => false,
        ],
    ];
@endphp

<div class="page-content store-contact-page__content mb-10 pb-2">
    <div class="container">
        <section class="store-contact-intro">
            <p class="store-contact-intro__eyebrow">نحن هنا لمساعدتك</p>
            <h2 class="store-contact-intro__title">تواصل مع {{ $siteName }}</h2>
            <p class="store-contact-intro__text">
                لديك سؤال عن منتج، طلب، أو استفسار عام؟ فريقنا جاهز للرد عليك بأسرع وقت.
            </p>
        </section>

        <section class="store-contact-info" aria-label="معلومات التواصل">
            <div class="store-contact-info__grid">
                @foreach($infoCards as $card)
                    <article class="store-contact-info__card">
                        <span class="store-contact-info__icon" style="--card-accent: {{ $card['accent'] }}">
                            <i class="{{ $card['icon'] }}"></i>
                        </span>
                        <h3 class="store-contact-info__card-title">{{ $card['title'] }}</h3>
                        @if($card['link'])
                            <a href="{{ $card['link'] }}" class="store-contact-info__card-value">{{ $card['value'] }}</a>
                        @else
                            <p class="store-contact-info__card-value">{{ $card['value'] }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>

        <section class="store-contact-main">
            <div class="row gutter-lg align-items-start">
                <div class="col-lg-5 mb-8 mb-lg-0">
                    <div class="store-contact-panel store-contact-panel--faq">
                        <div class="store-contact-panel__head">
                            <span class="store-contact-panel__badge"><i class="w-icon-comments"></i></span>
                            <div>
                                <h3 class="store-contact-panel__title">أسئلة شائعة</h3>
                                <p class="store-contact-panel__subtitle">إجابات سريعة قبل إرسال رسالتك</p>
                            </div>
                        </div>

                        <div class="accordion accordion-bg accordion-gutter-md accordion-border store-contact-faq">
                            @foreach($faqs as $item)
                                <div class="card">
                                    <div class="card-header">
                                        <a href="#{{ $item['id'] }}" class="{{ $item['open'] ? 'collapse' : 'expand' }}">{{ $item['q'] }}</a>
                                    </div>
                                    <div id="{{ $item['id'] }}" class="card-body {{ $item['open'] ? 'expanded' : 'collapsed' }}">
                                        <p class="mb-0">{{ $item['a'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="store-contact-panel__footer">
                            <a href="{{ route('frontend.faq') }}" class="store-contact-panel__link">
                                المزيد من الأسئلة
                                <i class="w-icon-long-arrow-left"></i>
                            </a>
                            <a href="{{ route('frontend.track-order') }}" class="store-contact-panel__link">
                                تتبع طلب
                                <i class="w-icon-long-arrow-left"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="store-contact-panel store-contact-panel--form">
                        <div class="store-contact-panel__head">
                            <span class="store-contact-panel__badge store-contact-panel__badge--primary">
                                <i class="w-icon-envelop3"></i>
                            </span>
                            <div>
                                <h3 class="store-contact-panel__title">أرسل لنا رسالة</h3>
                                <p class="store-contact-panel__subtitle">املأ النموذج وسنعود إليك قريباً</p>
                            </div>
                        </div>

                        @if(session('success'))
                            <div class="store-contact-alert store-contact-alert--success">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="store-contact-alert store-contact-alert--error">{{ session('error') }}</div>
                        @endif

                        <form class="store-contact-form" method="POST" action="{{ route('frontend.contact') }}">
                            @csrf
                            <div class="row store-contact-form__row">
                                <div class="col-md-6">
                                    <div class="store-contact-form__group">
                                        <label for="name">الاسم الكامل <span>*</span></label>
                                        <input type="text" id="name" name="name" class="form-control store-contact-form__input"
                                            value="{{ old('name') }}" placeholder="اسمك" required>
                                        @error('name')<p class="store-contact-form__error">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="store-contact-form__group">
                                        <label for="email">البريد الإلكتروني <span>*</span></label>
                                        <input type="email" id="email" name="email" class="form-control store-contact-form__input"
                                            value="{{ old('email') }}" placeholder="example@email.com" required>
                                        @error('email')<p class="store-contact-form__error">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row store-contact-form__row">
                                <div class="col-md-6">
                                    <div class="store-contact-form__group">
                                        <label for="phone">رقم الجوال</label>
                                        <input type="text" id="phone" name="phone" class="form-control store-contact-form__input"
                                            value="{{ old('phone') }}" placeholder="05xxxxxxxx">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="store-contact-form__group">
                                        <label for="subject">الموضوع <span>*</span></label>
                                        <input type="text" id="subject" name="subject" class="form-control store-contact-form__input"
                                            value="{{ old('subject') }}" placeholder="موضوع الرسالة" required>
                                        @error('subject')<p class="store-contact-form__error">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="store-contact-form__group">
                                <label for="message">رسالتك <span>*</span></label>
                                <textarea id="message" name="message" rows="6" class="form-control store-contact-form__input store-contact-form__textarea"
                                    placeholder="اكتب تفاصيل استفسارك هنا..." required>{{ old('message') }}</textarea>
                                @error('message')<p class="store-contact-form__error">{{ $message }}</p>@enderror
                            </div>

                            <div class="store-contact-form__actions">
                                <button type="submit" class="store-contact-form__submit">
                                    <i class="w-icon-envelop-closed"></i>
                                    إرسال الرسالة
                                </button>
                                @if($whatsapp)
                                    <a href="https://wa.me/{{ preg_replace('/\D+/', '', $whatsapp) }}" class="store-contact-form__whatsapp" target="_blank" rel="noopener noreferrer">
                                        <i class="fab fa-whatsapp"></i>
                                        واتساب
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
