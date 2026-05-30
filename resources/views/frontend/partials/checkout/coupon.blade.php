<div class="store-checkout-coupon">
    <button type="button" class="store-checkout-coupon__toggle" data-checkout-coupon-toggle aria-expanded="false">
        <i class="w-icon-tag"></i>
        <span>لديك كود خصم؟</span>
        <i class="w-icon-angle-down store-checkout-coupon__chevron"></i>
    </button>
    <div class="store-checkout-coupon__panel" data-checkout-coupon-panel hidden>
        <p class="store-checkout-coupon__hint">أدخل الكود وسيتم تطبيقه على إجمالي طلبك.</p>
        <form method="POST" action="{{ route('frontend.cart.apply-coupon') }}" class="store-checkout-coupon__form">
            @csrf
            <input type="text" name="coupon" class="form-control store-checkout-coupon__input"
                placeholder="كود الخصم" value="{{ old('coupon', session('coupon_code')) }}">
            <button type="submit" class="store-checkout-coupon__btn">تطبيق</button>
        </form>
    </div>
</div>
