<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ShopCatalogController;
use App\Http\Controllers\Frontend\ShopController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\CategoryController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\AccountController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\Frontend\CompareController;
use App\Http\Controllers\Frontend\CurrencyController;
use App\Http\Controllers\Frontend\TrackOrderController;

Route::prefix('')->name('frontend.')->group(function () {
    // Home
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Shop
    Route::get('shop', [ShopController::class, 'index'])->name('shop.index');
    Route::get('shop/products', [ShopCatalogController::class, 'products'])->name('shop.products');

    // Categories
    Route::get('category/{slug}', [CategoryController::class, 'show'])->name('category.show');

    // Products
    Route::get('product/{slug}', [ProductController::class, 'show'])->name('product.show');
    Route::get('product/{slug}/quick-view', [ProductController::class, 'quickView'])->name('product.quick-view');

    Route::get('currency/{code}', [CurrencyController::class, 'switch'])->name('currency.switch');

    // Cart
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::get('dropdown', [CartController::class, 'dropdown'])->name('dropdown');
        Route::post('/', [CartController::class, 'store'])->name('store');
        Route::patch('/{id}', [CartController::class, 'update'])->name('update');
        Route::delete('/{id}', [CartController::class, 'destroy'])->name('destroy');
        Route::post('apply-coupon', [CartController::class, 'applyCoupon'])->name('apply-coupon');
        Route::post('remove-coupon', [CartController::class, 'removeCoupon'])->name('remove-coupon');
    });

    // Checkout
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index')->middleware('auth');
        Route::post('/', [CheckoutController::class, 'store'])->name('store')->middleware('auth');
    });

    // Blog
    Route::prefix('blog')->name('blog.')->group(function () {
        Route::get('/', [BlogController::class, 'index'])->name('index');
        Route::get('/category/{slug}', [BlogController::class, 'category'])->name('category');
        Route::get('/tag/{slug}', [BlogController::class, 'tag'])->name('tag');
        Route::get('/{slug}', [BlogController::class, 'show'])->name('show');
        Route::post('/{slug}/comment', [BlogController::class, 'storeComment'])->name('comment.store')->middleware('auth');
    });

    // Pages
    Route::get('about', [PageController::class, 'about'])->name('about');
    Route::match(['get', 'post'], 'contact', [PageController::class, 'contact'])->name('contact');
    Route::get('faq', [PageController::class, 'faq'])->name('faq');
    Route::get('privacy', [PageController::class, 'privacy'])->name('privacy');
    Route::get('terms', [PageController::class, 'terms'])->name('terms');

    Route::match(['get', 'post'], 'track-order', [TrackOrderController::class, 'index'])->name('track-order');

    // User Account
    Route::middleware('auth')->group(function () {
        Route::get('account', [AccountController::class, 'index'])->name('account');
        Route::get('account/orders', [AccountController::class, 'orders'])->name('account.orders');
        Route::get('account/downloads', [AccountController::class, 'downloads'])->name('account.downloads');
        Route::get('account/addresses', [AccountController::class, 'addresses'])->name('account.addresses');
        Route::get('account/details', [AccountController::class, 'details'])->name('account.details');
        Route::get('account/orders/{order}', [AccountController::class, 'showOrder'])->name('account.orders.show');
    });

    Route::get('wishlist', [WishlistController::class, 'index'])->name('wishlist');
    Route::post('wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('wishlist/{product}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::get('compare', [CompareController::class, 'index'])->name('compare');
    Route::post('compare/toggle', [CompareController::class, 'toggle'])->name('compare.toggle');
    Route::delete('compare/{product}', [CompareController::class, 'destroy'])->name('compare.destroy');
    Route::post('compare/clear', [CompareController::class, 'clear'])->name('compare.clear');
});
