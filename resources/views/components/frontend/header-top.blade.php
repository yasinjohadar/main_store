@php
    $currencyCode = $displayCurrency?->code ?? 'SAR';
    $currencyLabel = $displayCurrency?->symbol ?: $currencyCode;
@endphp
            <div class="header-top">
                <div class="container">
                    <div class="header-left">
                        <p class="welcome-msg">مرحباً بك — توصيل سريع وعروض حصرية</p>
                    </div>
                    <div class="header-right pl-0">
                        @if(($currencies ?? collect())->count() > 1)
                            <div class="dropdown">
                                <a href="#currency" class="header-top-link">
                                    <i class="w-icon-money" aria-hidden="true"></i>{{ $currencyLabel }}
                                </a>
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
                            <span class="header-top-link header-top-link--static">
                                <i class="w-icon-money" aria-hidden="true"></i>{{ $currencyLabel }}
                            </span>
                        @endif

                        <span class="divider d-lg-show"></span>

                        <a href="{{ route('frontend.blog.index') }}" class="header-top-link d-lg-show">
                            <i class="w-icon-comments" aria-hidden="true"></i>المدونة
                        </a>
                        <a href="{{ route('frontend.contact') }}" class="header-top-link d-lg-show">
                            <i class="w-icon-call" aria-hidden="true"></i>اتصل بنا
                        </a>

                        @auth
                            <a href="{{ route('frontend.account') }}" class="header-top-link d-lg-show">
                                <i class="w-icon-account" aria-hidden="true"></i>حسابي
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <a href="#" class="header-top-link d-lg-show"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    <i class="w-icon-logout" aria-hidden="true"></i>تسجيل الخروج
                                </a>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="header-top-link d-lg-show">
                                <i class="w-icon-account" aria-hidden="true"></i>حسابي
                            </a>
                            <a href="{{ route('login') }}" class="header-top-link d-lg-show">
                                <i class="w-icon-account" aria-hidden="true"></i>تسجيل الدخول
                            </a>
                            <span class="delimiter d-lg-show">/</span>
                            <a href="{{ route('register') }}" class="header-top-link ml-0 d-lg-show">
                                <i class="w-icon-user" aria-hidden="true"></i>إنشاء حساب
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
