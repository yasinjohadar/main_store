<div class="store-track-order-form">
    <div class="store-track-order-form__icon">
        <i class="w-icon-map-marker"></i>
    </div>
    <div class="store-track-order-form__body">
        <h2 class="store-track-order-form__title">ابحث عن طلبك</h2>
        <p class="store-track-order-form__hint">أدخل رقم الطلب والبريد الإلكتروني المستخدم عند الشراء.</p>

        @if($errors->has('track'))
            <div class="store-cart-alert store-cart-alert--error store-track-order-form__alert">
                {{ $errors->first('track') }}
            </div>
        @endif

        <form method="POST" action="{{ route('frontend.track-order') }}" class="store-track-order-form__fields">
            @csrf
            <div class="store-track-order-field">
                <label for="track-order-number">رقم الطلب</label>
                <input type="text" id="track-order-number" name="order_number"
                    class="form-control store-track-order-input"
                    placeholder="مثال: ORD-20260529-0001"
                    value="{{ old('order_number', request('order_number')) }}" required>
            </div>
            <div class="store-track-order-field">
                <label for="track-order-email">البريد الإلكتروني</label>
                <input type="email" id="track-order-email" name="email"
                    class="form-control store-track-order-input"
                    placeholder="admin@gmail.com"
                    value="{{ old('email', request('email', auth()->user()?->email)) }}" required>
            </div>
            <button type="submit" class="store-track-order-form__submit">
                <i class="w-icon-search"></i>
                <span>تتبع الطلب</span>
            </button>
        </form>
    </div>
</div>
