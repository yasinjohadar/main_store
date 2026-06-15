@extends('layouts.auth')

@section('auth-title', 'نسيت كلمة المرور')
@section('auth-heading', 'نسيت كلمة المرور؟')
@section('auth-subheading', 'لا تقلق، سنساعدك على استعادة حسابك')

@section('auth-visual-title', 'استعد الوصول لحسابك')
@section('auth-visual-text')
    أدخل بريدك الإلكتروني وسنرسل لك رابطاً آمناً لإعادة تعيين كلمة المرور خلال دقائق.
@endsection

@section('auth-features')
    <div class="auth-login__feature"><i class="fas fa-envelope-open-text"></i><span>رابط إعادة تعيين آمن</span></div>
    <div class="auth-login__feature"><i class="fas fa-clock"></i><span>استلام سريع عبر البريد</span></div>
    <div class="auth-login__feature"><i class="fas fa-shield-alt"></i><span>حماية كاملة لبياناتك</span></div>
@endsection

@section('auth-content')
    <p class="auth-login__intro">
        أدخل البريد الإلكتروني المرتبط بحسابك وسنرسل إليك تعليمات إعادة تعيين كلمة المرور.
    </p>

    @if (session('status'))
        <div class="auth-login__alert auth-login__alert--success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
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

        <div class="auth-login__actions auth-login__actions--stack">
            <button type="submit" class="auth-login__submit">
                <i class="fas fa-paper-plane"></i>
                إرسال رابط إعادة التعيين
            </button>
        </div>
    </form>
@endsection

@section('auth-footer')
    <p class="auth-login__footer">
        تذكرت كلمة المرور؟
        <a href="{{ route('login') }}">العودة لتسجيل الدخول</a>
    </p>
@endsection
