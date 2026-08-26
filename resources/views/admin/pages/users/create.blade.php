@extends('admin.layouts.master')

@section('page-title')
    إنشاء مستخدم جديد
@stop

@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li class="small">{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="las la-user-plus"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>إنشاء مستخدم جديد</h1>
                        <p>إضافة حساب جديد مع الأدوار والصلاحيات</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('users.index') }}" class="admin-btn admin-btn-light">
                        <i class="las la-arrow-right"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="admin-page-card">
                <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data"
                    class="admin-form" id="user-form" autocomplete="off">
                    @csrf

                    <div class="admin-form-body">

                        <div class="admin-form-section">
                            <div class="admin-form-section-head">
                                <div class="admin-section-icon admin-section-icon-blue">
                                    <i class="las la-user"></i>
                                </div>
                                <div>
                                    <h3>المعلومات الأساسية</h3>
                                    <p>الاسم وبيانات الاتصال</p>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="admin-form-field">
                                        <label class="admin-form-label">الاسم الكامل <span class="required">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            name="name" placeholder="أدخل الاسم الكامل" value="{{ old('name') }}" required>
                                        @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="admin-form-field">
                                        <label class="admin-form-label">اسم المستخدم</label>
                                        <input type="text" class="form-control @error('username') is-invalid @enderror"
                                            name="username" placeholder="اسم المستخدم (اختياري)" value="{{ old('username') }}"
                                            autocomplete="off" autocapitalize="off" spellcheck="false">
                                        @error('username')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="admin-form-field">
                                        <label class="admin-form-label">البريد الإلكتروني <span class="required">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            name="email" placeholder="example@domain.com" value="{{ old('email') }}"
                                            autocomplete="off" required>
                                        @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="admin-form-field">
                                        <label class="admin-form-label">رقم الهاتف</label>
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                            name="phone" placeholder="05xxxxxxxx" value="{{ old('phone') }}">
                                        @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="admin-form-section">
                            <div class="admin-form-section-head">
                                <div class="admin-section-icon admin-section-icon-green">
                                    <i class="las la-lock"></i>
                                </div>
                                <div>
                                    <h3>الأمان</h3>
                                    <p>تعيين كلمة مرور آمنة للحساب</p>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="admin-form-field">
                                        <label class="admin-form-label">كلمة المرور <span class="required">*</span></label>
                                        <div class="admin-password-wrap">
                                            <input type="password" id="password" class="form-control @error('password') is-invalid @enderror"
                                                name="password" placeholder="••••••••" required>
                                            <button type="button" class="admin-password-toggle" data-toggle-password="password" aria-label="إظهار كلمة المرور">
                                                <i class="las la-eye"></i>
                                            </button>
                                        </div>
                                        @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="admin-form-field">
                                        <label class="admin-form-label">تأكيد كلمة المرور <span class="required">*</span></label>
                                        <div class="admin-password-wrap">
                                            <input type="password" id="password_confirmation"
                                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                                name="password_confirmation" placeholder="••••••••" required>
                                            <button type="button" class="admin-password-toggle" data-toggle-password="password_confirmation" aria-label="إظهار كلمة المرور">
                                                <i class="las la-eye"></i>
                                            </button>
                                        </div>
                                        @error('password_confirmation')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="admin-form-section">
                            <div class="admin-form-section-head">
                                <div class="admin-section-icon admin-section-icon-amber">
                                    <i class="las la-cog"></i>
                                </div>
                                <div>
                                    <h3>إعدادات الحساب</h3>
                                    <p>الصورة وحالة التفعيل</p>
                                </div>
                            </div>

                            <div class="row g-3 align-items-start">
                                <div class="col-md-6">
                                    <div class="admin-form-field">
                                        <label class="admin-form-label">صورة المستخدم</label>
                                        <div class="admin-photo-zone">
                                            <div class="admin-photo-preview-wrap">
                                                <img id="photo-preview" src="{{ asset('assets/images/faces/default-avatar.png') }}"
                                                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 96 96%22%3E%3Ccircle cx=%2248%22 cy=%2248%22 r=%2248%22 fill=%22%234f46e5%22/%3E%3Ctext x=%2248%22 y=%2262%22 font-family=%22Arial%22 font-size=%2236%22 font-weight=%22bold%22 fill=%22white%22 text-anchor=%22middle%22%3EU%3C/text%3E%3C/svg%3E'"
                                                     alt="صورة المستخدم" class="admin-photo-preview">
                                                <input type="file" name="photo" id="photo-input" accept="image/*" data-photo-preview="photo-preview">
                                            </div>
                                            <div class="admin-photo-actions">
                                                <label for="photo-input" class="admin-photo-btn mb-0">
                                                    <i class="las la-camera"></i>
                                                    اختر صورة
                                                </label>
                                                <p class="admin-photo-hint">JPG أو PNG — بحد أقصى 2 ميجابايت</p>
                                            </div>
                                        </div>
                                        @error('photo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="admin-form-switch-card">
                                        <div class="switch-info">
                                            <strong>تفعيل الدخول للنظام</strong>
                                            <span>السماح بتسجيل الدخول فور الإنشاء</span>
                                        </div>
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                                            {{ old('is_active', true) ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="admin-form-section">
                            <div class="admin-form-section-head">
                                <div class="admin-section-icon admin-section-icon-purple">
                                    <i class="las la-user-shield"></i>
                                </div>
                                <div>
                                    <h3>الأدوار والصلاحيات</h3>
                                    <p>حدد دوراً واحداً أو أكثر للمستخدم</p>
                                </div>
                            </div>

                            <div class="admin-role-grid">
                                @foreach ($roles as $role)
                                    <label class="admin-role-chip">
                                        <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                            {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}>
                                        <span>
                                            <i class="las la-user-tag"></i>
                                            {{ $role->name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            @error('roles')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                        </div>

                    </div>

                    <div class="admin-form-footer">
                        <a href="{{ route('users.index') }}" class="admin-btn admin-btn-secondary">
                            <i class="las la-times"></i>
                            إلغاء
                        </a>
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="las la-save"></i>
                            حفظ بيانات المستخدم
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@stop

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-photo-preview]').forEach(function (input) {
                input.addEventListener('change', function () {
                    if (!this.files || !this.files[0]) return;
                    var preview = document.getElementById(this.dataset.photoPreview);
                    if (!preview) return;
                    var reader = new FileReader();
                    reader.onload = function (e) { preview.src = e.target.result; };
                    reader.readAsDataURL(this.files[0]);
                });
            });

            document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var input = document.getElementById(this.dataset.togglePassword);
                    if (!input) return;
                    var icon = this.querySelector('i');
                    var showing = input.type === 'text';
                    input.type = showing ? 'password' : 'text';
                    if (icon) {
                        icon.classList.toggle('la-eye', showing);
                        icon.classList.toggle('la-eye-slash', !showing);
                    }
                });
            });
        });
    </script>
@stop
