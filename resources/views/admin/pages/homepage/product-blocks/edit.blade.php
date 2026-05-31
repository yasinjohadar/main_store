@extends('admin.layouts.master')

@section('page-title')
    {{ $label }}
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/admin-homepage.css') }}">
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <h5 class="page-title fs-21 mb-0">{{ $label }}</h5>
                <a href="{{ route('admin.homepage.index') }}" class="btn btn-light btn-sm">مدير الرئيسية</a>
            </div>

            <form method="POST" action="{{ route('admin.homepage.product-blocks.update', $block->block_key) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            @unless($isFashion)
                                <div class="col-md-6">
                                    <label class="form-label">عنوان القسم <span class="text-danger">*</span></label>
                                    <input type="text" name="section_title" class="form-control"
                                        value="{{ old('section_title', $block->section_title) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">CSS wrapper class</label>
                                    <input type="text" name="wrapper_class" class="form-control"
                                        value="{{ old('wrapper_class', $block->wrapper_class) }}">
                                </div>
                            @endunless

                            <div class="col-md-6">
                                <label class="form-label">صورة البانر</label>
                                <input type="file" name="banner_image" class="form-control" accept="image/*">
                                <small class="text-muted">مقاس مقترح: 280×400 بكسل للأقسام، 1920×400 لبانر الأزياء</small>
                                @if($block->banner_image_url)
                                    <div class="admin-homepage-preview mt-2">
                                        <img src="{{ $block->banner_image_url }}" alt="" class="admin-homepage-preview__img">
                                    </div>
                                    <div class="form-check mt-2">
                                        <input type="checkbox" class="form-check-input" name="remove_banner_image" value="1" id="remove_banner">
                                        <label class="form-check-label" for="remove_banner">حذف الصورة</label>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">لون الخلفية</label>
                                <input type="color" name="banner_bg_color" class="form-control form-control-color w-100"
                                    value="{{ old('banner_bg_color', $block->banner_bg_color ?? '#ebeced') }}">
                            </div>

                            @unless($isFashion)
                                <div class="col-md-6">
                                    <label class="form-label">عنوان فرعي للبانر</label>
                                    <input type="text" name="banner_subtitle" class="form-control"
                                        value="{{ old('banner_subtitle', $block->banner_subtitle) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">عنوان البانر (HTML مسموح)</label>
                                    <textarea name="banner_title" class="form-control" rows="2">{{ old('banner_title', $block->banner_title) }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">التصنيف</label>
                                    <select name="category_id" class="form-select">
                                        <option value="">— بدون —</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" @selected(old('category_id', $block->category_id) == $category->id)>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <div class="col-md-6">
                                    <label class="form-label">عنوان البانر</label>
                                    <input type="text" name="banner_title" class="form-control"
                                        value="{{ old('banner_title', $block->banner_title) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">نسبة الخصم</label>
                                    <input type="text" name="discount_percent" class="form-control"
                                        value="{{ old('discount_percent', $block->getSetting('discount_percent', '25')) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">كود الخصم</label>
                                    <input type="text" name="promo_code" class="form-control"
                                        value="{{ old('promo_code', $block->getSetting('promo_code', 'SAVE25')) }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">نص العرض (استخدم {code} للكود)</label>
                                    <textarea name="promo_text" class="form-control" rows="2">{{ old('promo_text', $block->getSetting('promo_text')) }}</textarea>
                                </div>
                            @endunless

                            <div class="col-md-6">
                                <label class="form-label">رابط مخصص (يُستخدم بدل التصنيف)</label>
                                <input type="text" name="button_url" class="form-control"
                                    value="{{ old('button_url', $block->button_url) }}">
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" class="form-check-input" name="is_active" value="1"
                                        {{ old('is_active', $block->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label">نشط</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.homepage.index') }}" class="btn btn-light">إلغاء</a>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
@stop
