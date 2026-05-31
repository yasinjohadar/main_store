@extends('admin.layouts.master')

@section('page-title')
    التسويق والتحليلات
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center my-4">
            <div>
                <h4 class="mb-0">لوحة التسويق والـ SEO</h4>
                <p class="text-muted mb-0">GA4 + Meta Pixel + تحليل داخلي</p>
            </div>
            <form method="get" class="d-flex gap-2">
                <input type="date" name="start" class="form-control form-control-sm" value="{{ $filters['start'] }}">
                <input type="date" name="end" class="form-control form-control-sm" value="{{ $filters['end'] }}">
                <button class="btn btn-sm btn-primary">تطبيق</button>
            </form>
        </div>

        <div class="row g-3 mb-4">
            @foreach([
                ['label' => 'مشاهدات الصفحات', 'value' => number_format($kpis['pageViews'])],
                ['label' => 'مشاهدات المنتجات', 'value' => number_format($kpis['productViews'])],
                ['label' => 'مشاهدات المدونة', 'value' => number_format($kpis['blogViews'])],
                ['label' => 'نقرات CTA', 'value' => number_format($kpis['ctaClicks'])],
                ['label' => 'إضافة للسلة (tracked)', 'value' => number_format($kpis['addToCart'])],
                ['label' => 'الطلبات', 'value' => number_format($kpis['orders'])],
                ['label' => 'الإيرادات', 'value' => number_format($kpis['revenue'], 2) . ' ر.س'],
            ] as $kpi)
            <div class="col-md-3 col-sm-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">{{ $kpi['label'] }}</div>
                        <div class="h4 mb-0">{{ $kpi['value'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">أكثر المنتجات مشاهدة</h6></div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            @forelse($topProducts as $row)
                            <tr><td>{{ $row['name'] }}</td><td class="text-end">{{ $row['views'] }}</td></tr>
                            @empty
                            <tr><td colspan="2" class="text-muted p-3">لا بيانات بعد</td></tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Content ROI — المدونة</h6></div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            @forelse($topPosts as $row)
                            <tr>
                                <td>{{ Str::limit($row['title'], 40) }}</td>
                                <td class="text-end">{{ $row['views'] }} views</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-muted p-3">لا بيانات بعد</td></tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-warning">
                    <div class="card-header"><h6 class="mb-0">SEO — منتجات بدون وصف</h6></div>
                    <ul class="list-group list-group-flush">
                        @forelse($seoIssues as $product)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $product->name }}</span>
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">إصلاح</a>
                        </li>
                        @empty
                        <li class="list-group-item text-muted">كل المنتجات لديها meta description</li>
                        @endforelse
                    </ul>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">UTM Sources</h6></div>
                    <ul class="list-group list-group-flush">
                        @forelse($utmSources as $utm)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $utm->utm_source }}</span>
                            <span>{{ $utm->total }}</span>
                        </li>
                        @empty
                        <li class="list-group-item text-muted">لا UTM مسجّلة — أضف utm_source للحملات</li>
                        @endforelse
                    </ul>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">أدنى درجات SEO (منتجات)</h6></div>
                    <ul class="list-group list-group-flush">
                        @forelse($seoScores as $row)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ Str::limit($row['name'], 40) }}</span>
                            <span class="badge {{ $row['score'] >= 70 ? 'bg-success' : ($row['score'] >= 40 ? 'bg-warning text-dark' : 'bg-danger') }}">{{ $row['score'] }}/100</span>
                        </li>
                        @empty
                        <li class="list-group-item text-muted">لا منتجات</li>
                        @endforelse
                    </ul>
                </div>
            </div>
            @if(!empty($seoAudit))
            <div class="col-12">
                <div class="card border-info">
                    <div class="card-header d-flex justify-content-between">
                        <h6 class="mb-0">آخر فحص SEO آلي</h6>
                        <span class="text-muted small">{{ $seoAudit['ran_at'] ?? '' }}</span>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">عدد المشكلات: <strong>{{ $seoAudit['issues_count'] ?? 0 }}</strong></p>
                        @if(!empty($seoAudit['issues']))
                        <ul class="small mb-0">
                            @foreach(array_slice($seoAudit['issues'], 0, 5) as $issue)
                            <li>{{ $issue['type'] }} #{{ $issue['id'] }} — {{ $issue['issue'] }}</li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="alert alert-info mt-4 mb-0">
            <strong>GA4 + GSC + Meta:</strong> أضف المعرفات من
            <a href="{{ route('admin.site-settings.index') }}">إعدادات الموقع → التحليلات</a>.
            <a href="{{ route('admin.pages.index') }}" class="ms-2">SEO الصفحات الثابتة</a>
            تحقق من Realtime في GA4 وMeta Pixel Helper بعد زيارة صفحة منتج.
        </div>
    </div>
</div>
@endsection
