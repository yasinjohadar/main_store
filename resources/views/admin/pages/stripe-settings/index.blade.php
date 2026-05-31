@extends('admin.layouts.master')

@section('page-title')
    إعدادات Stripe
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

    @php
        use App\Services\IntegrationSettingsService as ISS;
        $keyPublishable = ISS::KEY_STRIPE_KEY;
        $keySecret = ISS::KEY_STRIPE_SECRET;
        $keyWebhook = ISS::KEY_STRIPE_WEBHOOK_SECRET;
    @endphp

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                    <h4 class="mb-0">إعدادات Stripe</h4>
                    <p class="mb-0 text-muted">مفاتيح API، Webhook، واختبار الاتصال</p>
                </div>
                <div class="ms-auto d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-outline-primary btn-sm">وسائل الدفع</a>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm">سجل المدفوعات</a>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card custom-card h-100">
                        <div class="card-body">
                            <span class="text-muted small d-block">حزمة Stripe PHP</span>
                            <span class="admin-table__badge {{ $status['sdk_installed'] ? 'admin-table__badge--success' : 'admin-table__badge--danger' }}">
                                {{ $status['sdk_installed'] ? 'مثبتة' : 'غير مثبتة' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card custom-card h-100">
                        <div class="card-body">
                            <span class="text-muted small d-block">المفاتيح</span>
                            <span class="admin-table__badge {{ $status['secret_configured'] ? 'admin-table__badge--success' : 'admin-table__badge--warning' }}">
                                {{ $status['secret_configured'] ? 'مُعدّة' : 'ناقصة' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card custom-card h-100">
                        <div class="card-body">
                            <span class="text-muted small d-block">Webhook Secret</span>
                            <span class="admin-table__badge {{ $status['webhook_configured'] ? 'admin-table__badge--success' : 'admin-table__badge--secondary' }}">
                                {{ $status['webhook_configured'] ? 'مُعد' : 'اختياري' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card custom-card h-100">
                        <div class="card-body">
                            <span class="text-muted small d-block">الوضع</span>
                            <span class="admin-table__badge admin-table__badge--info">
                                {{ $status['test_mode'] ? 'تجريبي (test)' : 'مباشر (live)' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-7">
                    <form method="POST" action="{{ route('admin.stripe-settings.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="card custom-card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="card-title mb-0">مفاتيح API</div>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="stripe-test-connection">اختبار الاتصال</button>
                            </div>
                            <div class="card-body">
                                <div id="stripe-test-result" class="alert d-none mb-3" role="alert"></div>
                                <div class="mb-3">
                                    <label class="form-label">Publishable Key</label>
                                    <input type="text" name="{{ $keyPublishable }}" class="form-control font-monospace" dir="ltr"
                                        value="{{ old($keyPublishable, $settings[$keyPublishable] ?? '') }}"
                                        placeholder="pk_test_... أو pk_live_...">
                                    <div class="form-text">يظهر في واجهة المتجر عند الحاجة؛ يُخزَّن في إعدادات التكامل.</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Secret Key</label>
                                    <input type="password" name="{{ $keySecret }}" class="form-control font-monospace" dir="ltr"
                                        placeholder="{{ $storedSecrets->contains($keySecret) ? '•••••••• (محفوظ — اتركه فارغاً للإبقاء)' : 'sk_test_... أو sk_live_...' }}"
                                        autocomplete="new-password">
                                </div>
                                <div class="mb-0">
                                    <label class="form-label">Webhook Signing Secret</label>
                                    <input type="password" name="{{ $keyWebhook }}" class="form-control font-monospace" dir="ltr"
                                        placeholder="{{ $storedSecrets->contains($keyWebhook) ? '•••••••• (محفوظ)' : 'whsec_...' }}"
                                        autocomplete="new-password">
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">حفظ الإعدادات</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-lg-5">
                    <div class="card custom-card mb-4">
                        <div class="card-header"><div class="card-title mb-0">رابط Webhook</div></div>
                        <div class="card-body">
                            <p class="small text-muted">أضف هذا الرابط في Stripe Dashboard → Developers → Webhooks:</p>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control font-monospace" dir="ltr" readonly value="{{ $webhookUrl }}" id="stripe-webhook-url">
                                <button type="button" class="btn btn-outline-secondary" data-copy-target="stripe-webhook-url">نسخ</button>
                            </div>
                            <p class="small fw-semibold mb-2">الأحداث المطلوبة:</p>
                            <ul class="small mb-0">
                                @foreach($requiredEvents as $event)
                                    <li><code>{{ $event }}</code></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="card custom-card">
                        <div class="card-header"><div class="card-title mb-0">دليل الإعداد السريع</div></div>
                        <div class="card-body">
                            <ol class="small mb-0 ps-3">
                                <li class="mb-2">أنشئ حساباً على <a href="https://dashboard.stripe.com/register" target="_blank" rel="noopener">stripe.com</a>.</li>
                                <li class="mb-2">من Developers → API keys انسخ المفاتيح وألصقها هنا.</li>
                                <li class="mb-2">أنشئ Webhook بالرابط أعلاه وانسخ <code>whsec_...</code>.</li>
                                <li class="mb-2">فعّل وسيلة دفع «Stripe» من <a href="{{ route('admin.payment-methods.index') }}">وسائل الدفع</a>.</li>
                                <li>راجع <code>docs/stripe-setup.md</code> للتفاصيل وبطاقات الاختبار.</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-copy-target]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var el = document.getElementById(btn.getAttribute('data-copy-target'));
            if (el) { el.select(); document.execCommand('copy'); }
        });
    });
    var testBtn = document.getElementById('stripe-test-connection');
    var resultEl = document.getElementById('stripe-test-result');
    if (testBtn && resultEl) {
        testBtn.addEventListener('click', function() {
            testBtn.disabled = true;
            resultEl.classList.remove('d-none', 'alert-success', 'alert-danger');
            resultEl.textContent = 'جاري الاختبار...';
            fetch('{{ route('admin.stripe-settings.test') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            })
            .then(function(r) { return r.json().then(function(d) { return { ok: r.ok, data: d }; }); })
            .then(function(res) {
                resultEl.classList.add(res.ok ? 'alert-success' : 'alert-danger');
                resultEl.textContent = res.data.message || (res.ok ? 'نجح' : 'فشل');
            })
            .catch(function() {
                resultEl.classList.add('alert-danger');
                resultEl.textContent = 'خطأ في الاتصال بالخادم';
            })
            .finally(function() { testBtn.disabled = false; });
        });
    }
});
</script>
@endsection
