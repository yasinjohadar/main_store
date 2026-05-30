@php
    $user = auth()->user();
    $activeTab = $activeTab ?? 'addresses';

    $renderAddress = function ($address) {
        if (! $address) {
            return null;
        }

        if ($address instanceof \App\Models\CustomerAddress) {
            return [
                'name' => $address->name,
                'line' => trim($address->address_line_1 . ' ' . ($address->address_line_2 ?? '')),
                'city' => $address->city,
                'country' => $address->country,
                'postal' => $address->postal_code,
                'phone' => $address->phone,
            ];
        }

        return [
            'name' => $address->full_name,
            'line' => $address->address_line_1,
            'city' => $address->city,
            'country' => $address->country,
            'postal' => $address->postal_code,
            'phone' => $address->phone ?? null,
        ];
    };

    $billing = $renderAddress($billingAddress ?? null);
    $shipping = $renderAddress($shippingAddress ?? null);
@endphp

<div class="tab-pane {{ $activeTab === 'addresses' ? 'active in' : '' }}" id="account-addresses">
    <div class="account-panel">
        @include('frontend.partials.account.section-header', [
            'icon' => 'w-icon-map-marker',
            'title' => 'العناوين',
            'subtitle' => 'عناوين الشحن والفوترة المستخدمة في الدفع',
            'theme' => 'theme-addresses',
        ])

        <div class="account-panel__body">
            <div class="row gutter-sm">
                <div class="col-md-6 mb-4">
                    <div class="account-info-card theme-addresses">
                        <div class="account-info-card__head">
                            <span class="account-info-card__icon"><i class="w-icon-map-marker"></i></span>
                            <h5 class="account-info-card__title">عنوان الفوترة</h5>
                        </div>
                        @if($billing)
                            <dl class="account-dl">
                                <div><dt>الاسم</dt><dd>{{ $billing['name'] }}</dd></div>
                                <div><dt>العنوان</dt><dd>{{ $billing['line'] }}</dd></div>
                                <div><dt>المدينة</dt><dd>{{ $billing['city'] }}</dd></div>
                                <div><dt>الدولة</dt><dd>{{ $billing['country'] }}</dd></div>
                                <div><dt>الرمز البريدي</dt><dd>{{ $billing['postal'] }}</dd></div>
                                @if($billing['phone'])
                                    <div><dt>الجوال</dt><dd>{{ $billing['phone'] }}</dd></div>
                                @endif
                            </dl>
                        @else
                            <div class="account-info-card__empty">
                                <p class="mb-1">{{ $user->name }}</p>
                                <p class="mb-0 text-muted">{{ $user->email }}</p>
                                <small class="text-muted d-block mt-2">لم يُحفظ عنوان فوترة بعد.</small>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="account-info-card theme-account">
                        <div class="account-info-card__head">
                            <span class="account-info-card__icon"><i class="w-icon-truck"></i></span>
                            <h5 class="account-info-card__title">عنوان الشحن</h5>
                        </div>
                        @if($shipping)
                            <dl class="account-dl">
                                <div><dt>الاسم</dt><dd>{{ $shipping['name'] }}</dd></div>
                                <div><dt>العنوان</dt><dd>{{ $shipping['line'] }}</dd></div>
                                <div><dt>المدينة</dt><dd>{{ $shipping['city'] }}</dd></div>
                                <div><dt>الدولة</dt><dd>{{ $shipping['country'] }}</dd></div>
                                <div><dt>الرمز البريدي</dt><dd>{{ $shipping['postal'] }}</dd></div>
                                @if($shipping['phone'])
                                    <div><dt>الجوال</dt><dd>{{ $shipping['phone'] }}</dd></div>
                                @endif
                            </dl>
                        @else
                            <div class="account-info-card__empty">
                                <p class="mb-0 text-muted">لم يُحفظ عنوان شحن بعد.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <a href="{{ route('frontend.checkout.index') }}" class="account-btn account-btn--primary account-btn--sm">
                تحديث العناوين عند الدفع<i class="w-icon-long-arrow-left"></i>
            </a>
        </div>
    </div>
</div>
