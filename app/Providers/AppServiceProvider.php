<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;
use App\Models\Currency;
use App\Services\CurrencyService;
use App\Services\CompareService;
use App\Services\WishlistService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once app_path('Helpers/ShopHelper.php');
        require_once app_path('Helpers/SocialAuthHelper.php');
        require_once app_path('Helpers/AuthRedirectHelper.php');
        require_once app_path('Helpers/StoreSettingsHelper.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        $this->app->booted(function () {
            app(\App\Services\DynamicConfigLoader::class)->load();
        });
        
        // تسجيل PermissionServiceProvider
        $this->app->register(PermissionServiceProvider::class);

        // NOTE: order/webhook/registration listeners (AutoReplyWhatsAppListener,
        // SendOrderConfirmationEmail, SendAdminNewOrderEmail, HandleOrderStatusChanged,
        // DispatchOrderWebhooks, CreateReferralCoupon) are picked up automatically by
        // Laravel's event auto-discovery (their handle()/named methods type-hint the
        // event). Do NOT also register them here with Event::listen() — `php artisan
        // event:list` confirmed that double-registering fired every order email,
        // webhook, and referral coupon twice per event.

        \App\Models\Product::observe(\App\Observers\ProductObserver::class);

        View::composer('admin.layouts.main-header', function ($view) {
            $currencies = Currency::active()->ordered()->get();
            $currencyService = app(CurrencyService::class);
            $displayCurrency = $currencyService->getDisplayCurrency();
            $view->with(compact('currencies', 'displayCurrency'));
        });

        View::composer('admin.*', function ($view) {
            if (! $view->offsetExists('currencyService')) {
                $view->with('currencyService', app(CurrencyService::class));
            }
        });

        View::composer('frontend.layouts.master', function ($view) {
            if (! $view->offsetExists('seo')) {
                $view->with('seo', app(\App\Services\Seo\SeoResolverService::class)->forSite());
            }
        });

        View::composer([
            'frontend.layouts.main-header',
            'frontend.layouts.master',
            'components.frontend.header-middle',
            'components.frontend.sticky-footer',
        ], function ($view) {
            $cartService = app(\App\Services\CartService::class);
            $presenter = app(\App\Services\CartPresenter::class);
            $cart = $cartService->getCart();
            $viewData = $presenter->toViewData($cart);

            $view->with([
                'cartCount' => $viewData['cartCount'],
                'cartItems' => $viewData['cartItems'],
                'cartTotal' => $viewData['cartTotal'],
                'wishlistIds' => app(WishlistService::class)->ids(),
                'wishlistCount' => app(WishlistService::class)->count(),
                'compareIds' => app(CompareService::class)->ids(),
                'compareCount' => app(CompareService::class)->count(),
                'compareProducts' => app(CompareService::class)->products(),
            ]);
        });

        View::composer([
            'components.frontend.header-bottom',
            'components.frontend.header-middle',
            'components.frontend.header-top',
            'components.frontend.mobile-menu',
            'frontend.layouts.footer',
        ], function ($view) {
            $navCategories = Category::active()
                ->root()
                ->ordered()
                ->limit(7)
                ->get();

            $view->with('navCategories', $navCategories);
        });

        View::composer('components.frontend.header-top', function ($view) {
            $currencyService = app(CurrencyService::class);
            $view->with([
                'currencies' => Currency::active()->ordered()->get(),
                'displayCurrency' => $currencyService->getFrontendDisplayCurrency(),
            ]);
        });

        View::composer([
            'components.frontend.product-card',
            'components.frontend.product-related-slide',
            'components.frontend.wishlist-button',
            'components.frontend.compare-button',
            'frontend.partials.product.info',
            'frontend.partials.quick-view-content',
            'frontend.partials.compare.content',
        ], function ($view) {
            $view->with([
                'wishlistIds' => app(WishlistService::class)->ids(),
                'compareIds' => app(CompareService::class)->ids(),
            ]);
        });
    }
}