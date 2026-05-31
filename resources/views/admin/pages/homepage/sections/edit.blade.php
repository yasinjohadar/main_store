@extends('admin.layouts.master')

@section('page-title')
    إعدادات القسم
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
                <h5 class="page-title fs-21 mb-0">{{ $registry['label'] ?? $homeSection->key }}</h5>
                <a href="{{ route('admin.homepage.index') }}" class="btn btn-light btn-sm">العودة للمدير</a>
            </div>

            <form method="POST" action="{{ route('admin.homepage.sections.update', $homeSection) }}">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            @if($homeSection->key !== 'deals' || true)
                                <div class="col-md-6">
                                    <label class="form-label">العنوان الرئيسي</label>
                                    <input type="text" name="title" class="form-control"
                                        value="{{ old('title', $homeSection->title ?? $defaults['title']) }}"
                                        placeholder="{{ $defaults['title'] }}">
                                </div>
                            @endif

                            @if(in_array($homeSection->key, ['top_categories', 'popular_departments']))
                                <div class="col-md-6">
                                    <label class="form-label">العنوان الفرعي</label>
                                    <input type="text" name="subtitle" class="form-control"
                                        value="{{ old('subtitle', $homeSection->subtitle ?? $defaults['subtitle']) }}"
                                        placeholder="{{ $defaults['subtitle'] }}">
                                </div>
                            @endif

                            @if(in_array($homeSection->key, ['deals', 'blog', 'brands']))
                                <div class="col-md-6">
                                    <label class="form-label">نص علوي (Eyebrow)</label>
                                    <input type="text" name="eyebrow" class="form-control"
                                        value="{{ old('eyebrow', $homeSection->eyebrow ?? $defaults['eyebrow']) }}"
                                        placeholder="{{ $defaults['eyebrow'] }}">
                                </div>
                            @endif

                            @if($homeSection->key === 'deals')
                                @php
                                    $settings = $homeSection->settings ?? $defaults['settings'];
                                @endphp
                                <div class="col-md-6">
                                    <label class="form-label">نص علوي — الأكثر مبيعاً</label>
                                    <input type="text" name="settings[bestsellers_eyebrow]" class="form-control"
                                        value="{{ old('settings.bestsellers_eyebrow', $settings['bestsellers_eyebrow'] ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">عنوان — الأكثر مبيعاً</label>
                                    <input type="text" name="settings[bestsellers_title]" class="form-control"
                                        value="{{ old('settings.bestsellers_title', $settings['bestsellers_title'] ?? '') }}">
                                </div>
                            @endif

                            @if($homeSection->key === 'blog')
                                @php $settings = $homeSection->settings ?? $defaults['settings']; @endphp
                                <div class="col-md-6">
                                    <label class="form-label">نص زر «عرض الكل»</label>
                                    <input type="text" name="settings[view_all_label]" class="form-control"
                                        value="{{ old('settings.view_all_label', $settings['view_all_label'] ?? '') }}">
                                </div>
                            @endif

                            @if($homeSection->key === 'popular_departments')
                                @php
                                    $tabs = old('settings.tabs', $homeSection->getSetting('tabs', $defaults['settings']['tabs'] ?? []));
                                @endphp
                                <div class="col-12">
                                    <hr>
                                    <h6 class="mb-3">تبويبات الأقسام الشائعة</h6>
                                    @foreach($tabs as $i => $tab)
                                        <div class="border rounded p-3 mb-3">
                                            <input type="hidden" name="settings[tabs][{{ $i }}][key]" value="{{ $tab['key'] }}">
                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <label class="form-label">التسمية</label>
                                                    <input type="text" name="settings[tabs][{{ $i }}][label]" class="form-control"
                                                        value="{{ $tab['label'] ?? '' }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">أيقونة (class)</label>
                                                    <input type="text" name="settings[tabs][{{ $i }}][icon]" class="form-control"
                                                        value="{{ $tab['icon'] ?? '' }}" placeholder="w-icon-star">
                                                </div>
                                                <div class="col-md-4 d-flex align-items-end">
                                                    <div class="form-check">
                                                        <input type="hidden" name="settings[tabs][{{ $i }}][active]" value="0">
                                                        <input type="checkbox" class="form-check-input" name="settings[tabs][{{ $i }}][active]" value="1"
                                                            {{ !empty($tab['active']) ? 'checked' : '' }}>
                                                        <label class="form-check-label">التبويب النشط افتراضياً</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
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
