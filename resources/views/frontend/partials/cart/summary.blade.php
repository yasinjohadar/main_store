@php
    $shippingCost = $cartTotal >= 200 ? 0 : ($cartTotal > 0 ? 50 : 0);
    $discountAmount = session('discount', 0);
    $orderTotal = max(0, $cartTotal + $shippingCost - $discountAmount);
    $itemCount = count($cartItems);
@endphp

<div class="col-lg-4 sticky-sidebar-wrapper store-cart-sidebar">
    <div class="sticky-sidebar">
        <aside class="store-cart-summary">
            <div class="store-cart-summary__head">
                <h2 class="store-cart-summary__title">ملخص الطلب</h2>
                @if($itemCount > 0)
                    <span class="store-cart-summary__count">{{ $itemCount }} {{ $itemCount === 1 ? 'منتج' : 'منتجات' }}</span>
                @endif
            </div>

            <div class="store-cart-summary__rows">
                <div class="store-cart-summary__row">
                    <span>المجموع الفرعي</span>
                    <strong>{{ number_format($cartTotal, 2) }} ر.س</strong>
                </div>

                @if($discountAmount > 0)
                    <div class="store-cart-summary__row store-cart-summary__row--discount">
                        <span>الخصم</span>
                        <strong>-{{ number_format($discountAmount, 2) }} ر.س</strong>
                    </div>
                @endif

                <div class="store-cart-summary__row">
                    <span>الشحن</span>
                    <strong>
                        @if($shippingCost === 0 && $cartTotal > 0)
                            مجاني
                        @elseif($cartTotal > 0)
                            {{ number_format($shippingCost, 2) }} ر.س
                        @else
                            —
                        @endif
                    </strong>
                </div>
            </div>

            @if($cartTotal > 0 && $cartTotal < 200)
                <div class="store-cart-summary__shipping-tip">
                    <i class="w-icon-truck"></i>
                    <p>أضف منتجات بقيمة <strong>{{ number_format(200 - $cartTotal, 2) }} ر.س</strong> للحصول على شحن مجاني.</p>
                </div>
            @endif

            <div class="store-cart-summary__total">
                <span>الإجمالي</span>
                <strong>{{ number_format($orderTotal, 2) }} ر.س</strong>
            </div>

            @if($itemCount > 0)
                @auth
                    <a href="{{ route('frontend.checkout.index') }}" class="store-cart-summary__checkout">
                        <span>إتمام الشراء</span>
                        <i class="w-icon-long-arrow-left"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="store-cart-summary__checkout">
                        <span>سجّل الدخول للشراء</span>
                        <i class="w-icon-long-arrow-left"></i>
                    </a>
                    <p class="store-cart-summary__login-note">يجب تسجيل الدخول لإتمام الطلب</p>
                @endauth

                <div class="store-cart-summary__trust">
                    <span><i class="w-icon-check"></i> دفع آمن</span>
                    <span><i class="w-icon-check"></i> شحن سريع</span>
                </div>
            @endif
        </aside>
    </div>
</div>
