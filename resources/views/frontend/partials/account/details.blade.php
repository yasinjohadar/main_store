@php
    $user = auth()->user();
    $nameParts = explode(' ', $user->name, 2);
    $activeTab = $activeTab ?? 'details';

    $statusMessages = [
        'profile-updated' => 'تم تحديث بيانات الحساب بنجاح.',
        'password-updated' => 'تم تحديث كلمة المرور بنجاح.',
    ];
@endphp

<div class="tab-pane {{ $activeTab === 'details' ? 'active in' : '' }}" id="account-details">
    <div class="account-panel">
        @include('frontend.partials.account.section-header', [
            'icon' => 'w-icon-user',
            'title' => 'تفاصيل الحساب',
            'subtitle' => 'إدارة بياناتك الشخصية وكلمة المرور',
            'theme' => 'theme-account',
        ])

        <div class="account-panel__body">
            @if(session('status') && isset($statusMessages[session('status')]))
                <div class="account-alert account-alert--success">
                    <i class="w-icon-check-solid"></i>
                    {{ $statusMessages[session('status')] }}
                </div>
            @endif

            <div class="account-form-card mb-4">
                <h5 class="account-form-card__title">البيانات الشخصية</h5>
                <form class="account-form" method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('patch')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="firstname">الاسم الأول *</label>
                                <input type="text" id="firstname" name="name"
                                    value="{{ old('name', $nameParts[0] ?? $user->name) }}"
                                    class="form-control form-control-md" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lastname">اسم العائلة</label>
                                <input type="text" id="lastname" value="{{ $nameParts[1] ?? '' }}"
                                    class="form-control form-control-md" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="email_1">البريد الإلكتروني *</label>
                        <input type="email" id="email_1" name="email" value="{{ old('email', $user->email) }}"
                            class="form-control form-control-md" required>
                        @error('email')<p class="account-field-error">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="account-btn account-btn--primary account-btn--sm">حفظ التغييرات</button>
                </form>
            </div>

            <div class="account-form-card">
                <h5 class="account-form-card__title">تغيير كلمة المرور</h5>
                <form class="account-form" method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')
                    <div class="form-group">
                        <label for="current_password">كلمة المرور الحالية</label>
                        <input type="password" class="form-control form-control-md" id="current_password" name="current_password">
                        @error('current_password', 'updatePassword')<p class="account-field-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label for="password">كلمة المرور الجديدة</label>
                        <input type="password" class="form-control form-control-md" id="password" name="password">
                        @error('password', 'updatePassword')<p class="account-field-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group mb-4">
                        <label for="password_confirmation">تأكيد كلمة المرور</label>
                        <input type="password" class="form-control form-control-md" id="password_confirmation" name="password_confirmation">
                    </div>
                    <button type="submit" class="account-btn account-btn--primary account-btn--sm">تحديث كلمة المرور</button>
                </form>
            </div>

            <div class="account-form-card mt-4">
                <h5 class="account-form-card__title">الخصوصية والبيانات</h5>

                <a href="{{ route('frontend.account.privacy.export') }}" class="account-btn account-btn--outline account-btn--sm">
                    تنزيل بياناتي
                </a>

                <form class="account-form mt-3" method="POST" action="{{ route('frontend.account.privacy.destroy') }}"
                    onsubmit="return confirm('سيتم حذف بيانات حسابك بشكل نهائي. هل أنت متأكد؟');">
                    @csrf
                    @method('delete')
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="confirm_delete" name="confirm" required>
                        <label class="form-check-label" for="confirm_delete">أفهم أن هذا الإجراء نهائي ولا يمكن التراجع عنه</label>
                    </div>
                    <button type="submit" class="account-btn account-btn--danger account-btn--sm">حذف حسابي نهائياً</button>
                </form>
            </div>
        </div>
    </div>
</div>
