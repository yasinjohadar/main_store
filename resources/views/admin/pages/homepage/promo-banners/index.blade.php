@extends('admin.layouts.master')

@section('page-title')
    البانرات الترويجية
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
                    <h5 class="page-title fs-21 mb-1">البانرات الترويجية</h5>
                    <p class="text-muted mb-0 small">بانرات التصنيفات في الصفحة الرئيسية</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.homepage.index') }}" class="btn btn-light btn-sm">مدير الرئيسية</a>
                    <a href="{{ route('admin.homepage.promo-banners.create') }}" class="btn btn-primary btn-sm">
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
                                    <th style="width: 100px;">معاينة</th>
                                    <th>العنوان</th>
                                    <th>الترتيب</th>
                                    <th>الحالة</th>
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($banners as $banner)
                                    <tr>
                                        <td>
                                            @if($banner->image_url)
                                                <img src="{{ $banner->image_url }}" alt="" class="admin-homepage-thumb">
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $banner->title }}</div>
                                            <small class="text-muted">{{ $banner->subtitle }}</small>
                                        </td>
                                        <td>{{ $banner->sort_order }}</td>
                                        <td>{{ $banner->is_active ? 'نشط' : 'موقوف' }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.homepage.promo-banners.edit', $banner) }}" class="btn btn-sm btn-outline-primary">تعديل</a>
                                                <form method="POST" action="{{ route('admin.homepage.promo-banners.destroy', $banner) }}" onsubmit="return confirm('حذف هذا البانر؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">لا توجد بانرات.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
