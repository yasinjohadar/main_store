<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCheckoutAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            return $next($request);
        }

        if (config('store.guest_checkout_enabled', false)) {
            return $next($request);
        }

        return redirect()->route('login')->with('error', 'يجب تسجيل الدخول لإتمام الشراء');
    }
}
