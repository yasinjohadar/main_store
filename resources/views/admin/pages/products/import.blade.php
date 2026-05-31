@extends('admin.layouts.master')

@section('page-title') استيراد المنتجات @stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between my-4">
            <h5 class="page-title mb-0">استيراد / تصدير المنتجات</h5>
            <a href="{{ route('admin.products.export') }}" class="btn btn-outline-primary">تصدير CSV</a>
        </div>
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.products.import.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" accept=".csv" class="form-control mb-3" required>
                    <button type="submit" class="btn btn-primary">رفع واستيراد</button>
                </form>
                @if(session('import_errors'))
                    <ul class="mt-3 small text-danger">
                        @foreach(session('import_errors') as $err)<li>{{ $err }}</li>@endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@stop
