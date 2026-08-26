@extends('admin.layouts.master')

@section('page-title')
    تعديل الدور
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/admin-role-permissions.css') }}">
@stop

@section('content')
    @if (\Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ \Session::get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (\Session::has('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ \Session::get('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="las la-user-shield"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>تعديل الدور</h1>
                        <p>{{ $role->name }}</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('roles.index') }}" class="admin-btn admin-btn-light">
                        <i class="las la-arrow-right"></i>
                        العودة للأدوار
                    </a>
                </div>
            </div>

            <div class="admin-page-card">
                <form method="POST" action="{{ route('roles.update', $role) }}" class="admin-form">
                    @csrf
                    @method('PUT')

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
                                               name="name" value="{{ old('name', $role->name) }}" required>
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
                                'accordionId' => 'editRolePermissions',
                            ])
                        </div>
                    </div>

                    <input type="hidden" name="id" value="{{ $role->id }}">

                    <div class="admin-form-footer">
                        <a href="{{ route('roles.index') }}" class="admin-btn admin-btn-secondary">
                            <i class="las la-times"></i>
                            إلغاء
                        </a>
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="las la-save"></i>
                            حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@stop

@section('script')
<script src="{{ asset('assets/js/admin-role-permissions.js') }}"></script>
@stop
