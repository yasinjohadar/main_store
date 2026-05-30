    <div class="sticky-footer sticky-content fix-bottom">
        <a href="{{ route('frontend.home') }}" class="sticky-link active">
            <i class="w-icon-home"></i>
            <p>الرئيسية</p>
        </a>
        <a href="{{ route('frontend.shop.index') }}" class="sticky-link">
            <i class="w-icon-category"></i>
            <p>المتجر</p>
        </a>
        <a href="{{ route('frontend.account') }}" class="sticky-link">
            <i class="w-icon-account"></i>
            <p>حسابي</p>
        </a>
        <div class="cart-dropdown dir-up">
            <a href="{{ route('frontend.cart.index') }}" class="sticky-link">
                <i class="w-icon-cart"></i>
                <p>السلة</p>
            </a>
            <div class="dropdown-box store-cart-dropdown-panel store-cart-dropdown-panel--sticky">
                <div data-cart-dropdown-root>
                    @include('components.frontend.cart-dropdown-content')
                </div>
            </div>
            <!-- End of Dropdown Box -->
        </div>

        <div class="header-search hs-toggle dir-up">
            <a href="#" class="search-toggle sticky-link">
                <i class="w-icon-search"></i>
                <p>بحث</p>
            </a>
            <form action="{{ route('frontend.shop.index') }}" method="get" class="input-wrapper">
                <input type="text" class="form-control" name="search" autocomplete="off" placeholder="ابحث..."
                    required />
                <button class="btn btn-search" type="submit">
                    <i class="w-icon-search"></i>
                </button>
            </form>
        </div>
    </div>
    <!-- End of Sticky Footer -->