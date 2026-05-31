@extends('admin.layouts.master')

@section('page-title')
    التكاملات والدفع
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
                <h5 class="page-title fs-21 mb-1">التكاملات والدفع</h5>
                <p class="text-muted small mb-0">PayPal، Stripe، OAuth، وIndexNow — تُطبَّق على <code>config/services.php</code> من لوحة الإدارة.</p>
            </div>

            <div class="alert alert-info small">
                <strong>روابط Webhooks:</strong>
                Stripe: <code>{{ $webhookUrls['stripe'] }}</code> —
                PayPal: <code>{{ $webhookUrls['paypal'] }}</code>
            </div>

            @include('admin.partials.settings-form', [
                'schema' => $schema,
                'settings' => $settings,
                'sectionLabels' => $sectionLabels,
                'formAction' => route('admin.integration-settings.update'),
                'secretKeys' => $secretKeys,
                'storedSecrets' => $storedSecrets,
            ])
        </div>
    </div>
@stop
