<nav class="store-cart-breadcrumb store-order-steps" aria-label="خطوات الطلب">
    <div class="container">
        <ol class="store-cart-breadcrumb__steps">
            <li class="store-cart-breadcrumb__step is-done">
                <a href="{{ route('frontend.cart.index') }}" class="store-order-steps__link">
                    <span class="store-cart-breadcrumb__num">1</span>
                    <span class="store-cart-breadcrumb__label">سلة التسوق</span>
                </a>
            </li>
            <li class="store-cart-breadcrumb__step is-active" aria-current="step">
                <span class="store-cart-breadcrumb__num">2</span>
                <span class="store-cart-breadcrumb__label">إتمام الشراء</span>
            </li>
            <li class="store-cart-breadcrumb__step">
                <span class="store-cart-breadcrumb__num">3</span>
                <span class="store-cart-breadcrumb__label">اكتمال الطلب</span>
            </li>
        </ol>
    </div>
</nav>
