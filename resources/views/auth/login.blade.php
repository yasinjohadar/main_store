<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - {{ __('Log in') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: #020617; /* slate-950 - خلفية داكنة بدون تدرج */
            color: #e5e7eb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .container {
            width: 100%;
            max-width: 960px;
            display: flex;
            gap: 2.5rem;
            align-items: stretch;
        }

        .brand-panel {
            flex: 1.1;
            background-color: #020617;
            border-radius: 1.25rem;
            border: 1px solid rgba(148, 163, 184, 0.25);
            padding: 2rem 2.25rem;
            display: none;
            flex-direction: column;
            justify-content: space-between;
        }

        .brand-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .brand-logo {
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 0.9rem;
            background-color: #0f172a;
            border: 1px solid rgba(148, 163, 184, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-logo svg {
            width: 1.8rem;
            height: 1.8rem;
            fill: #22c55e;
        }

        .brand-title {
            display: flex;
            flex-direction: column;
            gap: 0.1rem;
        }

        .brand-title span:first-child {
            font-size: 1.05rem;
            font-weight: 700;
            color: #f9fafb;
        }

        .brand-title span:last-child {
            font-size: 0.9rem;
            color: #9ca3af;
        }

        .brand-highlights {
            margin-top: 2.25rem;
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .brand-highlight {
            padding: 0.9rem 1rem;
            border-radius: 0.9rem;
            background-color: #020617;
            border: 1px solid rgba(148, 163, 184, 0.35);
        }

        .brand-highlight-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #e5e7eb;
            margin-bottom: 0.25rem;
        }

        .brand-highlight-desc {
            font-size: 0.85rem;
            color: #9ca3af;
        }

        .brand-footer {
            margin-top: 2rem;
            font-size: 0.8rem;
            color: #6b7280;
        }

        .auth-panel {
            flex: 1;
            background-color: #020617;
            border-radius: 1.25rem;
            border: 1px solid rgba(148, 163, 184, 0.3);
            box-shadow: 0 18px 60px rgba(15, 23, 42, 0.85);
            padding: 2.25rem 2.4rem 2.1rem;
        }

        .page-title {
            margin-bottom: 1.75rem;
        }

        .page-title h1 {
            font-size: 1.45rem;
            font-weight: 700;
            color: #f9fafb;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 0.6rem;
        }

        .page-title .icon-edu {
            width: 2.1rem;
            height: 2.1rem;
            fill: #22c55e;
        }

        .page-title p {
            margin-top: 0.3rem;
            font-size: 0.9rem;
            color: #9ca3af;
        }

        .session-status {
            font-weight: 500;
            font-size: 0.85rem;
            color: #22c55e;
            margin-bottom: 1rem;
            padding: 0.45rem 0.75rem;
            border-radius: 0.6rem;
            background-color: rgba(34, 197, 94, 0.08);
            border: 1px solid rgba(34, 197, 94, 0.35);
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group:last-of-type {
            margin-bottom: 0;
        }

        label {
            display: block;
            font-weight: 600;
            font-size: 0.88rem;
            color: #e5e7eb;
            margin-bottom: 0.35rem;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.7rem 0.95rem;
            border-radius: 0.65rem;
            border: 1px solid #374151;
            font-size: 0.94rem;
            font-family: 'Cairo', sans-serif;
            color: #f9fafb;
            background-color: #020617;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
        }

        input[type="email"]::placeholder,
        input[type="password"]::placeholder {
            color: #6b7280;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #22c55e;
            box-shadow: 0 0 0 1px rgba(34, 197, 94, 0.65);
            background-color: #020617;
        }

        input[type="checkbox"] {
            width: 1.05rem;
            height: 1.05rem;
            border-radius: 0.3rem;
            border: 1px solid #4b5563;
            accent-color: #22c55e;
            cursor: pointer;
        }

        .remember-me {
            display: block;
            margin-top: 0.85rem;
        }

        .remember-me label {
            display: inline-flex;
            align-items: center;
            font-weight: 500;
            margin-bottom: 0;
            cursor: pointer;
        }

        .remember-me span {
            margin-right: 0.5rem;
            font-size: 0.88rem;
            color: #9ca3af;
        }

        .error-messages {
            font-size: 0.82rem;
            color: #f97373;
            margin-top: 0.4rem;
        }

        .error-messages ul {
            list-style: none;
            padding: 0;
        }

        .error-messages li {
            margin-top: 0.18rem;
        }

        .admin-fill-row {
            margin-top: 1.1rem;
            margin-bottom: 1.2rem;
        }

        .btn-admin-fill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 0.9rem;
            font-family: 'Cairo', sans-serif;
            font-size: 0.8rem;
            font-weight: 600;
            color: #22c55e;
            background-color: rgba(34, 197, 94, 0.08);
            border: 1px solid rgba(34, 197, 94, 0.4);
            border-radius: 0.6rem;
            cursor: pointer;
            transition: background-color 0.18s ease, border-color 0.18s ease, color 0.18s ease;
        }

        .btn-admin-fill:hover {
            background-color: rgba(34, 197, 94, 0.16);
            border-color: #22c55e;
        }

        .btn-admin-fill svg {
            width: 1rem;
            height: 1rem;
            fill: currentColor;
        }

        .form-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1.35rem;
        }

        .form-actions a {
            font-size: 0.85rem;
            color: #9ca3af;
            text-decoration: none;
        }

        .form-actions a:hover {
            color: #e5e7eb;
        }

        button[type="submit"] {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.7rem 1.6rem;
            font-family: 'Cairo', sans-serif;
            font-size: 0.96rem;
            font-weight: 600;
            background-color: #22c55e;
            color: #020617;
            border: none;
            border-radius: 0.7rem;
            cursor: pointer;
            transition: background-color 0.18s ease, box-shadow 0.18s ease, transform 0.08s ease;
        }

        button[type="submit"]:hover {
            background-color: #16a34a;
            box-shadow: 0 10px 26px rgba(22, 163, 74, 0.55);
        }

        button[type="submit"]:active {
            transform: translateY(1px);
            box-shadow: 0 6px 18px rgba(22, 163, 74, 0.45);
        }

        @media (max-width: 768px) {
            .container {
                max-width: 420px;
                flex-direction: column;
                gap: 1.5rem;
            }

            .auth-panel {
                padding: 1.75rem 1.7rem 1.6rem;
            }
        }

        @media (min-width: 900px) {
            .brand-panel {
                display: flex;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-title">
            <h1>
                <svg class="icon-edu" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"/></svg>
                تسجيل الدخول
            </h1>
        </div>

        <div class="form-container">
            @if (session('status'))
                <div class="session-status">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email">{{ __('Email') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="admin@admin.com" />
                    @if ($errors->get('email'))
                        <div class="error-messages">
                            <ul>
                                @foreach ((array) $errors->get('email') as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="password">{{ __('Password') }}</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="كلمة المرور" />
                    @if ($errors->get('password'))
                        <div class="error-messages">
                            <ul>
                                @foreach ((array) $errors->get('password') as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="remember-me">
                    <label for="remember_me">
                        <input id="remember_me" type="checkbox" name="remember">
                        <span>{{ __('Remember me') }}</span>
                    </label>
                </div>

                @if (config('app.debug'))
                <div class="admin-fill-row">
                    <button type="button" class="btn-admin-fill" id="btnAdminFill" title="تعبئة البريد وكلمة المرور من بيانات الأدمن في الـ seed">
                        <svg viewBox="0 0 24 24"><path d="M19 2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 2c1.65 0 3 1.35 3 3s-1.35 3-3 3-3-1.35-3-3 1.35-3 3-3zm6 14H6v-.23c0-1.47 2.94-2.24 4.5-2.24 1.56 0 4.5.77 4.5 2.24V18z"/></svg>
                        استخدام بيانات الأدمن
                    </button>
                </div>
                @endif

                <div class="form-actions">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                    <button type="submit">
                        {{ __('Log in') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if (config('app.debug'))
    <script>
        document.getElementById('btnAdminFill').addEventListener('click', function() {
            document.getElementById('email').value = 'admin@admin.com';
            document.getElementById('password').value = '123456789';
        });
    </script>
    @endif
</body>
</html>
