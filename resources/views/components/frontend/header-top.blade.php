@php
    $currencyCode = $displayCurrency?->code ?? 'SAR';
    $currencyLabel = $displayCurrency?->symbol ?: $currencyCode;
@endphp
            <div class="header-top">
                <div class="container">
                    <div class="header-left">
                        <p class="welcome-msg">مرحباً بك في {{ config('app.name') }} — توصيل سريع وعروض حصرية</p>
                    </div>
                    <div class="header-right pl-0">
                        @if(($currencies ?? collect())->count() > 1)
                            <div class="dropdown">
                                <a href="#currency">{{ $currencyLabel }}</a>
                                <div class="dropdown-box">
                                    @foreach($currencies as $currency)
                                        <a href="{{ route('frontend.currency.switch', $currency->code) }}"
                                            @class(['active' => $currency->code === $currencyCode])>
                                            {{ $currency->symbol ? $currency->symbol . ' ' : '' }}{{ $currency->code }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @elseif($displayCurrency)
                            <span class="currency-label">{{ $currencyLabel }}</span>
                        @endif

                        <span class="lang-label d-lg-show">العربية</span>

                        <span class="divider d-lg-show"></span>
                        <a href="{{ route('frontend.blog.index') }}" class="d-lg-show">المدونة</a>
                        <a href="{{ route('frontend.contact') }}" class="d-lg-show">اتصل بنا</a>

                        @auth
                            <a href="{{ route('frontend.account') }}" class="d-lg-show">حسابي</a>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <a href="#" class="d-lg-show login sign-in"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    <i class="w-icon-account"></i>تسجيل الخروج
                                </a>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="d-lg-show">حسابي</a>
                            <a href="{{ route('login') }}" class="d-lg-show login sign-in">
                                <i class="w-icon-account"></i>تسجيل الدخول
                            </a>
                            <span class="delimiter d-lg-show">/</span>
                            <a href="{{ route('register') }}" class="ml-0 d-lg-show login register">إنشاء حساب</a>
                        @endauth
                    </div>
                </div>
            </div>
