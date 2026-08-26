@extends('admin.layouts.master')

@section('page-title')
    إضافة تصنيف جديد
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li class="small">{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="las la-folder-plus"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>إضافة تصنيف جديد</h1>
                        <p>بيانات التصنيف، الصور، وإعدادات SEO</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('admin.categories.index') }}" class="admin-btn admin-btn-light">
                        <i class="las la-arrow-right"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="admin-page-card">
                <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data" class="admin-form">
                    @csrf

                    <div class="admin-form-body">

                        <div class="admin-form-section">
                            <div class="admin-form-section-head">
                                <div class="admin-section-icon admin-section-icon-blue">
                                    <i class="las la-folder"></i>
                                </div>
                                <div>
                                    <h3>المعلومات الأساسية</h3>
                                    <p>الاسم والرابط والتصنيف الأب</p>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="admin-form-field">
                                        <label class="admin-form-label">اسم التصنيف <span class="required">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                               name="name" placeholder="اسم التصنيف" value="{{ old('name') }}" required>
                                        @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="admin-form-field">
                                        <label class="admin-form-label">الرابط (Slug)</label>
                                        <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                               name="slug" placeholder="سيتم إنشاؤه تلقائياً من الاسم إذا تُرك فارغاً" value="{{ old('slug') }}">
                                        @error('slug')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="admin-form-field">
                                        <label class="admin-form-label">التصنيف الأب</label>
                                        <select class="form-select @error('parent_id') is-invalid @enderror" name="parent_id">
                                            <option value="">تصنيف رئيسي</option>
                                            @foreach($parentCategories as $parent)
                                                <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                                    {{ $parent->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('parent_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="admin-form-field">
                                        <label class="admin-form-label">الترتيب</label>
                                        <input type="number" class="form-control @error('order') is-invalid @enderror"
                                               name="order" value="{{ old('order', 0) }}" min="0">
                                        @error('order')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="admin-form-field">
                                        <label class="admin-form-label">الحالة <span class="required">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>نشط</option>
                                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                        </select>
                                        @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="admin-form-field">
                                        <label class="admin-form-label">الوصف</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror"
                                                  name="description" style="height: 100px">{{ old('description') }}</textarea>
                                        @error('description')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="admin-form-section">
                            <div class="admin-form-section-head">
                                <div class="admin-section-icon admin-section-icon-green">
                                    <i class="las la-image"></i>
                                </div>
                                <div>
                                    <h3>الصور</h3>
                                    <p>صورة التصنيف وصورة الغلاف</p>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="admin-form-field admin-image-zone">
                                        <label class="admin-form-label">صورة التصنيف</label>
                                        <input type="file" class="form-control @error('image') is-invalid @enderror"
                                               name="image" accept="image/*" onchange="previewImage(this, 'image-preview')">
                                        <img id="image-preview" class="admin-image-preview" style="display: none;" alt="معاينة الصورة">
                                        @error('image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="admin-form-field admin-image-zone">
                                        <label class="admin-form-label">صورة الغلاف</label>
                                        <input type="file" class="form-control @error('cover_image') is-invalid @enderror"
                                               name="cover_image" accept="image/*" onchange="previewImage(this, 'cover-preview')">
                                        <img id="cover-preview" class="admin-image-preview admin-image-preview--cover" style="display: none;" alt="معاينة صورة الغلاف">
                                        @error('cover_image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="admin-form-section">
                            <div class="admin-form-section-head">
                                <div class="admin-section-icon admin-section-icon-purple">
                                    <i class="las la-search"></i>
                                </div>
                                <div>
                                    <h3>إعدادات SEO</h3>
                                    <p>تحسين ظهور التصنيف في محركات البحث</p>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="admin-form-field">
                                        <label class="admin-form-label">عنوان SEO (Meta Title)</label>
                                        <input type="text" class="form-control @error('meta_title') is-invalid @enderror"
                                               name="meta_title" value="{{ old('meta_title') }}">
                                        @error('meta_title')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="admin-form-field">
                                        <label class="admin-form-label">وصف SEO (Meta Description)</label>
                                        <textarea class="form-control @error('meta_description') is-invalid @enderror"
                                                  name="meta_description" style="height: 100px">{{ old('meta_description') }}</textarea>
                                        @error('meta_description')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="admin-form-field">
                                        <label class="admin-form-label">الكلمات المفتاحية (Meta Keywords)</label>
                                        <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror"
                                               name="meta_keywords" placeholder="افصل بين الكلمات بفواصل" value="{{ old('meta_keywords') }}">
                                        @error('meta_keywords')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="admin-form-footer">
                        <a href="{{ route('admin.categories.index') }}" class="admin-btn admin-btn-secondary">
                            <i class="las la-times"></i>
                            إلغاء
                        </a>
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="las la-save"></i>
                            حفظ التصنيف
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@stop

@section('script')
    <script>
        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var preview = document.getElementById(previewId);
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@stop
