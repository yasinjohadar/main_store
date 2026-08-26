@extends('admin.layouts.master')

@section('page-title')
    قائمة آراء العملاء
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="las la-star"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>آراء العملاء</h1>
                        <p>إدارة تقييمات المنتجات ومراجعتها واعتمادها</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    @can('review-statistics')
                        <a href="{{ route('admin.reviews.statistics') }}" class="admin-btn admin-btn-secondary">
                            <i class="las la-chart-bar"></i>
                            الإحصائيات
                        </a>
                    @endcan
                    @can('review-create')
                        <a href="{{ route('admin.reviews.create') }}" class="admin-btn admin-btn-light">
                            <i class="las la-plus"></i>
                            إضافة رأي جديد
                        </a>
                    @endcan
                </div>
            </div>

            <div class="admin-report-stats">
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-blue">
                    <span class="admin-report-stat-icon"><i class="las la-comments"></i></span>
                    <span class="admin-report-stat-label">إجمالي الآراء</span>
                    <span class="admin-report-stat-value">{{ $reviewStats['total'] ?? 0 }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-green">
                    <span class="admin-report-stat-icon"><i class="las la-check-circle"></i></span>
                    <span class="admin-report-stat-label">معتمدة</span>
                    <span class="admin-report-stat-value">{{ $reviewStats['approved'] ?? 0 }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-amber">
                    <span class="admin-report-stat-icon"><i class="las la-clock"></i></span>
                    <span class="admin-report-stat-label">بانتظار المراجعة</span>
                    <span class="admin-report-stat-value">{{ $reviewStats['pending'] ?? 0 }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-cyan">
                    <span class="admin-report-stat-icon"><i class="las la-star"></i></span>
                    <span class="admin-report-stat-label">متوسط التقييم</span>
                    <span class="admin-report-stat-value">{{ $reviewStats['average_rating'] ?? 0 }}</span>
                </div>
            </div>

            <div class="admin-page-card">
                <div class="card-toolbar">
                    <form action="{{ route('admin.reviews.index') }}" method="GET" class="admin-filters">
                        <div class="search-input-wrap">
                            <i class="las la-search"></i>
                            <input type="text" name="query" class="form-control"
                                placeholder="بحث بالمستخدم أو التعليق" value="{{ request('query') }}" autocomplete="off">
                        </div>

                        <select name="product_id" class="form-select admin-filter-select">
                            <option value="">كل المنتجات</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ Str::limit($p->name, 40) }}</option>
                            @endforeach
                        </select>

                        <select name="status" class="form-select admin-filter-select">
                            <option value="">كل الحالات</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>معتمد</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                            <option value="spam" {{ request('status') == 'spam' ? 'selected' : '' }}>محتوى غير مرغوب</option>
                        </select>

                        <select name="rating" class="form-select admin-filter-select">
                            <option value="">كل التقييمات</option>
                            <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 نجوم</option>
                            <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 نجوم</option>
                            <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 نجوم</option>
                            <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 نجوم</option>
                            <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 نجم</option>
                        </select>

                        <button type="submit" class="admin-btn admin-btn-primary admin-btn-sm">
                            <i class="las la-search"></i>
                        </button>
                        <a href="{{ route('admin.reviews.index') }}" class="admin-btn admin-btn-secondary admin-btn-sm">مسح</a>
                    </form>
                </div>

                <div class="admin-table-wrap">
                    <div class="table-responsive">
                        <table class="admin-data-table">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 48px;">#</th>
                                    <th scope="col">المنتج</th>
                                    <th scope="col">المستخدم</th>
                                    <th scope="col">التقييم</th>
                                    <th scope="col">التعليق</th>
                                    <th scope="col">الحالة</th>
                                    <th scope="col" style="width: 70px;">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($reviews as $review)
                                    <tr>
                                        <th scope="row" class="row-number">{{ $loop->iteration + ($reviews->currentPage() - 1) * $reviews->perPage() }}</th>
                                        <td>
                                            @if($review->product)
                                                <a href="{{ route('admin.products.edit', $review->product) }}" class="admin-user-link">{{ Str::limit($review->product->name, 35) }}</a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($review->user)
                                                <div class="admin-user-cell">
                                                    <span class="admin-avatar-initial">{{ mb_substr($review->user->name, 0, 1) }}</span>
                                                    <div class="min-w-0">
                                                        <div class="fw-bold">
                                                            {{ $review->user->name }}
                                                            @if($review->is_verified_purchase)
                                                                <span class="admin-badge admin-badge-success" title="شراء موثق">✓ موثق</span>
                                                            @endif
                                                        </div>
                                                        <small class="text-muted">{{ $review->user->email }}</small>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">زائر</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-warning">{{ str_repeat('★', $review->rating) }}</span><span class="text-muted">{{ str_repeat('☆', 5 - $review->rating) }}</span>
                                            <span class="text-muted">({{ $review->rating }}/5)</span>
                                        </td>
                                        <td style="max-width: 280px;">
                                            @if($review->title)
                                                <div class="fw-bold">{{ Str::limit($review->title, 50) }}</div>
                                            @endif
                                            @if($review->comment)
                                                <p class="mb-0 text-muted small">{{ Str::limit($review->comment, 80) }}</p>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="admin-badge {{ $review->status_badge }}">{{ $review->status_text }}</span>
                                            @if($review->is_featured)
                                                <span class="admin-badge admin-badge-warning">مميز</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="admin-row-actions dropdown">
                                                <button class="admin-kebab-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="las la-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    @can('review-show')
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('admin.reviews.show', $review->id) }}">
                                                                <i class="las la-eye text-info me-2"></i>عرض
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    @can('review-edit')
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('admin.reviews.edit', $review->id) }}">
                                                                <i class="las la-pen text-primary me-2"></i>تعديل
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    @can('review-approve')
                                                        @if($review->status != 'approved')
                                                            <li>
                                                                <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST">
                                                                    @csrf
                                                                    <button type="submit" class="dropdown-item border-0 bg-transparent w-100 text-start">
                                                                        <i class="las la-check-circle text-success me-2"></i>اعتماد
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                    @endcan
                                                    @can('review-delete')
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <button type="button" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start"
                                                                data-confirm-delete
                                                                data-action="{{ route('admin.reviews.destroy', $review) }}"
                                                                data-method="DELETE"
                                                                data-title="حذف الرأي"
                                                                data-message="هل أنت متأكد من حذف هذا الرأي؟"
                                                                data-warning="لن يمكن التراجع عن هذا الإجراء.">
                                                                <i class="las la-trash me-2"></i>حذف الرأي
                                                            </button>
                                                        </li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="admin-empty-state">
                                                <i class="las la-star"></i>
                                                لا توجد آراء
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="admin-table-footer">
                    {{ $reviews->links() }}
                </div>
            </div>

        </div>
    </div>
@stop
