@php
    use App\Services\SiteSettingsService as SiteSettings;
    $siteName = site_setting(SiteSettings::KEY_SITE_NAME, config('app.name'));
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $siteName }} - @yield('auth-title', 'حسابي')</title>
    @php($authFavicon = site_setting_url(SiteSettings::KEY_SITE_FAVICON) ?: asset('frontend/assets/images/icons/favicon.png'))
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
                    <h2 class="auth-login__visual-title">@yield('auth-visual-title', 'مرحباً بك!')</h2>
                    <p class="auth-login__visual-text">@yield('auth-visual-text')</p>
                </div>
                <div class="auth-login__features">
                    @hasSection('auth-features')
                        @yield('auth-features')
                    @else
                        <div class="auth-login__feature"><i class="fas fa-truck"></i><span>شحن سريع وموثوق</span></div>
                        <div class="auth-login__feature"><i class="fas fa-shield-alt"></i><span>دفع آمن ومتعدد الخيارات</span></div>
                        <div class="auth-login__feature"><i class="fas fa-heart"></i><span>قائمة مفضلة ومقارنة منتجات</span></div>
                    @endif
                </div>
            </aside>

            <main class="auth-login__main">
                <div class="auth-login__card">
                    <header class="auth-login__head">
                        <h1 class="auth-login__title">@yield('auth-heading')</h1>
                        @hasSection('auth-subheading')
                            <p class="auth-login__subtitle">@yield('auth-subheading')</p>
                        @endif
                    </header>

                    @yield('auth-content')

                    @hasSection('auth-footer')
                        @yield('auth-footer')
                    @endif
                </div>
            </main>
        </div>
    </div>

    @stack('auth-scripts')
</body>
</html>
