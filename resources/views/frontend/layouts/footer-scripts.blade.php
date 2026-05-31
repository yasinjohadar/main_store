    <script src="{{ asset('frontend/assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/jquery.plugin/jquery.plugin.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/zoom/jquery.zoom.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/jquery.countdown/jquery.countdown.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/skrollr/skrollr.min.js') }}"></script>

    <!-- Swiper JS -->
    <script src="{{ asset('frontend/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>

    <!-- Main JS -->
    <script>
        window.storeNewsletterPopup = @json(app(\App\Services\SiteSettingsService::class)->newsletterPopupConfig());
    </script>
    <script src="{{ asset('frontend/assets/js/main.min.js') }}"></script>
    <script>
        window.LaravelCart = {
            cartUrl: @json(route('frontend.cart.index')),
            checkoutUrl: @json(route('frontend.checkout.index')),
            dropdownUrl: @json(route('frontend.cart.dropdown')),
            removeUrlBase: @json(rtrim(route('frontend.cart.index'), '/')),
            csrfToken: @json(csrf_token()),
        };
        window.LaravelWishlist = {
            toggleUrl: @json(route('frontend.wishlist.toggle')),
            wishlistUrl: @json(route('frontend.wishlist')),
            shopUrl: @json(route('frontend.shop.index')),
            csrfToken: @json(csrf_token()),
        };
        window.LaravelCompare = {
            toggleUrl: @json(route('frontend.compare.toggle')),
            clearUrl: @json(route('frontend.compare.clear')),
            compareUrl: @json(route('frontend.compare')),
            maxItems: {{ \App\Services\CompareService::MAX_ITEMS }},
            csrfToken: @json(csrf_token()),
            initialProducts: @json(app(\App\Services\CompareService::class)->barPayload()),
        };
    </script>
    <script src="{{ asset('frontend/assets/js/custom-cart.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/custom-wishlist.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/custom-compare.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/product-variants.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/custom-quickview.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/custom-search-category.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/custom-shop.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/custom-newsletter-popup.js') }}"></script>
    @stack('analytics-config')
    @include('frontend.partials.analytics-scripts')
@stack('scripts')
