@extends('admin.layouts.master')

@section('page-title')
    شريط الميزات
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
                    <h5 class="page-title fs-21 mb-1">شريط الميزات</h5>
                    <p class="text-muted mb-0 small">أيقونات الميزات أعلى الصفحة الرئيسية</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.homepage.index') }}" class="btn btn-light btn-sm">مدير الرئيسية</a>
                    <a href="{{ route('admin.homepage.features.create') }}" class="btn btn-primary btn-sm">
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
                                    <th>الأيقونة</th>
                                    <th>العنوان</th>
                                    <th>الترتيب</th>
                                    <th>الحالة</th>
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($features as $feature)
                                    <tr>
                                        <td><i class="{{ $feature->icon }} fs-4"></i></td>
                                        <td>
                                            <div class="fw-semibold">{{ $feature->title }}</div>
                                            <small class="text-muted">{{ $feature->description }}</small>
                                        </td>
                                        <td>{{ $feature->sort_order }}</td>
                                        <td>{{ $feature->is_active ? 'نشط' : 'موقوف' }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.homepage.features.edit', $feature) }}" class="btn btn-sm btn-outline-primary">تعديل</a>
                                                <form method="POST" action="{{ route('admin.homepage.features.destroy', $feature) }}" onsubmit="return confirm('حذف هذه الميزة؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">لا توجد ميزات.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
