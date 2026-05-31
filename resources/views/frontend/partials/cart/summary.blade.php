@php
    $totals = $totals ?? null;
    $subtotal = $totals?->subtotal ?? ($cartTotal ?? 0);
    $discountAmount = $totals?->discount ?? ($discountAmount ?? 0);
    $shippingCost = $totals?->shipping ?? 0;
    $taxAmount = $totals?->tax ?? 0;
    $orderTotal = $totals?->total ?? max(0, $subtotal - $discountAmount);
    $itemCount = $totals?->itemsCount ?? count($cartItems ?? []);
    $guestCheckout = config('store.guest_checkout_enabled', false);
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
                    <strong>{{ number_format($subtotal, 2) }} ر.س</strong>
                </div>
                @if($discountAmount > 0)
                    <div class="store-cart-summary__row store-cart-summary__row--discount">
                        <span>الخصم</span>
                        <strong>-{{ number_format($discountAmount, 2) }} ر.س</strong>
                    </div>
                @endif
                @if($taxAmount > 0)
                    <div class="store-cart-summary__row">
                        <span>الضريبة (تقديري)</span>
                        <strong>{{ number_format($taxAmount, 2) }} ر.س</strong>
                    </div>
                @endif
            </div>

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
                @elseif($guestCheckout)
                    <a href="{{ route('frontend.checkout.index') }}" class="store-cart-summary__checkout">
                        <span>إتمام الشراء كضيف</span>
                        <i class="w-icon-long-arrow-left"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="store-cart-summary__checkout">
                        <span>سجّل الدخول للشراء</span>
                        <i class="w-icon-long-arrow-left"></i>
                    </a>
                @endauth
            @endif
        </aside>
    </div>
</div>
