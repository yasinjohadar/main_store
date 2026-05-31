@php
    use Illuminate\Support\Facades\Storage;
    $itemCount = count($cartItems);
@endphp

<div class="col-lg-8 mb-6 store-cart-main">
    @if(session('success'))
        <div class="store-cart-alert store-cart-alert--success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="store-cart-alert store-cart-alert--error">
            @foreach($errors->all() as $error)
                <p class="mb-0">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if($itemCount > 0)
        <div class="store-cart-main__head">
            <div>
                <h1 class="store-cart-main__title">سلة التسوق</h1>
                <p class="store-cart-main__meta">{{ $itemCount }} {{ $itemCount === 1 ? 'منتج' : 'منتجات' }}</p>
            </div>
            <a href="{{ route('frontend.shop.index') }}" class="store-cart-main__continue d-none d-md-inline-flex">
                <i class="w-icon-long-arrow-left"></i>
                <span>متابعة التسوق</span>
            </a>
        </div>

        <div class="store-cart-list">
            <div class="store-cart-list__header d-none d-md-grid">
                <span>المنتج</span>
                <span>السعر</span>
                <span>الكمية</span>
                <span>المجموع</span>
            </div>

            @foreach($cartItems as $item)
                @php
                    $imageUrl = $item['image_url']
                        ?? (! empty($item['image']) ? Storage::url($item['image']) : null)
                        ?? ('https://picsum.photos/seed/cart' . $item['product_id'] . '/300/338');
                @endphp
                <article class="store-cart-item">
                    <div class="store-cart-item__product">
                        <a href="{{ route('frontend.product.show', $item['slug']) }}" class="store-cart-item__thumb">
                            <img src="{{ $imageUrl }}" alt="{{ $item['name'] }}" width="100" height="100" loading="lazy">
                        </a>
                        <div class="store-cart-item__info">
                            <a href="{{ route('frontend.product.show', $item['slug']) }}" class="store-cart-item__name">
                                {{ $item['name'] }}
                            </a>
                            <span class="store-cart-item__price-mobile d-md-none">
                                {{ number_format($item['price'], 2) }} ر.س
                            </span>
                        </div>
                    </div>

                    <div class="store-cart-item__price d-none d-md-flex">
                        <span>{{ number_format($item['price'], 2) }} ر.س</span>
                    </div>

                    <div class="store-cart-item__qty">
                        <form method="POST" action="{{ route('frontend.cart.update', $item['row_id']) }}">
                            @csrf
                            @method('PATCH')
                            <div class="input-group qty-input-group store-cart-item__qty-group">
                                <button type="button" class="quantity-minus w-icon-minus" aria-label="تقليل"></button>
                                <input class="quantity form-control" type="number" name="quantity"
                                    min="1" max="100000" value="{{ $item['quantity'] }}"
                                    onchange="this.form.submit()">
                                <button type="button" class="quantity-plus w-icon-plus" aria-label="زيادة"></button>
                            </div>
                        </form>
                    </div>

                    <div class="store-cart-item__total">
                        <span class="store-cart-item__total-label d-md-none">المجموع</span>
                        <strong>{{ number_format($item['subtotal'], 2) }} ر.س</strong>
                    </div>

                    <form method="POST" action="{{ route('frontend.cart.destroy', $item['row_id']) }}" class="store-cart-item__remove">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="store-cart-item__remove-btn" aria-label="حذف {{ $item['name'] }}">
                            <i class="fas fa-times"></i>
                        </button>
                    </form>
                </article>
            @endforeach
        </div>

        <a href="{{ route('frontend.shop.index') }}" class="store-cart-main__continue-mobile d-md-none">
            <i class="w-icon-long-arrow-left"></i>
            <span>متابعة التسوق</span>
        </a>

        <div class="store-cart-coupon">
            <div class="store-cart-coupon__icon"><i class="w-icon-tag"></i></div>
            <div class="store-cart-coupon__body">
                <h3 class="store-cart-coupon__title">كود الخصم</h3>
                <p class="store-cart-coupon__desc">أدخل الكود للحصول على خصم إضافي</p>
                <form method="POST" action="{{ route('frontend.cart.apply-coupon') }}" class="store-cart-coupon__form">
                    @csrf
                    <input type="text" name="coupon" class="form-control store-cart-coupon__input"
                        placeholder="أدخل كود الخصم..." value="{{ old('coupon', $couponCode ?? session('coupon_code')) }}">
                    <button type="submit" class="store-cart-coupon__btn">تطبيق</button>
                </form>
                @if($cart->coupon_code ?? session('coupon_code'))
                    <p class="store-cart-coupon__note text-success">الكوبون <strong>{{ $cart->coupon_code ?? session('coupon_code') }}</strong> مفعّل</p>
                    <form method="POST" action="{{ route('frontend.cart.remove-coupon') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">إزالة الكوبون</button>
                    </form>
                @endif
            </div>
        </div>
    @else
        <div class="store-cart-empty">
            <div class="store-cart-empty__icon"><i class="w-icon-cart"></i></div>
            <h2 class="store-cart-empty__title">سلتك فارغة</h2>
            <p class="store-cart-empty__desc">لم تضف أي منتجات بعد. تصفّح المتجر واختر ما يناسبك.</p>
            <a href="{{ route('frontend.shop.index') }}" class="store-cart-empty__btn">
                <span>تصفح المتجر</span>
                <i class="w-icon-long-arrow-left"></i>
            </a>
        </div>
    @endif
</div>
