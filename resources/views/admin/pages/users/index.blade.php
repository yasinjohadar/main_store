@extends('admin.layouts.master')

@section('page-title')
    قائمة المستخدمون
@stop



@section('css')
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
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">كافة المستخدمين</h5>

                </div>


            </div>
            <!-- Page Header Close -->



            <!-- Start::row-1 -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card admin-table-card">
                        <div class="admin-table-toolbar">
                            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                                <i class="las la-user-plus me-1"></i> إنشاء مستخدم جديد
                            </a>

                            <form action="{{ route('users.index') }}" method="GET"
                                class="admin-table-filter-form d-flex align-items-center gap-2 flex-wrap ms-auto"
                                data-admin-table-target="#admin-table-users"
                                data-admin-table-autosubmit>
                                <input type="text" name="query" class="form-control" style="min-width: 220px; max-width: 280px;"
                                    placeholder="بحث بالاسم أو الإيميل أو الهاتف" value="{{ request('query') }}">

                                <select name="is_active" class="form-select" style="width: auto;">
                                    <option value="">كل حالات التفعيل</option>
                                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>مفعّل</option>
                                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>موقوف</option>
                                </select>

                                <select name="per_page" class="form-select" style="width: auto;" title="عدد العناصر في الصفحة">
                                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 / صفحة</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 / صفحة</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / صفحة</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 / صفحة</option>
                                </select>

                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="las la-search"></i>
                                </button>
                                <a href="{{ route('users.index') }}" class="btn btn-light btn-sm" data-admin-table-reset>مسح</a>
                            </form>
                        </div>

                        <div class="card-body p-0" id="admin-table-users" data-admin-table>
                            @include('admin.pages.users.partials.table-body')
                        </div><!-- end card-body -->
                    </div><!-- end card -->
                </div>
            </div>
            <!--End::row-1 -->


        </div>
    </div>
    <!-- End::app-content -->

@stop

@section('script')
<script src="{{ asset('assets/js/admin-user-toggle.js') }}"></script>
@stop
