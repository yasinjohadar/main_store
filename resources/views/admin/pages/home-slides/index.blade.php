@extends('admin.layouts.master')

@section('page-title')
    شرائح السلاider
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/admin-home-slides.css') }}">
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
                    <h5 class="page-title fs-21 mb-1">سلاider الرئيسية</h5>
                    <p class="text-muted mb-0 small">إدارة شرائح الصفحة الرئيسية للمتجر</p>
                </div>
                <a href="{{ route('admin.home-slides.create') }}" class="btn btn-primary btn-sm">
                    <i class="las la-plus me-1"></i> إضافة شريحة
                </a>
            </div>

            <div class="card admin-table-card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table admin-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 90px;">معاينة</th>
                                    <th>العنوان</th>
                                    <th style="width: 80px;">الترتيب</th>
                                    <th style="width: 100px;">الحالة</th>
                                    <th style="width: 140px;">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($slides as $slide)
                                    <tr>
                                        <td>
                                            @if($slide->background_image_url)
                                                <img src="{{ $slide->background_image_url }}" alt="" class="rounded" style="width: 72px; height: 40px; object-fit: cover;">
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $slide->title }}</div>
                                            @if($slide->subtitle)
                                                <small class="text-muted">{{ $slide->subtitle }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $slide->sort_order }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.home-slides.toggle-active', $slide) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm {{ $slide->is_active ? 'btn-success' : 'btn-outline-secondary' }}">
                                                    {{ $slide->is_active ? 'نشط' : 'موقوف' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.home-slides.edit', $slide) }}" class="btn btn-sm btn-outline-primary">تعديل</a>
                                                <form method="POST" action="{{ route('admin.home-slides.destroy', $slide) }}" onsubmit="return confirm('حذف هذه الشريحة؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            لا توجد شرائح بعد.
                                            <a href="{{ route('admin.home-slides.create') }}">أضف أول شريحة</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
