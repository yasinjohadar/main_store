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
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">الطلبات</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card admin-table-card">
                        <div class="admin-table-toolbar">
                            <form action="{{ route('admin.orders.index') }}" method="GET"
                                class="admin-table-filter-form d-flex align-items-center gap-2 flex-wrap ms-auto"
                                data-admin-table-target="#admin-table-orders"
                                data-admin-table-autosubmit>
                                <input type="text" name="order_number" class="form-control"
                                    style="min-width: 180px; max-width: 220px;"
                                    placeholder="رقم الطلب" value="{{ request('order_number') }}">

                                <select name="status" class="form-select" style="width: auto;">
                                    <option value="">كل الحالات</option>
                                    @foreach($statuses as $s)
                                        <option value="{{ $s->slug }}" {{ request('status') == $s->slug ? 'selected' : '' }}>{{ $s->name }}</option>
                                    @endforeach
                                </select>

                                <input type="date" name="from" class="form-control" style="width: auto;" value="{{ request('from') }}" title="من تاريخ">
                                <input type="date" name="to" class="form-control" style="width: auto;" value="{{ request('to') }}" title="إلى تاريخ">

                                <select name="per_page" class="form-select" style="width: auto;" title="عدد العناصر في الصفحة">
                                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 / صفحة</option>
                                    <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 / صفحة</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 / صفحة</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / صفحة</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 / صفحة</option>
                                </select>

                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="las la-search"></i>
                                </button>
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-light btn-sm" data-admin-table-reset>مسح</a>
                            </form>
                        </div>

                        <div class="card-body p-0" id="admin-table-orders" data-admin-table>
                            @include('admin.pages.orders.partials.table-body')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $orderStatusesJson = $statuses->map(function ($s) {
            return [
                'id' => $s->id,
                'name' => $s->name,
                'color' => $s->color,
                'slug' => $s->slug,
            ];
        })->values();
    @endphp

    <script type="application/json" id="admin-order-statuses-json">
        @json($orderStatusesJson)
    </script>
@stop

@section('script')
<script src="{{ asset('assets/js/admin-order-inline-status.js') }}"></script>
@stop
