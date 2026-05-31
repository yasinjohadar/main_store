@extends('admin.layouts.master')

@section('page-title') {{ $webhook->exists ? 'تعديل Webhook' : 'Webhook جديد' }} @stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="card my-4">
            <div class="card-body">
                <form method="POST" action="{{ $webhook->exists ? route('admin.webhooks.update', $webhook) : route('admin.webhooks.store') }}">
                    @csrf
                    @if($webhook->exists) @method('PUT') @endif
                    <div class="mb-3">
                        <label class="form-label">الاسم</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $webhook->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL</label>
                        <input type="url" name="url" class="form-control" value="{{ old('url', $webhook->url) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الأحداث</label>
                        @foreach($events as $key => $label)
                            <div class="form-check">
                                <input type="checkbox" name="events[]" value="{{ $key }}" class="form-check-input"
                                    @checked(in_array($key, old('events', $webhook->events ?? [])))>
                                <label class="form-check-label">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $webhook->is_active ?? true))>
                        <label class="form-check-label">مفعّل</label>
                    </div>
                    @if($webhook->secret)
                        <p class="small text-muted">Secret: <code>{{ $webhook->secret }}</code></p>
                    @endif
                    <button type="submit" class="btn btn-primary">حفظ</button>
                    <a href="{{ route('admin.webhooks.index') }}" class="btn btn-secondary">إلغاء</a>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
