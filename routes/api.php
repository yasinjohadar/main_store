<?php

use App\Http\Controllers\Api\V1\CartApiController;
use App\Http\Controllers\Api\V1\CheckoutApiController;
use App\Http\Controllers\Api\V1\OrderApiController;
use App\Http\Controllers\Api\V1\ProductApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/store')->name('api.v1.store.')->group(function () {
    Route::get('products', [ProductApiController::class, 'index'])->name('products.index');
    Route::get('products/{slug}', [ProductApiController::class, 'show'])->name('products.show');

    Route::get('cart', [CartApiController::class, 'show'])->name('cart.show');
    Route::post('cart/items', [CartApiController::class, 'store'])->name('cart.store');
    Route::patch('cart/items/{id}', [CartApiController::class, 'update'])->name('cart.update');
    Route::delete('cart/items/{id}', [CartApiController::class, 'destroy'])->name('cart.destroy');
    Route::post('cart/coupon', [CartApiController::class, 'applyCoupon'])->name('cart.coupon');

    Route::post('checkout/totals', [CheckoutApiController::class, 'totals'])->name('checkout.totals');
    Route::post('checkout', [CheckoutApiController::class, 'store'])->middleware('auth:sanctum')->name('checkout.store');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('orders', [OrderApiController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderApiController::class, 'show'])->name('orders.show');
    });
});

Route::post('webhooks/stripe', [\App\Http\Controllers\WebhookController::class, 'stripe'])->name('webhooks.stripe');
Route::post('webhooks/paypal', [\App\Http\Controllers\WebhookController::class, 'paypal'])->name('webhooks.paypal');
