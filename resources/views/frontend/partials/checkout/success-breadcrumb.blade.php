<nav class="store-cart-breadcrumb store-order-steps" aria-label="خطوات الطلب">
    <div class="container">
        <ol class="store-cart-breadcrumb__steps">
            <li class="store-cart-breadcrumb__step is-done">
                <a href="{{ route('frontend.cart.index') }}" class="store-order-steps__link">
                    <span class="store-cart-breadcrumb__num">1</span>
                    <span class="store-cart-breadcrumb__label">سلة التسوق</span>
                </a>
            </li>
            <li class="store-cart-breadcrumb__step is-done">
                <a href="{{ route('frontend.checkout.index') }}" class="store-order-steps__link">
                    <span class="store-cart-breadcrumb__num">2</span>
                    <span class="store-cart-breadcrumb__label">إتمام الشراء</span>
                </a>
            </li>
            <li class="store-cart-breadcrumb__step is-active is-done" aria-current="step">
                <span class="store-cart-breadcrumb__num"><i class="w-icon-check"></i></span>
                <span class="store-cart-breadcrumb__label">اكتمال الطلب</span>
            </li>
        </ol>
    </div>
</nav>
