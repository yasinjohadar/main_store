@php
    use Illuminate\Support\Facades\Storage;

    $user = auth()->user();
    $nameParts = $user ? explode(' ', $user->name, 2) : ['', ''];
    $firstName = old('first_name', $nameParts[0] ?? '');
    $lastName = old('last_name', $nameParts[1] ?? '');
    $totals = $totals ?? null;
    $itemCount = $totals?->itemsCount ?? count($cartItems ?? []);
    $subtotal = $totals?->subtotal ?? ($cartTotal ?? 0);
    $discountAmount = $totals?->discount ?? ($discountAmount ?? 0);
    $shippingCost = $totals?->shipping ?? 0;
    $taxAmount = $totals?->tax ?? 0;
    $orderTotal = $totals?->total ?? max(0, $subtotal + $shippingCost + $taxAmount - $discountAmount);
    $shippingMethods = $totals?->shippingMethods ?? [];
    $selectedMethodId = old('shipping_method_id', $totals?->selectedShippingMethod['id'] ?? null);
    $paymentMethods = $paymentMethods ?? collect();
    $selectedPayment = old('payment_method_id', $paymentMethods->first()?->id);
@endphp

@include('frontend.partials.checkout.coupon')

<form class="store-checkout-form" method="POST" action="{{ route('frontend.checkout.store') }}">
    @csrf
    <input type="hidden" name="shipping_country" value="{{ old('shipping_country', $shippingAddress['country'] ?? config('store.default_country', 'SA')) }}">

    @if($errors->any())
        <div class="store-cart-alert store-cart-alert--error store-checkout-form__alert">
            @foreach($errors->all() as $error)
                <p class="mb-0">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="row gutter-lg store-checkout-layout">
        <div class="col-lg-7 mb-6 store-checkout-main">
            <div class="store-checkout-main__head">
                <div>
                    <h1 class="store-checkout-main__title">إتمام الشراء</h1>
                    <p class="store-checkout-main__meta">أكمل بياناتك لإرسال طلبك بأمان</p>
                </div>
                <a href="{{ route('frontend.cart.index') }}" class="store-checkout-main__back d-none d-md-inline-flex">
                    <i class="w-icon-long-arrow-left"></i>
                    <span>العودة للسلة</span>
                </a>
            </div>

            <section class="store-checkout-card">
                <div class="store-checkout-card__head">
                    <span class="store-checkout-card__icon"><i class="w-icon-map-marker"></i></span>
                    <div>
                        <h2 class="store-checkout-card__title">بيانات الشحن</h2>
                    </div>
                </div>
                <div class="store-checkout-card__body">
                    <div class="row gutter-sm">
                        <div class="col-md-6">
                            <div class="store-checkout-field">
                                <label>الاسم الأول *</label>
                                <input type="text" class="form-control store-checkout-input" name="first_name" value="{{ $firstName }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="store-checkout-field">
                                <label>اسم العائلة *</label>
                                <input type="text" class="form-control store-checkout-input" name="last_name" value="{{ $lastName }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="store-checkout-field">
                        <label>العنوان *</label>
                        <input type="text" class="form-control store-checkout-input" name="address" value="{{ old('address') }}" required>
                    </div>
                    <div class="row gutter-sm">
                        <div class="col-md-6">
                            <div class="store-checkout-field">
                                <label>المدينة *</label>
                                <input type="text" class="form-control store-checkout-input" name="city" value="{{ old('city', $shippingAddress['city'] ?? '') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="store-checkout-field">
                                <label>الرمز البريدي</label>
                                <input type="text" class="form-control store-checkout-input" name="zip_code" value="{{ old('zip_code') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row gutter-sm">
                        <div class="col-md-6">
                            <div class="store-checkout-field">
                                <label>رقم الجوال *</label>
                                <input type="text" class="form-control store-checkout-input" name="phone" value="{{ old('phone') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="store-checkout-field">
                                <label>البريد الإلكتروني *</label>
                                <input type="email" class="form-control store-checkout-input" name="email" value="{{ old('email', $user->email ?? '') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="store-checkout-field">
                        <label>ملاحظات الطلب</label>
                        <textarea class="form-control store-checkout-input" name="notes" rows="2">{{ old('notes') }}</textarea>
                    </div>

                    @if($loyaltyEnabled ?? false)
                    <div class="store-checkout-field mt-3">
                        <label>استبدال نقاط الولاء (رصيدك: {{ $loyaltyBalance ?? 0 }})</label>
                        <input type="number" class="form-control store-checkout-input" name="points_to_redeem"
                            min="0" max="{{ $loyaltyBalance ?? 0 }}" value="{{ old('points_to_redeem', 0) }}">
                        <small class="text-muted">اترك 0 إذا لا ترغب في الاستبدال</small>
                    </div>
                    @endif
                </div>
            </section>

            @if(count($shippingMethods) > 0)
            <section class="store-checkout-card mt-4">
                <div class="store-checkout-card__head">
                    <h2 class="store-checkout-card__title h6 mb-0">طريقة الشحن</h2>
                </div>
                <div class="store-checkout-card__body">
                    @foreach($shippingMethods as $method)
                    <label class="d-flex align-items-center gap-2 mb-2">
                        <input type="radio" name="shipping_method_id" value="{{ $method['id'] }}"
                            @checked((int) $selectedMethodId === (int) $method['id'])>
                        <span>{{ $method['name'] }} — {{ $method['cost'] == 0 ? 'مجاني' : number_format($method['cost'], 2) . ' ر.س' }}</span>
                    </label>
                    @endforeach
                </div>
            </section>
            @endif
        </div>

        <div class="col-lg-5 mb-6 sticky-sidebar-wrapper store-checkout-sidebar">
            <div class="sticky-sidebar">
                <aside class="store-checkout-order">
                    <div class="store-checkout-order__head">
                        <h2 class="store-checkout-order__title">ملخص الطلب</h2>
                        <span class="store-checkout-order__count">{{ $itemCount }} منتجات</span>
                    </div>

                    <ul class="store-checkout-order__items">
                        @foreach($cartItems as $item)
                            @php
                                $imageUrl = $item['image_url'] ?? asset('frontend/assets/images/cart/product-1.jpg');
                            @endphp
                            <li class="store-checkout-order__item">
                                <div class="store-checkout-order__item-product">
                                    <span class="store-checkout-order__item-thumb">
                                        <img src="{{ $imageUrl }}" alt="{{ $item['name'] }}" width="56" height="56" loading="lazy">
                                    </span>
                                    <span class="store-checkout-order__item-name">{{ $item['name'] }} <small>× {{ $item['quantity'] }}</small></span>
                                </div>
                                <strong>{{ number_format($item['subtotal'], 2) }} ر.س</strong>
                            </li>
                        @endforeach
                    </ul>

                    <div class="store-checkout-order__rows">
                        <div class="store-checkout-order__row"><span>المجموع الفرعي</span><strong>{{ number_format($subtotal, 2) }} ر.س</strong></div>
                        @if($discountAmount > 0)
                        <div class="store-checkout-order__row store-checkout-order__row--discount"><span>الخصم</span><strong>-{{ number_format($discountAmount, 2) }} ر.س</strong></div>
                        @endif
                        <div class="store-checkout-order__row"><span>الشحن</span><strong>{{ $shippingCost == 0 ? 'مجاني' : number_format($shippingCost, 2) . ' ر.س' }}</strong></div>
                        @if($taxAmount > 0)
                        <div class="store-checkout-order__row"><span>الضريبة</span><strong>{{ number_format($taxAmount, 2) }} ر.س</strong></div>
                        @endif
                    </div>

                    <div class="store-checkout-order__total">
                        <span>الإجمالي</span>
                        <strong>{{ number_format($orderTotal, 2) }} ر.س</strong>
                    </div>

                    <div class="store-checkout-payments">
                        <h3 class="store-checkout-payments__title">طرق الدفع</h3>
                        @forelse($paymentMethods as $method)
                        @php $isStripe = in_array($method->driver, ['stripe', 'card'], true) && ($method->driver === 'stripe' || ($method->config['gateway'] ?? '') === 'stripe'); @endphp
                        <label class="store-checkout-payment {{ (int) $selectedPayment === $method->id ? 'is-selected' : '' }}">
                            <input type="radio" name="payment_method_id" value="{{ $method->id }}" @checked((int) $selectedPayment === $method->id)>
                            <span class="store-checkout-payment__box">
                                <span class="store-checkout-payment__text">
                                    <strong>{{ $method->name }}</strong>
                                    @if($isStripe)
                                        <small class="d-block text-muted mt-1">دفع آمن عبر Stripe — فيزا، ماستركارد، مدى</small>
                                    @endif
                                </span>
                                @if($isStripe)
                                    <span class="store-checkout-payment__stripe" aria-hidden="true">Stripe</span>
                                @endif
                            </span>
                        </label>
                        @empty
                        <p class="text-muted small">لا توجد طرق دفع مفعّلة. راجع إعدادات المتجر.</p>
                        @endforelse
                    </div>

                    <button type="submit" class="store-checkout-submit" data-checkout-submit>
                        <span>تأكيد الطلب</span>
                        <i class="w-icon-long-arrow-left"></i>
                    </button>
                </aside>
            </div>
        </div>
    </div>
</form>
