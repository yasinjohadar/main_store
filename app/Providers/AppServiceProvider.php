<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use App\Events\WhatsAppMessageReceived;
use App\Listeners\AutoReplyWhatsAppListener;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        
        // تسجيل PermissionServiceProvider
        $this->app->register(PermissionServiceProvider::class);

        // Register WhatsApp auto-reply listener
        Event::listen(
            WhatsAppMessageReceived::class,
            AutoReplyWhatsAppListener::class
        );

        View::composer('admin.layouts.main-header', function ($view) {
            $currencies = Currency::active()->ordered()->get();
            $currencyService = app(CurrencyService::class);
            $displayCurrency = $currencyService->getDisplayCurrency();
            $view->with(compact('currencies', 'displayCurrency'));
        });

        View::composer('admin.layouts.master', function ($view) {
            $view->with('currencyService', app(CurrencyService::class));
        });

        View::composer([
            'frontend.layouts.main-header',
            'frontend.layouts.master',
            'components.frontend.header-middle',
            'components.frontend.sticky-footer',
        ], function ($view) {
            $cart = session('cart', []);
            $cartItems = [];
            $cartTotal = 0;

            if (is_array($cart)) {
                foreach ($cart as $rowId => $item) {
                    $subtotal = ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
                    $cartItems[] = array_merge($item, [
                        'row_id' => $rowId,
                        'subtotal' => $subtotal,
                    ]);
                    $cartTotal += $subtotal;
                }
            }

            $view->with([
                'cartCount' => count($cartItems),
                'cartItems' => $cartItems,
                'cartTotal' => $cartTotal,
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