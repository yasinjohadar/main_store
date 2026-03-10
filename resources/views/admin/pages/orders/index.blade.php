@extends('admin.layouts.master')

@section('page-title')
    الطلبات
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <h5 class="page-title fs-21 mb-1">الطلبات</h5>
            </div>

            <div class="card">
                <div class="card-header">
                    <form action="{{ route('admin.orders.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                        <input type="text" name="order_number" class="form-control" style="width: 180px;" placeholder="رقم الطلب" value="{{ request('order_number') }}">
                        <select name="status" class="form-select" style="width: 150px;">
                            <option value="">كل الحالات</option>
                            @foreach($statuses as $s)
                                <option value="{{ $s->slug }}" {{ request('status') == $s->slug ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                        <input type="date" name="from" class="form-control" style="width: 150px;" value="{{ request('from') }}" placeholder="من">
                        <input type="date" name="to" class="form-control" style="width: 150px;" value="{{ request('to') }}" placeholder="إلى">
                        <button type="submit" class="btn btn-secondary">بحث</button>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-danger">مسح</a>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>رقم الطلب</th>
                                    <th>العميل</th>
                                    <th>الحالة</th>
                                    <th>المجموع</th>
                                    <th>التاريخ</th>
                                    <th>عمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $order)
                                    <tr>
                                        <td><strong>{{ $order->order_number }}</strong></td>
                                        <td>{{ $order->user->name ?? 'ضيف' }}</td>
                                        <td><span class="badge" style="background-color: {{ $order->status->color ?? '#6c757d' }}">{{ $order->status->name }}</span></td>
                                        <td>{{ $currencyService->format((float) $order->total) }}</td>
                                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-primary">عرض</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">لا توجد طلبات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $orders->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@stop
