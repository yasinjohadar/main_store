@extends('admin.layouts.master')

@section('page-title') Webhooks @stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between my-4">
            <h5 class="page-title mb-0">Outbound Webhooks</h5>
            <a href="{{ route('admin.webhooks.create') }}" class="btn btn-primary">إضافة</a>
        </div>
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <div class="card">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead><tr><th>الاسم</th><th>URL</th><th>الأحداث</th><th>التسليمات</th><th></th></tr></thead>
                    <tbody>
                        @forelse($webhooks as $webhook)
                            <tr>
                                <td>{{ $webhook->name }}</td>
                                <td><code>{{ Str::limit($webhook->url, 40) }}</code></td>
                                <td>{{ implode(', ', $webhook->events ?? []) }}</td>
                                <td>{{ $webhook->deliveries_count }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.webhooks.edit', $webhook) }}" class="btn btn-sm btn-outline-primary">تعديل</a>
                                    <x-admin.delete-trigger
                                        :action="route('admin.webhooks.destroy', $webhook)"
                                        title="حذف Webhook"
                                        :item="$webhook->name"
                                        message="هل تريد حذف هذا الـ Webhook؟"
                                        button-class="btn-sm btn-outline-danger"
                                        label="حذف"
                                    />
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">لا توجد webhooks.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $webhooks->links() }}
        </div>
    </div>
</div>
@stop
