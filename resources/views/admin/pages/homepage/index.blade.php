@extends('admin.layouts.master')

@section('page-title')
    مدير الصفحة الرئيسية
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
                    <h5 class="page-title fs-21 mb-1">مدير الصفحة الرئيسية</h5>
                    <p class="text-muted mb-0 small">ترتيب الأقسام، إظهار/إخفاء، والانتقال لتحرير المحتوى</p>
                </div>
            </div>

            <form id="homepage-reorder-form" method="POST" action="{{ route('admin.homepage.sections.reorder') }}" class="mb-4">
                @csrf
            </form>

            <div class="card admin-table-card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">أقسام الصفحة الرئيسية</span>
                    <button type="submit" form="homepage-reorder-form" class="btn btn-sm btn-primary">حفظ الترتيب</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table admin-table mb-0">
                            <thead>
                                <tr>
                                    <th>القسم</th>
                                    <th style="width: 100px;">مرئي</th>
                                    <th style="width: 120px;">الترتيب</th>
                                    <th style="width: 160px;">المحتوى</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sections as $section)
                                    @php
                                        $meta = $registry[$section->key] ?? [];
                                        $editRoute = $homePage->editRouteName($section);
                                        $editParams = $homePage->editRouteParams($section);
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $meta['label'] ?? $section->key }}</div>
                                            <small class="text-muted">{{ $section->key }}</small>
                                        </td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.homepage.sections.toggle-visible', $section) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm {{ $section->is_visible ? 'btn-success' : 'btn-outline-secondary' }}">
                                                    {{ $section->is_visible ? 'مرئي' : 'مخفي' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <input type="number" form="homepage-reorder-form" name="sort_order[{{ $section->key }}]"
                                                value="{{ $section->sort_order }}" min="0"
                                                class="form-control form-control-sm" style="width: 80px;">
                                        </td>
                                        <td>
                                            @if($editRoute)
                                                <a href="{{ route($editRoute, $editParams) }}" class="btn btn-sm btn-outline-primary">
                                                    تعديل المحتوى
                                                </a>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
