@extends('admin.layouts.master')

@section('page-title')
    مناطق وطرق الشحن
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

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">مناطق وطرق الشحن</h5>
                    <p class="text-muted mb-0 small">اضبط مناطق الشحن (الدول / المدن) وطرق الشحن المتاحة لكل منطقة.</p>
                </div>
                <a href="{{ route('admin.shipping.zones.create') }}" class="btn btn-primary btn-sm">إضافة منطقة شحن</a>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th>اسم المنطقة</th>
                                            <th>الحالة</th>
                                            <th>عدد المواقع</th>
                                            <th>طرق الشحن</th>
                                            <th style="width: 80px;">الترتيب</th>
                                            <th style="width: 160px;">العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($zones as $zone)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $zone->name }}</td>
                                                <td>
                                                    @if($zone->is_active)
                                                        <span class="badge bg-success">مفعّل</span>
                                                    @else
                                                        <span class="badge bg-secondary">معطّل</span>
                                                    @endif
                                                </td>
                                                <td>{{ $zone->locations_count }}</td>
                                                <td>{{ $zone->methods_count }}</td>
                                                <td>{{ $zone->order }}</td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ route('admin.shipping.zones.edit', $zone) }}" class="btn btn-sm btn-primary">تعديل</a>
                                                        <form action="{{ route('admin.shipping.zones.destroy', $zone) }}" method="POST" onsubmit="return confirm('حذف هذه المنطقة مع جميع طرق الشحن التابعة لها؟');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">
                                                    لا توجد مناطق شحن بعد.
                                                    <a href="{{ route('admin.shipping.zones.create') }}">إضافة أول منطقة شحن</a>
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
        </div>
    </div>
@stop

