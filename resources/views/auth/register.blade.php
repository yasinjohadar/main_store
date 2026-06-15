@extends('layouts.auth')

@section('auth-title', 'إنشاء حساب')
@section('auth-heading', 'إنشاء حساب جديد')
@section('auth-subheading', 'املأ البيانات التالية للبدء')

@section('auth-visual-title', 'انضم إلينا اليوم!')
@section('auth-visual-text')
    أنشئ حساباً مجانياً لتتبع طلباتك، حفظ مفضلاتك، والتسوق بسرعة وأمان في أي وقت.
@endsection

@section('auth-features')
    <div class="auth-login__feature"><i class="fas fa-user-check"></i><span>حساب آمن وسهل الإدارة</span></div>
    <div class="auth-login__feature"><i class="fas fa-box"></i><span>متابعة الطلبات والشحن</span></div>
    <div class="auth-login__feature"><i class="fas fa-heart"></i><span>قائمة مفضلة شخصية</span></div>
@endsection

@section('auth-content')
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="auth-login__field">
            <label for="name">الاسم الكامل</label>
            <div class="auth-login__input-wrap">
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="أدخل اسمك" @class(['auth-login__input--error' => $errors->has('name')])>
                <span class="auth-login__input-icon" aria-hidden="true"><i class="fas fa-user"></i></span>
            </div>
            @if ($errors->get('name'))
                <div class="auth-login__errors">
                    <ul>@foreach ((array) $errors->get('name') as $message)<li>{{ $message }}</li>@endforeach</ul>
                </div>
            @endif
        </div>

        <div class="auth-login__field">
            <label for="email">البريد الإلكتروني</label>
            <div class="auth-login__input-wrap">
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="example@email.com" @class(['auth-login__input--error' => $errors->has('email')])>
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
                <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="أدخل كلمة مرور قوية" @class(['auth-login__input--error' => $errors->has('password')])>
                <button type="button" class="auth-login__toggle-pass" data-toggle-password="password" aria-label="إظهار كلمة المرور">
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

        <div class="auth-login__field">
            <label for="password_confirmation">تأكيد كلمة المرور</label>
            <div class="auth-login__input-wrap">
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="أعد إدخال كلمة المرور" @class(['auth-login__input--error' => $errors->has('password_confirmation')])>
                <button type="button" class="auth-login__toggle-pass" data-toggle-password="password_confirmation" aria-label="إظهار كلمة المرور">
                    <i class="fas fa-eye"></i>
                </button>
                <span class="auth-login__input-icon" aria-hidden="true"><i class="fas fa-lock"></i></span>
            </div>
            @if ($errors->get('password_confirmation'))
                <div class="auth-login__errors">
                    <ul>@foreach ((array) $errors->get('password_confirmation') as $message)<li>{{ $message }}</li>@endforeach</ul>
                </div>
            @endif
        </div>

        <div class="auth-login__actions auth-login__actions--stack">
            <button type="submit" class="auth-login__submit">
                <i class="fas fa-user-plus"></i>
                إنشاء حساب
            </button>
        </div>
    </form>
@endsection

@section('auth-footer')
    <p class="auth-login__footer">
        لديك حساب بالفعل؟
        <a href="{{ route('login') }}">تسجيل الدخول</a>
    </p>
@endsection

@push('auth-scripts')
    <script>
        document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(this.dataset.togglePassword);
                if (!input) return;
                var icon = this.querySelector('i');
                var isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                icon.classList.toggle('fa-eye', !isHidden);
                icon.classList.toggle('fa-eye-slash', isHidden);
                this.setAttribute('aria-label', isHidden ? 'إخفاء كلمة المرور' : 'إظهار كلمة المرور');
            });
        });
    </script>
@endpush
