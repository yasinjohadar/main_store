@extends('admin.layouts.master')

@section('page-title')
    SEO الصفحات الثابتة
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="my-4">
            <h4 class="mb-1">SEO الصفحات الثابتة</h4>
            <p class="text-muted mb-0">about، contact، faq، privacy، terms</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>الصفحة</th>
                            <th>المسار</th>
                            <th>Schema</th>
                            <th>الحالة</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pages as $page)
                        <tr>
                            <td>{{ $page->title }}</td>
                            <td><code>{{ $page->route_name }}</code></td>
                            <td>{{ $page->schema_type ?? 'WebPage' }}</td>
                            <td>
                                @if($page->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-secondary">معطّل</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-outline-primary">تحرير SEO</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
