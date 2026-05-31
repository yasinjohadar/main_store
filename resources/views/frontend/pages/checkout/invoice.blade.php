@extends('frontend.layouts.invoice')

@section('title', 'فاتورة الطلب ' . $order->order_number . ' - ' . ($store['name'] ?? config('app.name')))

@section('content')
    @php
        use App\Services\SiteSettingsService;

        $payment = $order->payments->first();
        $paymentMethod = $payment?->paymentMethod;
        $shippingAddress = $order->shipping_address;
        $billingAddress = $order->billing_address;
        $currencyLabel = ($order->currency ?? 'SAR') === 'SAR' ? 'ر.س' : ($order->currency ?? 'SAR');
        $customerEmail = $shippingAddress?->email ?? $order->guest_email ?? $order->user?->email;
        $socialLinks = array_filter([
            'فيسبوك' => site_setting(SiteSettingsService::KEY_SITE_FACEBOOK_URL),
            'إنستغرام' => site_setting(SiteSettingsService::KEY_SITE_INSTAGRAM_URL),
            'تويتر' => site_setting(SiteSettingsService::KEY_SITE_TWITTER_URL),
        ]);
    @endphp

    <div class="store-invoice-toolbar no-print">
        <div class="store-invoice-toolbar__inner">
            <a href="{{ route('frontend.checkout.success', $order) }}" class="store-invoice-toolbar__back">
                <i class="w-icon-long-arrow-right"></i>
                <span>العودة لتأكيد الطلب</span>
            </a>
            <button type="button" class="store-invoice-toolbar__print" onclick="window.print()">
                <i class="w-icon-download"></i>
                <span>طباعة / حفظ PDF</span>
            </button>
        </div>
    </div>

    <article class="store-invoice" id="store-order-invoice">
        <header class="store-invoice__header">
            <div class="store-invoice__brand">
                @if(! empty($store['logo']))
                    <img src="{{ $store['logo'] }}" alt="{{ $store['name'] }}" class="store-invoice__logo">
                @endif
                <div class="store-invoice__brand-text">
                    <h1 class="store-invoice__store-name">{{ $store['name'] }}</h1>
                    @if(! empty($store['address']))
                        <p class="store-invoice__store-meta">{{ $store['address'] }}</p>
                    @endif
                </div>
            </div>
            <div class="store-invoice__doc">
                <p class="store-invoice__doc-label">وصل / فاتورة طلب</p>
                <p class="store-invoice__doc-number">{{ $order->order_number }}</p>
                <p class="store-invoice__doc-date">
                    {{ $order->created_at->locale('ar')->translatedFormat('l، j F Y — h:i A') }}
                </p>
            </div>
        </header>

        <div class="store-invoice__contacts">
            @if(! empty($store['phone']))
                <span><i class="w-icon-phone"></i> {{ $store['phone'] }}</span>
            @endif
            @if(! empty($store['email']))
                <span><i class="w-icon-envelop3"></i> {{ $store['email'] }}</span>
            @endif
            @if(! empty($store['whatsapp']))
                <span><i class="w-icon-comments"></i> واتساب: {{ $store['whatsapp'] }}</span>
            @endif
            @foreach($socialLinks as $label => $url)
                <span>{{ $label }}: {{ $url }}</span>
            @endforeach
        </div>

        <div class="store-invoice__grid">
            <section class="store-invoice__panel">
                <h2 class="store-invoice__panel-title">بيانات العميل</h2>
                @if($shippingAddress)
                    <p class="store-invoice__line"><strong>الاسم:</strong> {{ $shippingAddress->full_name }}</p>
                    @if($customerEmail)
                        <p class="store-invoice__line"><strong>البريد:</strong> {{ $customerEmail }}</p>
                    @endif
                    @if($shippingAddress->phone)
                        <p class="store-invoice__line"><strong>الجوال:</strong> {{ $shippingAddress->phone }}</p>
                    @endif
                    <p class="store-invoice__line"><strong>العنوان:</strong> {{ $shippingAddress->full_address }}</p>
                @else
                    <p class="store-invoice__line text-muted">—</p>
                @endif
            </section>

            <section class="store-invoice__panel">
                <h2 class="store-invoice__panel-title">تفاصيل الطلب</h2>
                <p class="store-invoice__line"><strong>الحالة:</strong> {{ $order->status?->name ?? 'قيد المعالجة' }}</p>
                @if($order->shippingMethod)
                    <p class="store-invoice__line"><strong>الشحن:</strong> {{ $order->shippingMethod->name }}</p>
                @endif
                @if($paymentMethod)
                    <p class="store-invoice__line"><strong>الدفع:</strong> {{ $paymentMethod->name }}</p>
                @endif
                @if($payment)
                    <p class="store-invoice__line">
                        <strong>حالة الدفع:</strong>
                        @switch($payment->status)
                            @case('completed') مكتمل @break
                            @case('pending') قيد الانتظار @break
                            @case('failed') فشل @break
                            @default {{ $payment->status }}
                        @endswitch
                    </p>
                @endif
                @if($order->coupon_code)
                    <p class="store-invoice__line"><strong>كود الخصم:</strong> {{ $order->coupon_code }}</p>
                @endif
            </section>
        </div>

        <section class="store-invoice__table-wrap">
            <h2 class="store-invoice__panel-title">المنتجات</h2>
            <table class="store-invoice__table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المنتج</th>
                        <th>SKU</th>
                        <th>الكمية</th>
                        <th>سعر الوحدة</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                {{ $item->product_name }}
                                @if($item->variant_description)
                                    <small class="store-invoice__variant">{{ $item->variant_description }}</small>
                                @endif
                            </td>
                            <td>{{ $item->sku ?? '—' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format((float) $item->unit_price, 2) }} {{ $currencyLabel }}</td>
                            <td>{{ number_format((float) $item->total, 2) }} {{ $currencyLabel }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <div class="store-invoice__summary">
            <div class="store-invoice__summary-rows">
                <div class="store-invoice__summary-row">
                    <span>المجموع الفرعي</span>
                    <strong>{{ number_format((float) $order->subtotal, 2) }} {{ $currencyLabel }}</strong>
                </div>
                @if((float) $order->discount_amount > 0)
                    <div class="store-invoice__summary-row store-invoice__summary-row--discount">
                        <span>الخصم</span>
                        <strong>-{{ number_format((float) $order->discount_amount, 2) }} {{ $currencyLabel }}</strong>
                    </div>
                @endif
                @if((float) ($order->points_discount_amount ?? 0) > 0)
                    <div class="store-invoice__summary-row store-invoice__summary-row--discount">
                        <span>نقاط الولاء</span>
                        <strong>-{{ number_format((float) $order->points_discount_amount, 2) }} {{ $currencyLabel }}</strong>
                    </div>
                @endif
                <div class="store-invoice__summary-row">
                    <span>الشحن</span>
                    <strong>
                        @if((float) $order->shipping_amount <= 0)
                            مجاني
                        @else
                            {{ number_format((float) $order->shipping_amount, 2) }} {{ $currencyLabel }}
                        @endif
                    </strong>
                </div>
                @if((float) $order->tax_amount > 0)
                    <div class="store-invoice__summary-row">
                        <span>الضريبة</span>
                        <strong>{{ number_format((float) $order->tax_amount, 2) }} {{ $currencyLabel }}</strong>
                    </div>
                @endif
                <div class="store-invoice__summary-row store-invoice__summary-row--total">
                    <span>الإجمالي</span>
                    <strong>{{ number_format((float) $order->total, 2) }} {{ $currencyLabel }}</strong>
                </div>
            </div>
        </div>

        @if($order->customer_note)
            <section class="store-invoice__note">
                <h2 class="store-invoice__panel-title">ملاحظات العميل</h2>
                <p>{{ $order->customer_note }}</p>
            </section>
        @endif

        @if($billingAddress && $billingAddress->id !== $shippingAddress?->id)
            <section class="store-invoice__note">
                <h2 class="store-invoice__panel-title">عنوان الفوترة</h2>
                <p>{{ $billingAddress->full_name }} — {{ $billingAddress->full_address }}</p>
            </section>
        @endif

        <footer class="store-invoice__footer">
            <p>شكراً لتسوقكم من {{ $store['name'] }}.</p>
            <p class="store-invoice__footer-meta">هذه وثيقة إلكترونية صادرة عن النظام — {{ url('/') }}</p>
        </footer>
    </article>
@endsection
