@php
    $savedAddresses = $savedAddresses ?? collect();
@endphp

<div class="tab-pane {{ $activeTab === 'addresses' ? 'active in' : '' }}" id="account-addresses">
    <div class="account-panel">
        @include('frontend.partials.account.section-header', [
            'icon' => 'w-icon-map-marker',
            'title' => 'العناوين',
            'subtitle' => 'إدارة عناوين الشحن والفوترة',
            'theme' => 'theme-addresses',
        ])

        <div class="account-panel__body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @foreach($savedAddresses as $address)
                <div class="account-info-card theme-addresses mb-4">
                    <div class="account-info-card__head d-flex justify-content-between align-items-center">
                        <h5 class="account-info-card__title mb-0">
                            {{ $address->type === 'billing' ? 'فوترة' : 'شحن' }}
                            @if($address->is_default)<span class="badge bg-primary ms-1">افتراضي</span>@endif
                        </h5>
                        <form method="POST" action="{{ route('frontend.account.addresses.destroy', $address) }}" onsubmit="return confirm('حذف العنوان؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                        </form>
                    </div>
                    <form method="POST" action="{{ route('frontend.account.addresses.update', $address) }}" class="mt-3">
                        @csrf @method('PUT')
                        <input type="hidden" name="type" value="{{ $address->type }}">
                        <div class="row gutter-sm">
                            <div class="col-md-6 mb-2">
                                <input type="text" name="name" class="form-control" value="{{ $address->name }}" placeholder="الاسم">
                            </div>
                            <div class="col-md-6 mb-2">
                                <input type="text" name="phone" class="form-control" value="{{ $address->phone }}" placeholder="الجوال">
                            </div>
                            <div class="col-12 mb-2">
                                <input type="text" name="address_line_1" class="form-control" value="{{ $address->address_line_1 }}" required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <input type="text" name="city" class="form-control" value="{{ $address->city }}" required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <input type="text" name="country" class="form-control" value="{{ $address->country }}" maxlength="2" required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <input type="text" name="postal_code" class="form-control" value="{{ $address->postal_code }}">
                            </div>
                            <div class="col-12">
                                <label class="me-2"><input type="checkbox" name="is_default" value="1" @checked($address->is_default)> افتراضي</label>
                                <button type="submit" class="btn btn-sm btn-primary">حفظ</button>
                            </div>
                        </div>
                    </form>
                </div>
            @endforeach

            <div class="account-info-card theme-account">
                <div class="account-info-card__head">
                    <h5 class="account-info-card__title">إضافة عنوان جديد</h5>
                </div>
                <form method="POST" action="{{ route('frontend.account.addresses.store') }}" class="mt-3">
                    @csrf
                    <div class="row gutter-sm">
                        <div class="col-md-6 mb-2">
                            <select name="type" class="form-control" required>
                                <option value="shipping">شحن</option>
                                <option value="billing">فوترة</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <input type="text" name="name" class="form-control" placeholder="الاسم">
                        </div>
                        <div class="col-md-6 mb-2">
                            <input type="text" name="phone" class="form-control" placeholder="الجوال">
                        </div>
                        <div class="col-12 mb-2">
                            <input type="text" name="address_line_1" class="form-control" placeholder="العنوان" required>
                        </div>
                        <div class="col-md-4 mb-2">
                            <input type="text" name="city" class="form-control" placeholder="المدينة" required>
                        </div>
                        <div class="col-md-4 mb-2">
                            <input type="text" name="country" class="form-control" value="SA" maxlength="2" required>
                        </div>
                        <div class="col-md-4 mb-2">
                            <input type="text" name="postal_code" class="form-control" placeholder="الرمز البريدي">
                        </div>
                        <div class="col-12">
                            <label class="me-2"><input type="checkbox" name="is_default" value="1"> تعيين كافتراضي</label>
                            <button type="submit" class="account-btn account-btn--primary account-btn--sm">إضافة</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
