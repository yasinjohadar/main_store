<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        use App\Services\SiteSettingsService as SiteSettings;
        $siteName = site_setting(SiteSettings::KEY_SITE_NAME, config('app.name'));
    @endphp
    <title>{{ $siteName }} - تسجيل الدخول</title>
    @php($authFavicon = site_setting_url(\App\Services\SiteSettingsService::KEY_SITE_FAVICON) ?: asset('frontend/assets/images/icons/favicon.png'))
    <link rel="icon" href="{{ $authFavicon }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/fontawesome-free/css/all.min.css') }}">
    @include('frontend.partials.store-theme')
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/auth-login.css') }}">
</head>
<body class="auth-login-page">
    <div class="auth-login">
        <a href="{{ route('frontend.home') }}" class="auth-login__back">
            <i class="fas fa-arrow-right"></i>
            العودة للمتجر
        </a>

        <div class="auth-login__shell">
            <aside class="auth-login__visual" aria-hidden="true">
                <div class="auth-login__visual-inner">
                    <div class="auth-login__brand">
                        <span class="auth-login__brand-icon"><i class="fas fa-store"></i></span>
                        <div>
                            <p class="auth-login__brand-name">{{ $siteName }}</p>
                            <p class="auth-login__brand-tagline">تسوّق بذكاء واطمئنان</p>
                        </div>
                    </div>
                    <h2 class="auth-login__visual-title">مرحباً بعودتك!</h2>
                    <p class="auth-login__visual-text">
                        سجّل دخولك لمتابعة طلباتك، إدارة حسابك، والاستمتاع بتجربة تسوق سلسة وآمنة.
                    </p>
                </div>
                <div class="auth-login__features">
                    <div class="auth-login__feature"><i class="fas fa-truck"></i><span>شحن سريع وموثوق</span></div>
                    <div class="auth-login__feature"><i class="fas fa-shield-alt"></i><span>دفع آمن ومتعدد الخيارات</span></div>
                    <div class="auth-login__feature"><i class="fas fa-heart"></i><span>قائمة مفضلة ومقارنة منتجات</span></div>
                </div>
            </aside>

            <main class="auth-login__main">
                <div class="auth-login__card">
                    <header class="auth-login__head">
                        <h1 class="auth-login__title">تسجيل الدخول</h1>
                        <p class="auth-login__subtitle">أدخل بياناتك للمتابعة إلى حسابك</p>
                    </header>

                    @if ($socialGoogleEnabled || $socialFacebookEnabled)
                        <div class="auth-login__social">
                            @if ($socialGoogleEnabled)
                                <a href="{{ route('auth.social.redirect', 'google') }}" class="auth-login__social-btn auth-login__social-btn--google">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="#EA4335" d="M12 10.2v3.6h5.1c-.2 1.2-1.5 3.6-5.1 3.6-3.1 0-5.6-2.5-5.6-5.6s2.5-5.6 5.6-5.6c1.8 0 3 .8 3.7 1.5l2.5-2.4C16.9 3.6 14.7 2.6 12 2.6 6.9 2.6 2.7 6.8 2.7 12s4.2 9.4 9.3 9.4c5.4 0 9-3.8 9-9.2 0-.6-.1-1.1-.2-1.6H12z"/></svg>
                                    <span>المتابعة باستخدام Google</span>
                                </a>
                            @endif
                            @if ($socialFacebookEnabled)
                                <a href="{{ route('auth.social.redirect', 'facebook') }}" class="auth-login__social-btn auth-login__social-btn--facebook">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M13.5 3H16V0h-2.5C9.6 0 7 2.6 7 6v2H4v3h3v12h3.5V11H16l.5-3H10.5V6c0-1.4 1.1-3 3-3z"/></svg>
                                    <span>المتابعة باستخدام Facebook</span>
                                </a>
                            @endif
                        </div>
                        <div class="auth-login__divider">أو</div>
                    @elseif (config('app.debug'))
                        <p class="auth-login__social-hint">
                            لتفعيل Google/Facebook أضف مفاتيح OAuth في ملف <code>.env</code>
                        </p>
                    @endif

                    @if (session('status'))
                        <div class="auth-login__alert auth-login__alert--success">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="auth-login__field">
                            <label for="email">البريد الإلكتروني</label>
                            <div class="auth-login__input-wrap">
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="example@email.com" @class(['auth-login__input--error' => $errors->has('email')])>
                                <span class="auth-login__input-icon" aria-hidden="true"><i class="fas fa-envelope"></i></span>
                            </div>
                            @if ($errors->get('email'))
                                <div class="auth-login__errors">
                                    <ul>@foreach ((array) $errors->get('email') as $message)<li>{{ $message }}</li>@endforeach</ul>
                                </div>
                            @endif
                        </div>

                        <div class="auth-login__field">
                            <label for="password">كلمة المرور</label>
                            <div class="auth-login__input-wrap">
                                <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="أدخل كلمة المرور" @class(['auth-login__input--error' => $errors->has('password')])>
                                <button type="button" class="auth-login__toggle-pass" id="togglePassword" aria-label="إظهار كلمة المرور">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <span class="auth-login__input-icon" aria-hidden="true"><i class="fas fa-lock"></i></span>
                            </div>
                            @if ($errors->get('password'))
                                <div class="auth-login__errors">
                                    <ul>@foreach ((array) $errors->get('password') as $message)<li>{{ $message }}</li>@endforeach</ul>
                                </div>
                            @endif
                        </div>

                        <label class="auth-login__remember" for="remember_me">
                            <input id="remember_me" type="checkbox" name="remember">
                            <span>تذكرني</span>
                        </label>

                        @if (config('app.debug'))
                            <button type="button" class="auth-login__admin-btn" id="btnAdminFill" title="تعبئة بيانات الأدمن للتطوير">
                                <i class="fas fa-user-shield"></i>
                                استخدام بيانات الأدمن
                            </button>
                        @endif

                        <div class="auth-login__actions">
                            <button type="submit" class="auth-login__submit">
                                <i class="fas fa-sign-in-alt"></i>
                                دخول
                            </button>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="auth-login__forgot">نسيت كلمة المرور؟</a>
                            @endif
                        </div>
                    </form>

                    @if (Route::has('register'))
                        <p class="auth-login__footer">
                            ليس لديك حساب؟
                            <a href="{{ route('register') }}">إنشاء حساب جديد</a>
                        </p>
                    @endif
                </div>
            </main>
        </div>
    </div>

    <script>
        document.getElementById('togglePassword')?.addEventListener('click', function () {
            const input = document.getElementById('password');
            const icon = this.querySelector('i');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !isHidden);
            icon.classList.toggle('fa-eye-slash', isHidden);
            this.setAttribute('aria-label', isHidden ? 'إخفاء كلمة المرور' : 'إظهار كلمة المرور');
        });

        @if (config('app.debug'))
        document.getElementById('btnAdminFill')?.addEventListener('click', function () {
            document.getElementById('email').value = 'admin@admin.com';
            document.getElementById('password').value = '123456789';
        });
        @endif
    </script>
</body>
</html>
