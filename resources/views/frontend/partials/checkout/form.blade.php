@php
    use Illuminate\Support\Facades\Storage;

    $user = auth()->user();
    $nameParts = $user ? explode(' ', $user->name, 2) : ['', ''];
    $firstName = old('first_name', $nameParts[0] ?? '');
    $lastName = old('last_name', $nameParts[1] ?? '');
    $shippingCost = $cartTotal >= 200 ? 0 : 50;
    $discountAmount = session('discount', 0);
    $orderTotal = max(0, $cartTotal + $shippingCost - $discountAmount);
    $itemCount = count($cartItems);
    $paymentMethod = old('payment_method', 'bank_transfer');
@endphp

<form class="store-checkout-form" method="POST" action="{{ route('frontend.checkout.store') }}">
    @csrf

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

            @include('frontend.partials.checkout.coupon')

            <section class="store-checkout-card">
                <div class="store-checkout-card__head">
                    <span class="store-checkout-card__icon"><i class="w-icon-map-marker"></i></span>
                    <div>
                        <h2 class="store-checkout-card__title">بيانات الشحن</h2>
                        <p class="store-checkout-card__subtitle">أدخل عنوان التوصيل بدقة</p>
                    </div>
                </div>

                <div class="store-checkout-card__body">
                    <div class="row gutter-sm">
                        <div class="col-md-6">
                            <div class="store-checkout-field">
                                <label for="checkout-first-name">الاسم الأول *</label>
                                <input type="text" id="checkout-first-name" class="form-control store-checkout-input"
                                    name="first_name" value="{{ $firstName }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="store-checkout-field">
                                <label for="checkout-last-name">اسم العائلة *</label>
                                <input type="text" id="checkout-last-name" class="form-control store-checkout-input"
                                    name="last_name" value="{{ $lastName }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="store-checkout-field">
                        <label for="checkout-address">العنوان *</label>
                        <input type="text" id="checkout-address" class="form-control store-checkout-input"
                            name="address" placeholder="رقم المنزل واسم الشارع"
                            value="{{ old('address') }}" required>
                    </div>

                    <div class="row gutter-sm">
                        <div class="col-md-6">
                            <div class="store-checkout-field">
                                <label for="checkout-city">المدينة *</label>
                                <input type="text" id="checkout-city" class="form-control store-checkout-input"
                                    name="city" value="{{ old('city') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="store-checkout-field">
                                <label for="checkout-zip">الرمز البريدي</label>
                                <input type="text" id="checkout-zip" class="form-control store-checkout-input"
                                    name="zip_code" value="{{ old('zip_code') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row gutter-sm">
                        <div class="col-md-6">
                            <div class="store-checkout-field">
                                <label for="checkout-phone">رقم الجوال *</label>
                                <input type="text" id="checkout-phone" class="form-control store-checkout-input"
                                    name="phone" value="{{ old('phone') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="store-checkout-field">
                                <label for="checkout-email">البريد الإلكتروني *</label>
                                <input type="email" id="checkout-email" class="form-control store-checkout-input"
                                    name="email" value="{{ old('email', $user->email ?? '') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="store-checkout-field">
                        <label for="order-notes">ملاحظات الطلب (اختياري)</label>
                        <textarea class="form-control store-checkout-input store-checkout-textarea" id="order-notes"
                            name="notes" rows="3" placeholder="ملاحظات خاصة بالتوصيل أو الطلب">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-lg-5 mb-6 sticky-sidebar-wrapper store-checkout-sidebar">
            <div class="sticky-sidebar">
                <aside class="store-checkout-order">
                    <div class="store-checkout-order__head">
                        <h2 class="store-checkout-order__title">ملخص الطلب</h2>
                        <span class="store-checkout-order__count">{{ $itemCount }} {{ $itemCount === 1 ? 'منتج' : 'منتجات' }}</span>
                    </div>

                    <ul class="store-checkout-order__items">
                        @foreach($cartItems as $item)
                            @php
                                $imageUrl = $item['image_url']
                                    ?? (! empty($item['image']) ? Storage::url($item['image']) : null)
                                    ?? ('https://picsum.photos/seed/checkout' . $item['product_id'] . '/120/120');
                            @endphp
                            <li class="store-checkout-order__item">
                                <div class="store-checkout-order__item-product">
                                    <span class="store-checkout-order__item-thumb">
                                        <img src="{{ $imageUrl }}" alt="{{ $item['name'] }}" width="56" height="56" loading="lazy">
                                    </span>
                                    <span class="store-checkout-order__item-name">
                                        {{ $item['name'] }}
                                        <small>× {{ $item['quantity'] }}</small>
                                    </span>
                                </div>
                                <strong>{{ number_format($item['subtotal'], 2) }} ر.س</strong>
                            </li>
                        @endforeach
                    </ul>

                    <div class="store-checkout-order__rows">
                        <div class="store-checkout-order__row">
                            <span>المجموع الفرعي</span>
                            <strong>{{ number_format($cartTotal, 2) }} ر.س</strong>
                        </div>
                        @if($discountAmount > 0)
                            <div class="store-checkout-order__row store-checkout-order__row--discount">
                                <span>الخصم</span>
                                <strong>-{{ number_format($discountAmount, 2) }} ر.س</strong>
                            </div>
                        @endif
                        <div class="store-checkout-order__row">
                            <span>الشحن</span>
                            <strong>{{ $shippingCost === 0 ? 'مجاني' : number_format($shippingCost, 2) . ' ر.س' }}</strong>
                        </div>
                    </div>

                    <div class="store-checkout-order__total">
                        <span>الإجمالي</span>
                        <strong>{{ number_format($orderTotal, 2) }} ر.س</strong>
                    </div>

                    <div class="store-checkout-payments">
                        <h3 class="store-checkout-payments__title">طرق الدفع</h3>

                        <label class="store-checkout-payment {{ $paymentMethod === 'bank_transfer' ? 'is-selected' : '' }}">
                            <input type="radio" name="payment_method" value="bank_transfer"
                                @checked($paymentMethod === 'bank_transfer')>
                            <span class="store-checkout-payment__box">
                                <span class="store-checkout-payment__icon"><i class="w-icon-money"></i></span>
                                <span class="store-checkout-payment__text">
                                    <strong>تحويل بنكي</strong>
                                    <small>حوّل المبلغ واستخدم رقم الطلب كمرجع</small>
                                </span>
                                <span class="store-checkout-payment__check" aria-hidden="true"><i class="w-icon-check"></i></span>
                            </span>
                        </label>

                        <label class="store-checkout-payment {{ $paymentMethod === 'cod' ? 'is-selected' : '' }}">
                            <input type="radio" name="payment_method" value="cod"
                                @checked($paymentMethod === 'cod')>
                            <span class="store-checkout-payment__box">
                                <span class="store-checkout-payment__icon"><i class="w-icon-truck"></i></span>
                                <span class="store-checkout-payment__text">
                                    <strong>الدفع عند الاستلام</strong>
                                    <small>ادفع نقداً عند استلام طلبك</small>
                                </span>
                                <span class="store-checkout-payment__check" aria-hidden="true"><i class="w-icon-check"></i></span>
                            </span>
                        </label>

                        <label class="store-checkout-payment {{ $paymentMethod === 'credit_card' ? 'is-selected' : '' }}">
                            <input type="radio" name="payment_method" value="credit_card"
                                @checked($paymentMethod === 'credit_card')>
                            <span class="store-checkout-payment__box">
                                <span class="store-checkout-payment__icon"><i class="fas fa-credit-card"></i></span>
                                <span class="store-checkout-payment__text">
                                    <strong>بطاقة ائتمان</strong>
                                    <small>الدفع الإلكتروني قريباً</small>
                                </span>
                                <span class="store-checkout-payment__check" aria-hidden="true"><i class="w-icon-check"></i></span>
                            </span>
                        </label>
                    </div>

                    <button type="submit" class="store-checkout-submit">
                        <span>تأكيد الطلب</span>
                        <i class="w-icon-long-arrow-left"></i>
                    </button>

                    <div class="store-checkout-order__trust">
                        <span><i class="w-icon-check"></i> دفع آمن</span>
                        <span><i class="w-icon-check"></i> توصيل موثوق</span>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</form>
