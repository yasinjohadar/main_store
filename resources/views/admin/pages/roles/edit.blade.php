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
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                    <h5 class="page-title fs-21 mb-1">تعديل الدور</h5>
                    <p class="text-muted mb-0">{{ $role->name }}</p>
                </div>
                <a href="{{ route('roles.index') }}" class="btn btn-light btn-sm">
                    <i class="las la-arrow-right me-1"></i> العودة للأدوار
                </a>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card admin-detail-card">
                        <div class="admin-detail-card__header">
                            <h6 class="mb-0">بيانات الدور والصلاحيات</h6>
                        </div>
                        <div class="admin-detail-card__body">
                            <form method="POST" action="{{ route('roles.update', $role) }}">
                                @csrf
                                @method('PUT')

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">اسم الدور</label>
                                        <input type="text" class="form-control" name="name" value="{{ old('name', $role->name) }}" required>
                                    </div>
                                </div>

                                @include('admin.pages.roles.partials.permissions-accordion', [
                                    'accordionId' => 'editRolePermissions',
                                ])

                                <input type="hidden" name="id" value="{{ $role->id }}">

                                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                    <a href="{{ route('roles.index') }}" class="btn btn-light">إلغاء</a>
                                    <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
<script src="{{ asset('assets/js/admin-role-permissions.js') }}"></script>
@stop
