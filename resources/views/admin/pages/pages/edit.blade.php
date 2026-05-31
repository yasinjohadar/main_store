@extends('admin.layouts.master')

@section('page-title')
    SEO — {{ $page->title }}
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="my-4 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">SEO — {{ $page->title }}</h4>
                <p class="text-muted mb-0"><code>{{ $page->route_name }}</code></p>
            </div>
            <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.pages.update', $page) }}">
            @csrf
            @method('PUT')

            <div class="card mb-3">
                <div class="card-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">عنوان الصفحة</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $page->title) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">مقتطف</label>
                        <input type="text" name="excerpt" class="form-control" value="{{ old('excerpt', $page->excerpt) }}">
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                                {{ old('is_active', $page->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">الصفحة نشطة</label>
                        </div>
                    </div>
                </div>
            </div>

            <x-admin.seo-panel entity-type="page" :values="$page->only([
                'meta_title','meta_description','meta_keywords','canonical_url',
                'focus_keyword','og_title','og_description','og_image','robots_meta','schema_type'
            ])" />

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">حفظ</button>
            </div>
        </form>
    </div>
</div>
@endsection
