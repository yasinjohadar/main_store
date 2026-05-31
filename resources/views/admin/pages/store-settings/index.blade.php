@extends('admin.layouts.master')

@section('page-title')
    إعدادات المتجر
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="my-4">
                <h5 class="page-title fs-21 mb-1">إعدادات المتجر</h5>
                <p class="text-muted small mb-0">الدفع كضيف، الضريبة، العملة، المخزون، والولاء — تُطبَّق مباشرة على <code>config/store.php</code> دون تعديل .env.</p>
            </div>

            @include('admin.partials.settings-form', [
                'schema' => $schema,
                'settings' => $settings,
                'sectionLabels' => $sectionLabels,
                'formAction' => route('admin.store-settings.update'),
                'orderStatusField' => \App\Services\StoreSettingsService::KEY_DEFAULT_ORDER_STATUS,
                'orderStatuses' => $orderStatuses,
            ])
        </div>
    </div>
@stop
