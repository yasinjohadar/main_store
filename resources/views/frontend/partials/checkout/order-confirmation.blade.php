@php
    $payment = $order->payments->first();
    $paymentMethod = $payment?->paymentMethod;
    $shippingAddress = $order->shipping_address;
    $currencyLabel = ($order->currency ?? 'SAR') === 'SAR' ? 'ر.س' : ($order->currency ?? 'SAR');
    $itemCount = $order->items->sum('quantity');
    $customerEmail = $shippingAddress?->email ?? $order->guest_email ?? $order->user?->email;
@endphp

<div class="store-checkout-success-hero">
    <div class="store-checkout-success-hero__icon" aria-hidden="true">
        <i class="w-icon-check"></i>
    </div>
    <div class="store-checkout-success-hero__body">
        <p class="store-checkout-success-hero__eyebrow">تم استلام طلبك</p>
        <h1 class="store-checkout-success-hero__title">شكراً لك! طلبك قيد المعالجة</h1>
        <p class="store-checkout-success-hero__meta">
            @if($customerEmail)
                أرسلنا تأكيداً إلى <strong>{{ $customerEmail }}</strong>.
            @else
                احتفظ برقم الطلب للمتابعة.
            @endif
        </p>
    </div>
    <div class="store-checkout-success-hero__badge">
        <span class="store-checkout-success-hero__badge-label">رقم الطلب</span>
        <strong class="store-checkout-success-hero__badge-value">{{ $order->order_number }}</strong>
        <span class="store-checkout-success-hero__badge-date">
            {{ $order->created_at->locale('ar')->translatedFormat('j F Y — h:i A') }}
        </span>
    </div>
</div>

