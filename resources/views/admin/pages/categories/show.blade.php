@extends('admin.layouts.master')

@section('page-title')
    تفاصيل التصنيف
@stop

@section('css')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل التصنيف: {{ $category->name }}</h5>
                </div>
                <div>
                    @can('category-edit')
                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-primary btn-sm me-2">
                            <i class="bi bi-pencil"></i> تعديل
                        </a>
                    @endcan
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <!-- الصور -->
                                <div class="col-md-4 mb-4">
                                    <h6 class="text-primary mb-3">الصور</h6>
                                    @if($category->image)
                                        <div class="mb-3">
                                            <label class="form-label">صورة التصنيف</label>
                                            <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" 
                                                 class="img-fluid rounded" style="max-height: 200px;">
                                        </div>
                                    @endif
                                    @if($category->cover_image)
                                        <div>
                                            <label class="form-label">صورة الغلاف</label>
                                            <img src="{{ Storage::url($category->cover_image) }}" alt="صورة الغلاف" 
                                                 class="img-fluid rounded" style="max-height: 200px;">
                                        </div>
                                    @endif
                                </div>

                                <!-- المعلومات الأساسية -->
                                <div class="col-md-8">
                                    <h6 class="text-primary mb-3">المعلومات الأساسية</h6>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 200px;">الاسم</th>
                                            <td>{{ $category->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>الرابط (Slug)</th>
                                            <td><code>{{ $category->slug ?? '-' }}</code></td>
                                        </tr>
                                        <tr>
                                            <th>التصنيف الأب</th>
                                            <td>
                                                @if($category->parent)
                                                    <a href="{{ route('admin.categories.show', $category->parent->id) }}">
                                                        {{ $category->parent->name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">تصنيف رئيسي</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>الوصف</th>
                                            <td>{{ $category->description ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>الترتيب</th>
                                            <td>{{ $category->order }}</td>
                                        </tr>
                                        <tr>
                                            <th>الحالة</th>
                                            <td>
                                                @if($category->status == 'active')
                                                    <span class="badge bg-success">نشط</span>
                                                @else
                                                    <span class="badge bg-danger">غير نشط</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>تاريخ الإنشاء</th>
                                            <td>{{ $category->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>آخر تحديث</th>
                                            <td>{{ $category->updated_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- SEO -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">إعدادات SEO</h6>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 200px;">عنوان SEO</th>
                                            <td>{{ $category->meta_title ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>وصف SEO</th>
                                            <td>{{ $category->meta_description ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>الكلمات المفتاحية</th>
                                            <td>{{ $category->meta_keywords ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- كورسات هذا التصنيف -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">كورسات هذا التصنيف ({{ $category->courses->count() }})</h6>
                                    @if($category->courses->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>العنوان</th>
                                                        <th>الرابط</th>
                                                        <th>الحالة</th>
                                                        <th>السعر</th>
                                                        <th>العمليات</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($category->courses as $course)
                                                        <tr>
                                                            <td>{{ $course->title }}</td>
                                                            <td><code>{{ $course->slug ?? '-' }}</code></td>
                                                            <td>
                                                                @if($course->status == 'published')
                                                                    <span class="badge bg-success">منشور</span>
                                                                @elseif($course->status == 'draft')
                                                                    <span class="badge bg-secondary">مسودة</span>
                                                                @else
                                                                    <span class="badge bg-warning text-dark">أرشيف</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $course->price !== null ? number_format($course->price, 2) . ' ' . $course->currency : '-' }}</td>
                                                            <td>
                                                                @can('course-show')
                                                                    <a href="{{ route('admin.courses.show', $course->id) }}" class="btn btn-sm btn-info me-1"><i class="bi bi-eye"></i></a>
                                                                @endcan
                                                                @can('course-edit')
                                                                    <a href="{{ route('admin.courses.edit', $course->id) }}" class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></a>
                                                                @endcan
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted mb-0">لا توجد كورسات مرتبطة بهذا التصنيف.</p>
                                    @endif
                                </div>
                            </div>

                            <!-- التصنيفات الفرعية -->
                            @if($category->children->count() > 0)
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3">التصنيفات الفرعية ({{ $category->children->count() }})</h6>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>الاسم</th>
                                                        <th>الرابط</th>
                                                        <th>الحالة</th>
                                                        <th>العمليات</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($category->children as $child)
                                                        <tr>
                                                            <td>{{ $child->name }}</td>
                                                            <td><code>{{ $child->slug ?? '-' }}</code></td>
                                                            <td>
                                                                @if($child->status == 'active')
                                                                    <span class="badge bg-success">نشط</span>
                                                                @else
                                                                    <span class="badge bg-danger">غير نشط</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('admin.categories.show', $child->id) }}" 
                                                                   class="btn btn-sm btn-info">
                                                                    <i class="bi bi-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
