@extends('admin.layouts.master')

@section('page-title')
    إنشاء دور جديد
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/admin-role-permissions.css') }}">
@stop

@section('content')
    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @endif

    @if (\Session::has('error'))
        <div class="alert alert-danger">
            <ul>
                <li>{!! \Session::get('error') !!}</li>
            </ul>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Start::app-content -->
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="las la-user-shield"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>إنشاء دور جديد</h1>
                        <p>تعريف دور جديد وربطه بالصلاحيات المناسبة</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('roles.index') }}" class="admin-btn admin-btn-light">
                        <i class="las la-arrow-right"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="admin-page-card">
                <form method="POST" action="{{ route('roles.store') }}" class="admin-form">
                    @csrf

                    <div class="admin-form-body">
                        <div class="admin-form-section">
                            <div class="admin-form-section-head">
                                <div class="admin-section-icon admin-section-icon-blue">
                                    <i class="las la-user-shield"></i>
                                </div>
                                <div>
                                    <h3>بيانات الدور</h3>
                                    <p>اسم الدور كما يظهر في النظام</p>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="admin-form-field">
                                        <label class="admin-form-label">اسم الدور <span class="required">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                               name="name" value="{{ old('name') }}" placeholder="مثال: مشرف عام" required>
                                        @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="admin-form-section">
                            <div class="admin-form-section-head">
                                <div class="admin-section-icon admin-section-icon-purple">
                                    <i class="las la-key"></i>
                                </div>
                                <div>
                                    <h3>الصلاحيات</h3>
                                    <p>حدد الصلاحيات الممنوحة لهذا الدور</p>
                                </div>
                            </div>

                            @include('admin.pages.roles.partials.permissions-accordion', [
                                'accordionId' => 'createRolePermissions',
                            ])
                        </div>
                    </div>

                    <div class="admin-form-footer">
                        <a href="{{ route('roles.index') }}" class="admin-btn admin-btn-secondary">
                            <i class="las la-times"></i>
                            إلغاء
                        </a>
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="las la-save"></i>
                            حفظ الدور
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <!-- End::app-content -->
@stop

@section('script')
<script src="{{ asset('assets/js/admin-role-permissions.js') }}"></script>
@stop