<div class="row gutter-lg store-checkout-success-layout">
    <div class="col-lg-8 mb-6 store-checkout-success-main">
        <section class="store-checkout-card store-checkout-success-card">
            <div class="store-checkout-card__head">
                <span class="store-checkout-card__icon"><i class="w-icon-cart"></i></span>
                <div>
                    <h2 class="store-checkout-card__title">المنتجات المطلوبة</h2>
                    <p class="store-checkout-card__subtitle">{{ $itemCount }} منتج في هذا الطلب</p>
                </div>
            </div>
            <div class="store-checkout-card__body store-checkout-success-card__body">
                <ul class="store-checkout-order__items store-checkout-success-items">
                    @foreach($order->items as $item)
                        @php
                            $imageUrl = $item->product?->primary_image_url
                                ?? asset('frontend/assets/images/cart/product-1.jpg');
                        @endphp
                        <li class="store-checkout-order__item store-checkout-success-item">
                            <div class="store-checkout-order__item-product">
                                <span class="store-checkout-order__item-thumb">
                                    @if($item->product)
                                        <a href="{{ route('frontend.product.show', $item->product->slug) }}">
                                            <img src="{{ $imageUrl }}" alt="{{ $item->product_name }}" width="64" height="64" loading="lazy">
                                        </a>
                                    @else
                                        <img src="{{ $imageUrl }}" alt="{{ $item->product_name }}" width="64" height="64" loading="lazy">
                                    @endif
                                </span>
                                <span class="store-checkout-order__item-name">
                                    @if($item->product)
                                        <a href="{{ route('frontend.product.show', $item->product->slug) }}" class="store-checkout-success-item__link">
                                            {{ $item->product_name }}
                                        </a>
                                    @else
                                        {{ $item->product_name }}
                                    @endif
                                    <small>× {{ $item->quantity }}</small>
                                    @if($item->variant_description)
                                        <small class="store-checkout-success-item__variant">{{ $item->variant_description }}</small>
                                    @endif
                                    @if($item->sku)
                                        <small class="store-checkout-success-item__sku">SKU: {{ $item->sku }}</small>
                                    @endif
                                </span>
                            </div>
                            <strong>{{ number_format((float) $item->total, 2) }} {{ $currencyLabel }}</strong>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        @if($shippingAddress)
            <section class="store-checkout-card store-checkout-success-card">
                <div class="store-checkout-card__head">
                    <span class="store-checkout-card__icon"><i class="w-icon-map-marker"></i></span>
                    <div>
                        <h2 class="store-checkout-card__title">عنوان الشحن</h2>
                        <p class="store-checkout-card__subtitle">سيتم التوصيل إلى هذا العنوان</p>
                    </div>
                </div>
                <div class="store-checkout-card__body">
                    <div class="store-checkout-success-address">
                        <p class="store-checkout-success-address__name">{{ $shippingAddress->full_name }}</p>
                        <p class="store-checkout-success-address__line">{{ $shippingAddress->full_address }}</p>
                        @if($shippingAddress->phone)
                            <p class="store-checkout-success-address__meta">
                                <i class="w-icon-phone"></i>
                                <span>{{ $shippingAddress->phone }}</span>
                            </p>
                        @endif
                        @if($shippingAddress->email)
                            <p class="store-checkout-success-address__meta">
                                <i class="w-icon-envelop3"></i>
                                <span>{{ $shippingAddress->email }}</span>
                            </p>
                        @endif
                    </div>
                </div>
            </section>
        @endif

        @if($order->customer_note)
            <section class="store-checkout-card store-checkout-success-card">
                <div class="store-checkout-card__head">
                    <span class="store-checkout-card__icon"><i class="w-icon-comments"></i></span>
                    <div>
                        <h2 class="store-checkout-card__title">ملاحظات الطلب</h2>
                    </div>
                </div>
                <div class="store-checkout-card__body">
                    <p class="store-checkout-success-note mb-0">{{ $order->customer_note }}</p>
                </div>
            </section>
        @endif

        @php
            $downloads = $order->items->flatMap(fn ($item) => $item->downloads ?? collect());
        @endphp
        @if($downloads->isNotEmpty())
            <section class="store-checkout-card store-checkout-success-card">
                <div class="store-checkout-card__head">
                    <span class="store-checkout-card__icon"><i class="w-icon-download"></i></span>
                    <div>
                        <h2 class="store-checkout-card__title">المنتجات الرقمية</h2>
                        <p class="store-checkout-card__subtitle">يمكنك تحميل ملفاتك من هنا</p>
                    </div>
                </div>
                <div class="store-checkout-card__body">
                    <ul class="store-checkout-success-downloads">
                        @foreach($downloads as $download)
                            <li class="store-checkout-success-download">
                                @if($download->canDownload())
                                    <a href="{{ route('frontend.downloads.show', $download->download_token) }}" class="store-checkout-success-download__link">
                                        <i class="w-icon-download"></i>
                                        <span>{{ $download->file?->name ?? 'تحميل الملف' }}</span>
                                    </a>
                                @else
                                    <span class="store-checkout-success-download__disabled">
                                        <i class="w-icon-download"></i>
                                        <span>{{ $download->file?->name ?? 'ملف رقمي' }} — غير متاح حالياً</span>
                                    </span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </section>
        @endif

        @if($paymentMethod?->driver === 'bank_transfer')
            <div class="store-checkout-success-notice store-checkout-success-notice--info">
                <span class="store-checkout-success-notice__icon"><i class="w-icon-money"></i></span>
                <div>
                    <strong>تحويل بنكي</strong>
                    <p class="mb-0">يرجى تحويل المبلغ <strong>{{ number_format((float) $order->total, 2) }} {{ $currencyLabel }}</strong> واستخدام رقم الطلب <code>{{ $order->order_number }}</code> كمرجع في التحويل.</p>
                </div>
            </div>
        @elseif($paymentMethod?->driver === 'cod')
            <div class="store-checkout-success-notice store-checkout-success-notice--info">
                <span class="store-checkout-success-notice__icon"><i class="w-icon-truck"></i></span>
                <div>
                    <strong>الدفع عند الاستلام</strong>
                    <p class="mb-0">سيتم التواصل معك لتأكيد الطلب قبل الشحن. جهّز المبلغ عند الاستلام.</p>
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4 mb-6 store-checkout-sidebar">
        <aside class="store-checkout-order store-checkout-success-summary">
            <div class="store-checkout-order__head">
                <h2 class="store-checkout-order__title">ملخص الطلب</h2>
                <span class="store-checkout-order__count store-checkout-success-status">
                    {{ $order->status?->name ?? 'قيد المعالجة' }}
                </span>
            </div>

            <div class="store-checkout-order__rows store-checkout-success-summary__rows">
                <div class="store-checkout-order__row">
                    <span>المجموع الفرعي</span>
                    <strong>{{ number_format((float) $order->subtotal, 2) }} {{ $currencyLabel }}</strong>
                </div>
                @if((float) $order->discount_amount > 0)
                    <div class="store-checkout-order__row store-checkout-order__row--discount">
                        <span>الخصم @if($order->coupon_code)<small>({{ $order->coupon_code }})</small>@endif</span>
                        <strong>-{{ number_format((float) $order->discount_amount, 2) }} {{ $currencyLabel }}</strong>
                    </div>
                @endif
                @if((float) ($order->points_discount_amount ?? 0) > 0)
                    <div class="store-checkout-order__row store-checkout-order__row--discount">
                        <span>نقاط الولاء</span>
                        <strong>-{{ number_format((float) $order->points_discount_amount, 2) }} {{ $currencyLabel }}</strong>
                    </div>
                @endif
                <div class="store-checkout-order__row">
                    <span>الشحن @if($order->shippingMethod)<small>({{ $order->shippingMethod->name }})</small>@endif</span>
                    <strong>
                        @if((float) $order->shipping_amount <= 0)
                            مجاني
                        @else
                            {{ number_format((float) $order->shipping_amount, 2) }} {{ $currencyLabel }}
                        @endif
                    </strong>
                </div>
                @if((float) $order->tax_amount > 0)
                    <div class="store-checkout-order__row">
                        <span>الضريبة</span>
                        <strong>{{ number_format((float) $order->tax_amount, 2) }} {{ $currencyLabel }}</strong>
                    </div>
                @endif
            </div>

            <div class="store-checkout-order__total">
                <span>الإجمالي</span>
                <strong>{{ number_format((float) $order->total, 2) }} {{ $currencyLabel }}</strong>
            </div>

            <div class="store-checkout-success-meta">
                @if($paymentMethod)
                    <div class="store-checkout-success-meta__row">
                        <span>طريقة الدفع</span>
                        <strong>{{ $paymentMethod->name }}</strong>
                    </div>
                @endif
                @if($payment)
                    <div class="store-checkout-success-meta__row">
                        <span>حالة الدفع</span>
                        <strong class="store-checkout-success-meta__status store-checkout-success-meta__status--{{ $payment->status }}">
                            @switch($payment->status)
                                @case('completed') مكتمل @break
                                @case('pending') قيد الانتظار @break
                                @case('failed') فشل @break
                                @default {{ $payment->status }}
                            @endswitch
                        </strong>
                    </div>
                @endif
            </div>

            <div class="store-checkout-success-actions">
                <a href="{{ route('frontend.checkout.invoice', $order) }}" class="store-checkout-submit store-checkout-success-actions__primary" target="_blank" rel="noopener noreferrer">
                    <span>عرض الفاتورة / الوصل</span>
                    <i class="w-icon-download"></i>
                </a>
                <a href="{{ route('frontend.shop.index') }}" class="store-checkout-success-actions__secondary">
                    <span>متابعة التسوق</span>
                    <i class="w-icon-long-arrow-left"></i>
                </a>
                <a href="{{ route('frontend.track-order') }}" class="store-checkout-success-actions__secondary">
                    <i class="w-icon-truck"></i>
                    <span>تتبع الطلب</span>
                </a>
                @auth
                    <a href="{{ route('frontend.account.orders.show', $order) }}" class="store-checkout-success-actions__secondary">
                        <i class="w-icon-orders"></i>
                        <span>عرض في حسابي</span>
                    </a>
                @endauth
            </div>

            <div class="store-checkout-order__trust store-checkout-success-trust">
                <span><i class="w-icon-check"></i> طلبك محفوظ بأمان</span>
                <span><i class="w-icon-shield"></i> دعم ما بعد البيع</span>
            </div>
        </aside>
    </div>
</div>
