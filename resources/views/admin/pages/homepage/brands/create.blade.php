@extends('admin.layouts.master')

@section('page-title')
    إضافة ماركة
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/admin-homepage.css') }}">
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <h5 class="page-title fs-21 mb-0">إضافة ماركة</h5>
                <a href="{{ route('admin.homepage.brands.index') }}" class="btn btn-light btn-sm">العودة</a>
            </div>

            <form method="POST" action="{{ route('admin.homepage.brands.store') }}" enctype="multipart/form-data">
                @csrf
                @include('admin.pages.homepage.brands.partials.form', ['brand' => null])
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.homepage.brands.index') }}" class="btn btn-light">إلغاء</a>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
@stop
