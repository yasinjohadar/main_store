@extends('admin.layouts.master')

@section('page-title')
لوحة القيادة
@stop

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin-command-center.css') }}">
@stop

@section('content')
<div class="main-content app-content cmd-dashboard">
    <div class="container-fluid">

        {{-- Hero + period filter --}}
        <div class="cmd-dashboard__hero">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h2>مرحباً، {{ auth()->user()->name ?? 'مدير' }}</h2>
                    <p>نظرة شاملة على أداء المتجر — من {{ $filters['start'] }} إلى {{ $filters['end'] }}</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.reports.dashboard') }}" class="btn btn-sm btn-light">تقارير مفصّلة</a>
                    <a href="{{ route('admin.marketing.dashboard') }}" class="btn btn-sm btn-outline-light">التسويق</a>
                </div>
            </div>
            <div class="cmd-period-pills">
                @foreach([
                    'today' => 'اليوم',
                    'last_7_days' => '7 أيام',
                    'last_30_days' => '30 يوماً',
                    'this_month' => 'هذا الشهر',
                ] as $key => $label)
                    <a href="{{ route('admin.dashboard', ['preset' => $key]) }}"
                       class="{{ $preset === $key ? 'is-active' : '' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>

        {{-- KPI widgets — bento layout --}}
        @php
            $kpiCards = [
                ['key' => 'orders', 'label' => 'طلبات مؤكدة', 'format' => 'number', 'icon' => 'la-shopping-bag', 'tone' => 'cyan', 'href' => route('admin.orders.index')],
                ['key' => 'aov', 'label' => 'متوسط قيمة الطلب', 'format' => 'money', 'icon' => 'la-receipt', 'tone' => 'violet'],
                ['key' => 'page_views', 'label' => 'زيارات الصفحات', 'format' => 'number', 'icon' => 'la-eye', 'tone' => 'emerald'],
                ['key' => 'add_to_cart', 'label' => 'إضافة للسلة', 'format' => 'number', 'icon' => 'la-cart-plus', 'tone' => 'amber'],
                ['key' => 'new_customers', 'label' => 'عملاء جدد', 'format' => 'number', 'icon' => 'la-user-plus', 'tone' => 'rose', 'href' => route('admin.customers.index')],
            ];
            $revenueKpi = $kpis['revenue'] ?? ['value' => 0, 'trend' => null];
            $ordersKpi = $kpis['orders'] ?? ['value' => 0, 'trend' => null];
        @endphp

        <div class="cmd-widgets">
            {{-- Featured revenue --}}
            <a href="{{ route('admin.reports.dashboard') }}" class="cmd-widget cmd-widget--hero cmd-widget--tone-indigo text-decoration-none">
                <div class="cmd-widget__bg"></div>
                <div class="cmd-widget__shine"></div>
                <div class="cmd-widget__mesh"></div>
                <div class="cmd-widget__bubble cmd-widget__bubble--1"></div>
                <div class="cmd-widget__bubble cmd-widget__bubble--2"></div>
                <div class="cmd-widget__bubble cmd-widget__bubble--3"></div>
                <div class="cmd-widget__glow"></div>
                <div class="cmd-widget__top">
                    <span class="cmd-widget__icon-wrap">
                        <span class="cmd-widget__icon-ring" aria-hidden="true"></span>
                        <span class="cmd-widget__icon"><i class="las la-wallet"></i></span>
                    </span>
                    <span class="cmd-widget__chip">الأداء المالي</span>
                </div>
                <div class="cmd-widget__body">
                    <span class="cmd-widget__label">إيرادات المبيعات</span>
                    <span class="cmd-widget__value">{{ $currencyService->format((float) $revenueKpi['value']) }}</span>
                    @if($revenueKpi['trend'] !== null)
                        @php $trendUp = $revenueKpi['trend'] >= 0; @endphp
                        <span class="cmd-widget__trend cmd-widget__trend--{{ $revenueKpi['trend'] == 0 ? 'neutral' : ($trendUp ? 'up' : 'down') }}">
                            @if($revenueKpi['trend'] != 0)<i class="las la-arrow-{{ $trendUp ? 'up' : 'down' }}"></i>@endif
                            {{ $revenueKpi['trend'] > 0 ? '+' : '' }}{{ $revenueKpi['trend'] }}%
                        </span>
                    @endif
                </div>
                <div class="cmd-widget__footer">
                    <span><strong data-count="{{ (int) $ordersKpi['value'] }}">0</strong> طلب مؤكد</span>
                    <span class="cmd-widget__link">التقارير <i class="las la-arrow-left"></i></span>
                </div>
            </a>

            <div class="cmd-widgets__grid">
                @foreach($kpiCards as $index => $card)
                    @php $kpi = $kpis[$card['key']] ?? ['value' => 0, 'trend' => null]; @endphp
                    @php
                        $tag = isset($card['href']) ? 'a' : 'div';
                        $trendUp = ($kpi['trend'] ?? 0) >= 0;
                        $trendClass = $kpi['trend'] === null ? 'neutral' : ($kpi['trend'] == 0 ? 'neutral' : ($trendUp ? 'up' : 'down'));
                    @endphp
                    <{{ $tag }}
                        @if(isset($card['href'])) href="{{ $card['href'] }}" @endif
                        style="--card-delay: {{ ($index + 1) * 0.08 }}s"
                        class="cmd-widget cmd-widget--compact cmd-widget--tone-{{ $card['tone'] }}{{ isset($card['href']) ? ' cmd-widget--link' : '' }}">
                        <div class="cmd-widget__shine"></div>
                        <div class="cmd-widget__mesh"></div>
                        <div class="cmd-widget__bubble cmd-widget__bubble--1"></div>
                        <div class="cmd-widget__bubble cmd-widget__bubble--3"></div>
                        <div class="cmd-widget__glow"></div>
                        <div class="cmd-widget__row">
                            <span class="cmd-widget__icon-wrap">
                                <span class="cmd-widget__icon-ring" aria-hidden="true"></span>
                                <span class="cmd-widget__icon cmd-widget__icon--sm"><i class="las {{ $card['icon'] }}"></i></span>
                            </span>
                            <div class="cmd-widget__meta">
                                <span class="cmd-widget__label">{{ $card['label'] }}</span>
                                @if($card['format'] === 'money')
                                    <span class="cmd-widget__value cmd-widget__value--sm">{{ $currencyService->format((float) $kpi['value']) }}</span>
                                @else
                                    <span class="cmd-widget__value cmd-widget__value--sm" data-count="{{ (int) $kpi['value'] }}">0</span>
                                @endif
                            </div>
                        </div>
                        @if($kpi['trend'] !== null)
                            <span class="cmd-widget__trend cmd-widget__trend--{{ $trendClass }} cmd-widget__trend--inline">
                                @if($kpi['trend'] != 0)<i class="las la-arrow-{{ $trendUp ? 'up' : 'down' }}"></i>@endif
                                {{ $kpi['trend'] > 0 ? '+' : '' }}{{ $kpi['trend'] }}%
                            </span>
                        @endif
                    </{{ $tag }}>
                @endforeach
            </div>
        </div>

        {{-- Insight chips --}}
        <div class="cmd-insights">
            <div class="cmd-insight">
                <span class="cmd-insight__icon"><i class="las la-percentage"></i></span>
                <div>
                    <span class="cmd-insight__label">معدل التحويل</span>
                    <span class="cmd-insight__value">{{ number_format($conversionRate, 2) }}%</span>
                </div>
            </div>
            <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="cmd-insight cmd-insight--link">
                <span class="cmd-insight__icon cmd-insight__icon--warning"><i class="las la-clock"></i></span>
                <div>
                    <span class="cmd-insight__label">طلبات قيد الانتظار</span>
                    <span class="cmd-insight__value">{{ number_format($pendingOrdersCount) }}</span>
                </div>
            </a>
            <a href="{{ route('admin.inventory.index') }}" class="cmd-insight cmd-insight--link">
                <span class="cmd-insight__icon cmd-insight__icon--danger"><i class="las la-exclamation-triangle"></i></span>
                <div>
                    <span class="cmd-insight__label">مخزون منخفض</span>
                    <span class="cmd-insight__value">{{ number_format($lowStockCount) }}</span>
                </div>
            </a>
            <div class="cmd-insight">
                <span class="cmd-insight__icon cmd-insight__icon--info"><i class="las la-star"></i></span>
                <div>
                    <span class="cmd-insight__label">مراجعات بانتظار الموافقة</span>
                    <span class="cmd-insight__value">{{ number_format($pendingReviews) }}</span>
                </div>
            </div>
        </div>

        {{-- Funnel + catalog mini stats --}}
        <div class="row g-3 mb-3">
            <div class="col-lg-5">
                <div class="cmd-card">
                    <div class="cmd-card__head">
                        <h3 class="cmd-card__title">مسار التحويل</h3>
                    </div>
                    <div class="cmd-card__body">
                        @php $funnelMax = max(1, collect($funnel)->max('value')); @endphp
                        <div class="cmd-funnel">
                            @foreach($funnel as $step)
                                <div class="cmd-funnel__step">
                                    <span class="small fw-medium">{{ $step['label'] }}</span>
                                    <div class="cmd-funnel__bar-wrap">
                                        <div class="cmd-funnel__bar" style="width: {{ round(($step['value'] / $funnelMax) * 100) }}%"></div>
                                    </div>
                                    <span class="small fw-bold text-end">{{ number_format($step['value']) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="cmd-card">
                    <div class="cmd-card__head">
                        <h3 class="cmd-card__title">أفضل المنتجات مبيعاً</h3>
                        <a href="{{ route('admin.reports.dashboard') }}" class="small text-primary">عرض الكل</a>
                    </div>
                    <div class="cmd-card__body p-0">
                        @if($topProducts->isNotEmpty())
                            <table class="table cmd-table-compact mb-0">
                                <thead class="cmd-table-head">
                                    <tr><th>المنتج</th><th>الكمية</th><th>الإيراد</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($topProducts as $row)
                                        <tr>
                                            <td>{{ \Illuminate\Support\Str::limit($row->product_name, 40) }}</td>
                                            <td>{{ number_format($row->qty) }}</td>
                                            <td class="fw-semibold">{{ $currencyService->format((float) $row->revenue) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="cmd-empty">لا توجد مبيعات في هذه الفترة.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent orders + alerts + quick actions --}}
        <div class="row g-3 mb-3">
            <div class="col-xl-7">
                <div class="cmd-card">
                    <div class="cmd-card__head">
                        <h3 class="cmd-card__title">آخر الطلبات</h3>
                        <a href="{{ route('admin.orders.index') }}" class="small text-primary">جميع الطلبات</a>
                    </div>
                    <div class="cmd-card__body p-0">
                        @if($recentOrders->isNotEmpty())
                            <table class="table cmd-table-compact mb-0">
                                <thead class="cmd-table-head">
                                    <tr>
                                        <th>الطلب</th>
                                        <th>العميل</th>
                                        <th>المبلغ</th>
                                        <th>الحالة</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                        <tr>
                                            <td class="fw-medium">#{{ $order->order_number }}</td>
                                            <td>{{ $order->user->name ?? $order->guest_email ?? '—' }}</td>
                                            <td>{{ $currencyService->format((float) $order->total) }}</td>
                                            <td>
                                                <span class="cmd-badge-pill" style="background: {{ $order->status->color ?? '#64748b' }}22; color: {{ $order->status->color ?? '#64748b' }}">
                                                    {{ $order->status->name ?? '—' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">عرض</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="cmd-empty">لا توجد طلبات بعد.</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="cmd-card">
                            <div class="cmd-card__head">
                                <h3 class="cmd-card__title">تنبيهات المخزون</h3>
                                @if($lowStockProducts->isNotEmpty())
                                    <span class="badge bg-warning">{{ $lowStockProducts->count() }}+</span>
                                @endif
                            </div>
                            <div class="cmd-card__body p-0">
                                @if($lowStockProducts->isNotEmpty())
                                    <ul class="list-group list-group-flush">
                                        @foreach($lowStockProducts as $product)
                                            <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                                <a href="{{ route('admin.products.edit', $product) }}" class="text-truncate me-2">{{ $product->name }}</a>
                                                <span class="badge bg-danger">{{ $product->stock_quantity }} متبقي</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="cmd-empty">المخزون جيد — لا منتجات تحت {{ $lowStockThreshold }}.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="cmd-card">
                            <div class="cmd-card__head">
                                <h3 class="cmd-card__title">آراء العملاء</h3>
                                @if($pendingReviews > 0)
                                    <a href="{{ route('admin.reviews.index') }}" class="badge bg-warning text-dark">{{ $pendingReviews }} بانتظار المراجعة</a>
                                @endif
                            </div>
                            <div class="cmd-card__body p-0">
                                @if($recentReviews->isNotEmpty())
                                    <table class="table cmd-table-compact mb-0">
                                        <thead class="cmd-table-head">
                                            <tr><th>المستخدم</th><th>التقييم</th><th>الحالة</th></tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentReviews as $review)
                                                <tr>
                                                    <td>{{ $review->user->name ?? '—' }}</td>
                                                    <td>
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="las la-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted opacity-25' }}"></i>
                                                        @endfor
                                                    </td>
                                                    <td><span class="badge {{ $review->status_badge }}">{{ $review->status_text }}</span></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="cmd-empty">لا توجد آراء بعد.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick actions + catalog stats --}}
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="cmd-card">
                    <div class="cmd-card__head">
                        <h3 class="cmd-card__title">إجراءات سريعة</h3>
                    </div>
                    <div class="cmd-card__body">
                        <div class="cmd-quick-grid">
                            <a href="{{ route('admin.products.create') }}" class="cmd-quick-link">
                                <span class="cmd-quick-link__icon"><i class="las la-plus"></i></span>
                                <span><strong>منتج جديد</strong><br><small class="text-muted">إضافة منتج للمتجر</small></span>
                            </a>
                            <a href="{{ route('admin.orders.index') }}" class="cmd-quick-link">
                                <span class="cmd-quick-link__icon"><i class="las la-shopping-bag"></i></span>
                                <span><strong>إدارة الطلبات</strong><br><small class="text-muted">متابعة وتحديث الحالات</small></span>
                            </a>
                            <a href="{{ route('admin.categories.index') }}" class="cmd-quick-link">
                                <span class="cmd-quick-link__icon"><i class="las la-th-large"></i></span>
                                <span><strong>التصنيفات</strong><br><small class="text-muted">{{ number_format($catalogStats['categories']) }} تصنيف</small></span>
                            </a>
                            <a href="{{ route('admin.reviews.index') }}" class="cmd-quick-link">
                                <span class="cmd-quick-link__icon"><i class="las la-comment"></i></span>
                                <span><strong>مراجعات العملاء</strong><br><small class="text-muted">{{ number_format($catalogStats['reviews']) }} مراجعة</small></span>
                            </a>
                            <a href="{{ route('admin.blog.posts.index') }}" class="cmd-quick-link">
                                <span class="cmd-quick-link__icon"><i class="las la-newspaper"></i></span>
                                <span><strong>المدونة</strong><br><small class="text-muted">{{ number_format($catalogStats['posts']) }} مقال</small></span>
                            </a>
                            <a href="{{ route('admin.store-settings.index') }}" class="cmd-quick-link">
                                <span class="cmd-quick-link__icon"><i class="las la-cog"></i></span>
                                <span><strong>إعدادات المتجر</strong><br><small class="text-muted">الضريبة، الشحن، الدفع</small></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="cmd-card">
                    <div class="cmd-card__head">
                        <h3 class="cmd-card__title">ملخص المتجر</h3>
                    </div>
                    <div class="cmd-card__body">
                        <div class="row g-2 text-center">
                            @foreach([
                                ['label' => 'منتجات', 'value' => $catalogStats['products'], 'icon' => 'la-box'],
                                ['label' => 'تصنيفات', 'value' => $catalogStats['categories'], 'icon' => 'la-th'],
                                ['label' => 'مراجعات', 'value' => $catalogStats['reviews'], 'icon' => 'la-star'],
                                ['label' => 'مقالات', 'value' => $catalogStats['posts'], 'icon' => 'la-file-alt'],
                            ] as $stat)
                                <div class="col-6">
                                    <div class="cmd-stat-box rounded-3 p-3">
                                        <i class="las {{ $stat['icon'] }} fs-4 text-primary"></i>
                                        <div class="fw-bold fs-5 mt-1">{{ number_format($stat['value']) }}</div>
                                        <div class="small text-muted">{{ $stat['label'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@stop

@section('script')
<script src="{{ asset('assets/js/admin-command-center.js') }}"></script>
@stop
