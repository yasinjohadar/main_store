@extends('admin.layouts.master')

@section('page-title')
    ماركات الصفحة الرئيسية
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/admin-homepage.css') }}">
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                    <h5 class="page-title fs-21 mb-1">ماركات / عملاء</h5>
                    <p class="text-muted mb-0 small">شعارات العملاء في الصفحة الرئيسية</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.homepage.sections.edit', 'brands') }}" class="btn btn-light btn-sm">عناوين القسم</a>
                    <a href="{{ route('admin.homepage.index') }}" class="btn btn-light btn-sm">مدير الرئيسية</a>
                    <a href="{{ route('admin.homepage.brands.create') }}" class="btn btn-primary btn-sm">
                        <i class="las la-plus me-1"></i> إضافة
                    </a>
                </div>
            </div>

            <div class="card admin-table-card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table admin-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 100px;">الشعار</th>
                                    <th>الاسم</th>
                                    <th>الترتيب</th>
                                    <th>الحالة</th>
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($brands as $brand)
                                    <tr>
                                        <td>
                                            @if($brand->logo_url)
                                                <img src="{{ $brand->logo_url }}" alt="" class="admin-homepage-thumb admin-homepage-thumb--logo">
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $brand->name }}</td>
                                        <td>{{ $brand->sort_order }}</td>
                                        <td>{{ $brand->is_active ? 'نشط' : 'موقوف' }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.homepage.brands.edit', $brand) }}" class="btn btn-sm btn-outline-primary">تعديل</a>
                                                <form method="POST" action="{{ route('admin.homepage.brands.destroy', $brand) }}" onsubmit="return confirm('حذف هذا الشعار؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">لا توجد ماركات.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
