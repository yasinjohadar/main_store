<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        $this->ensureProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider, CartService $cartService): RedirectResponse
    {
        $this->ensureProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Throwable) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'تعذّر تسجيل الدخول عبر ' . $this->providerLabel($provider) . '. حاول مرة أخرى.']);
        }

        $idColumn = $provider . '_id';
        $user = User::query()->where($idColumn, $socialUser->getId())->first();

        if (! $user && $socialUser->getEmail()) {
            $user = User::query()->where('email', $socialUser->getEmail())->first();

            if ($user) {
                $user->update([$idColumn => $socialUser->getId()]);
            }
        }

        if (! $user) {
            $email = $socialUser->getEmail() ?: $provider . '_' . $socialUser->getId() . '@social.local';

            $user = User::create([
                'name' => $socialUser->getName() ?: 'مستخدم',
                'email' => $email,
                'password' => bcrypt(Str::random(40)),
                $idColumn => $socialUser->getId(),
                'email_verified_at' => $socialUser->getEmail() ? now() : null,
                'photo' => $socialUser->getAvatar(),
                'is_active' => true,
                'status' => 'active',
            ]);
        }

        if (! $user->is_active) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'تم إلغاء تفعيل حسابك. يرجى التواصل مع الإدارة.']);
        }

        $guestSessionId = request()->session()->getId();

        Auth::login($user, true);
        $cartService->mergeGuestCartIntoUser($user->id, $guestSessionId);
        request()->session()->regenerate();

        return redirect()->intended(auth_home_route());
    }

    protected function ensureProvider(string $provider): void
    {
        if (! in_array($provider, ['google', 'facebook'], true)) {
            abort(404);
        }

        if (! social_login_enabled($provider)) {
            abort(503, 'Social login is not configured.');
        }
    }

    protected function providerLabel(string $provider): string
    {
        return match ($provider) {
            'google' => 'Google',
            'facebook' => 'Facebook',
            default => $provider,
        };
    }
}
