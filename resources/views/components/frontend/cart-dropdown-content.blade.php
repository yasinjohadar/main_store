@php
    use Illuminate\Support\Facades\Storage;

    $items = $cartItems ?? [];
    $total = $cartTotal ?? 0;
    $count = count($items);
@endphp

<div class="store-cart-dropdown" data-cart-dropdown>
    <div class="store-cart-dropdown__items">
        @forelse($items as $item)
            @php
                $imageUrl = $item['image_url']
                    ?? (! empty($item['image']) ? Storage::url($item['image']) : null)
                    ?? asset('frontend/assets/images/cart/product-1.jpg');
            @endphp
            <article class="store-cart-dropdown__item" data-cart-row="{{ $item['row_id'] }}">
                <a href="{{ route('frontend.product.show', $item['slug']) }}" class="store-cart-dropdown__thumb">
                    <img src="{{ $imageUrl }}" alt="{{ $item['name'] }}" width="96" height="96" loading="lazy">
                </a>
                <div class="store-cart-dropdown__body">
                    <a href="{{ route('frontend.product.show', $item['slug']) }}" class="store-cart-dropdown__name">
                        {{ $item['name'] }}
                    </a>
                    <div class="store-cart-dropdown__meta">
                        <span class="store-cart-dropdown__qty">{{ $item['quantity'] }} ×</span>
                        <span class="store-cart-dropdown__price">{{ number_format($item['price'], 2) }} ر.س</span>
                    </div>
                    <div class="store-cart-dropdown__line-total">
                        <span class="store-cart-dropdown__line-total-label">الإجمالي</span>
                        <span class="store-cart-dropdown__subtotal">{{ number_format($item['subtotal'], 2) }} ر.س</span>
                    </div>
                </div>
                <button type="button" class="store-cart-dropdown__remove" data-cart-remove="{{ $item['row_id'] }}"
                    aria-label="حذف {{ $item['name'] }}">
                    <i class="fas fa-times"></i>
                </button>
            </article>
        @empty
            <div class="store-cart-dropdown__empty">
                <span class="store-cart-dropdown__empty-icon"><i class="w-icon-cart"></i></span>
                <p class="store-cart-dropdown__empty-title">سلتك فارغة</p>
                <p class="store-cart-dropdown__empty-desc">لم تضف أي منتجات بعد</p>
                <a href="{{ route('frontend.shop.index') }}" class="store-cart-dropdown__empty-btn">
                    تصفح المتجر
                </a>
            </div>
        @endforelse
    </div>

    @if($count > 0)
        <div class="store-cart-dropdown__footer">
            <div class="store-cart-dropdown__total">
                <span class="store-cart-dropdown__total-label">المجموع</span>
                <strong class="store-cart-dropdown__total-value" data-cart-total>{{ number_format($total, 2) }} ر.س</strong>
            </div>
            <div class="store-cart-dropdown__actions">
                <a href="{{ route('frontend.cart.index') }}" class="store-cart-dropdown__btn store-cart-dropdown__btn--outline">
                    عرض السلة
                </a>
                <a href="{{ route('frontend.checkout.index') }}" class="store-cart-dropdown__btn store-cart-dropdown__btn--primary">
                    إتمام الشراء
                </a>
            </div>
        </div>
    @endif
</div>
