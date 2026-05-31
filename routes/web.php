<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;

Route::get('/dashboard', function () {
    return redirect(auth_home_route());
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth', 'check.user.active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin routes
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::put('users/{user}/change-password', [UserController::class, 'updatePassword'])->name('users.update-password');
});

// مسار toggle-status بدون middleware check.user.active
Route::middleware(['auth'])->group(function () {
    Route::post('users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
});

// مسار بديل للتجربة
Route::post('toggle-user-status/{id}', [UserController::class, 'toggleStatus'])->name('users.toggle-status-alt');

// Legacy store routes — redirect to unified frontend
Route::prefix('store')->name('store.')->group(function () {
    Route::get('cart', fn () => redirect()->route('frontend.cart.index', [], 301))->name('cart.index');
    Route::get('checkout', fn () => redirect()->route('frontend.checkout.index', [], 301))->name('checkout.index');
    Route::get('checkout/success/{order}', fn ($order) => redirect()->route('frontend.checkout.success', $order, 301))->name('checkout.success');
    Route::get('downloads/{token}', fn ($token) => redirect()->route('frontend.downloads.show', $token, 301))->name('downloads.show');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/frontend.php';