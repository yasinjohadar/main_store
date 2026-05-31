@extends('admin.layouts.master')

@section('page-title')
    إعدادات الموقع العامة
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                    <h5 class="page-title fs-21 mb-1">إعدادات الموقع العامة</h5>
                    <p class="text-muted mb-0 small">اسم الموقع، الشعار، التواصل، وضع الصيانة، SEO، والتحليلات (GA4/Pixel). لإعدادات المتجر والدفع راجع «إعدادات المتجر» و«التكاملات».</p>
                </div>
            </div>

            @php
                $bySection = [];
                foreach ($schema as $key => $def) {
                    $bySection[$def['section']][$key] = $def;
                }
                $sectionsOrder = array_keys($sectionLabels);
            @endphp

            <form action="{{ route('admin.site-settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-header border-bottom-0">
                        <ul class="nav nav-tabs card-header-tabs" id="siteSettingsTabs" role="tablist">
                            @foreach ($sectionsOrder as $idx => $sectionKey)
                                @if (isset($bySection[$sectionKey]))
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $idx === 0 ? 'active' : '' }}" id="tab-{{ $sectionKey }}" data-bs-toggle="tab" data-bs-target="#pane-{{ $sectionKey }}"
                                                type="button" role="tab" aria-controls="pane-{{ $sectionKey }}" aria-selected="{{ $idx === 0 ? 'true' : 'false' }}">
                                            {{ $sectionLabels[$sectionKey] }}
                                        </button>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-body tab-content" id="siteSettingsTabsContent">
                        @foreach ($sectionsOrder as $idx => $sectionKey)
                            @if (!isset($bySection[$sectionKey]))
                                @continue
                            @endif
                            <div class="tab-pane fade {{ $idx === 0 ? 'show active' : '' }}" id="pane-{{ $sectionKey }}" role="tabpanel" aria-labelledby="tab-{{ $sectionKey }}">
                                <div class="row g-3">
                                    @foreach ($bySection[$sectionKey] as $key => $def)
                                        @php
                                            $value = old($key, $settings[$key] ?? $def['default']);
                                            $isBoolean = ($def['type'] ?? 'string') === 'boolean';
                                        @endphp
                                        <div class="col-12 {{ $isBoolean ? '' : 'col-md-6' }}">
                                            @if ($isBoolean)
                                                <div class="form-check form-switch">
                                                    <input type="hidden" name="{{ $key }}" value="0">
                                                    <input class="form-check-input @error($key) is-invalid @enderror" type="checkbox" name="{{ $key }}" value="1" id="input-{{ $key }}"
                                                           {{ $value ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="input-{{ $key }}">{{ $def['label'] }}</label>
                                                    @if (!empty($def['hint']))
                                                        <small class="d-block text-muted">{{ $def['hint'] }}</small>
                                                    @endif
                                                    @error($key)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                                </div>
                                            @elseif ($key === \App\Services\SiteSettingsService::KEY_SITE_LOGO)
                                                @php($logoPreviewUrl = site_setting_url(\App\Services\SiteSettingsService::KEY_SITE_LOGO))
                                                <label class="form-label">{{ $def['label'] }}</label>
                                                @if ($logoPreviewUrl)
                                                    <div class="mb-3 p-3 border rounded bg-light d-flex flex-wrap align-items-center gap-3">
                                                        <img src="{{ $logoPreviewUrl }}" alt="معاينة الشعار" class="img-thumbnail bg-white" style="max-height: 72px; max-width: 220px; object-fit: contain;">
                                                        <div>
                                                            <div class="form-check mb-0">
                                                                <input type="hidden" name="remove_site_logo" value="0">
                                                                <input class="form-check-input" type="checkbox" name="remove_site_logo" value="1" id="remove_site_logo">
                                                                <label class="form-check-label text-danger" for="remove_site_logo">حذف الشعار الحالي</label>
                                                            </div>
                                                            <small class="text-muted d-block mt-1">ارفع ملفاً جديداً لاستبداله دون حذف.</small>
                                                        </div>
                                                    </div>
                                                @endif
                                                <input type="file" class="form-control @error('site_logo_file') is-invalid @enderror" name="site_logo_file" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                                                @if (!empty($def['hint']))
                                                    <small class="text-muted d-block mt-1">{{ $def['hint'] }}</small>
                                                @endif
                                                @error('site_logo_file')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            @elseif ($key === \App\Services\SiteSettingsService::KEY_SITE_PRIMARY_COLOR)
                                                <label class="form-label" for="input-{{ $key }}">{{ $def['label'] }}</label>
                                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                                    <input type="color"
                                                           class="form-control form-control-color @error($key) is-invalid @enderror"
                                                           id="picker-{{ $key }}"
                                                           value="{{ $value ?: \App\Support\SiteTheme::DEFAULT_PRIMARY }}"
                                                           title="اختر اللون الأساسي">
                                                    <input type="text"
                                                           class="form-control @error($key) is-invalid @enderror"
                                                           name="{{ $key }}"
                                                           id="input-{{ $key }}"
                                                           value="{{ $value ?: \App\Support\SiteTheme::DEFAULT_PRIMARY }}"
                                                           maxlength="7"
                                                           pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
                                                           dir="ltr"
                                                           style="max-width: 8rem;">
                                                    <span class="rounded-circle border"
                                                          id="preview-{{ $key }}"
                                                          style="width: 2.25rem; height: 2.25rem; background: {{ $value ?: \App\Support\SiteTheme::DEFAULT_PRIMARY }};"></span>
                                                </div>
                                                @if (!empty($def['hint']))
                                                    <small class="text-muted d-block mt-1">{{ $def['hint'] }}</small>
                                                @endif
                                                @error($key)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            @elseif ($key === \App\Services\SiteSettingsService::KEY_SITE_BORDER_RADIUS)
                                                <label class="form-label" for="input-{{ $key }}">{{ $def['label'] }}</label>
                                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                                    <input type="number"
                                                           class="form-control @error($key) is-invalid @enderror"
                                                           name="{{ $key }}"
                                                           id="input-{{ $key }}"
                                                           value="{{ old($key, $value ?? \App\Support\SiteTheme::DEFAULT_BORDER_RADIUS) }}"
                                                           min="0"
                                                           max="50"
                                                           step="1"
                                                           style="max-width: 8rem;">
                                                    <span class="text-muted">px</span>
                                                    <span class="border bg-light d-inline-flex align-items-center justify-content-center"
                                                          id="preview-{{ $key }}"
                                                          style="width: 4.5rem; height: 3rem; border-radius: {{ (int) ($value ?? \App\Support\SiteTheme::DEFAULT_BORDER_RADIUS) }}px; border-color: #dee2e6 !important;"></span>
                                                </div>
                                                @if (!empty($def['hint']))
                                                    <small class="text-muted d-block mt-1">{{ $def['hint'] }}</small>
                                                @endif
                                                @error($key)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            @elseif ($key === \App\Services\SiteSettingsService::KEY_SITE_FAVICON)
                                                @php($faviconPreviewUrl = site_setting_url(\App\Services\SiteSettingsService::KEY_SITE_FAVICON))
                                                <label class="form-label">{{ $def['label'] }}</label>
                                                @if ($faviconPreviewUrl)
                                                    <div class="mb-3 p-3 border rounded bg-light d-flex flex-wrap align-items-center gap-3">
                                                        <img src="{{ $faviconPreviewUrl }}" alt="معاينة الأيقونة" class="img-thumbnail bg-white" style="width: 48px; height: 48px; object-fit: contain;">
                                                        <div>
                                                            <div class="form-check mb-0">
                                                                <input type="hidden" name="remove_site_favicon" value="0">
                                                                <input class="form-check-input" type="checkbox" name="remove_site_favicon" value="1" id="remove_site_favicon">
                                                                <label class="form-check-label text-danger" for="remove_site_favicon">حذف الأيقونة الحالية</label>
                                                            </div>
                                                            <small class="text-muted d-block mt-1">ارفع ملفاً جديداً لاستبدالها دون حذف.</small>
                                                        </div>
                                                    </div>
                                                @endif
                                                <input type="file" class="form-control @error('site_favicon_file') is-invalid @enderror" name="site_favicon_file" accept="image/png,image/jpeg,image/webp,image/svg+xml,image/x-icon,.ico">
                                                @if (!empty($def['hint']))
                                                    <small class="text-muted d-block mt-1">{{ $def['hint'] }}</small>
                                                @endif
                                                @error('site_favicon_file')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            @elseif ($key === \App\Services\SiteSettingsService::KEY_NEWSLETTER_POPUP_IMAGE)
                                                @php($popupImageUrl = site_setting_url(\App\Services\SiteSettingsService::KEY_NEWSLETTER_POPUP_IMAGE) ?: asset(\App\Services\SiteSettingsService::DEFAULT_NEWSLETTER_POPUP_IMAGE))
                                                <label class="form-label">{{ $def['label'] }}</label>
                                                @if ($popupImageUrl)
                                                    <div class="mb-3 p-3 border rounded bg-light">
                                                        <img src="{{ $popupImageUrl }}" alt="معاينة نافذة الاشتراك" class="img-thumbnail bg-white" style="max-height: 120px; max-width: 100%; object-fit: cover;">
                                                        <div class="form-check mt-2 mb-0">
                                                            <input type="hidden" name="remove_newsletter_popup_image" value="0">
                                                            <input class="form-check-input" type="checkbox" name="remove_newsletter_popup_image" value="1" id="remove_newsletter_popup_image">
                                                            <label class="form-check-label text-danger" for="remove_newsletter_popup_image">استعادة الصورة الافتراضية</label>
                                                        </div>
                                                    </div>
                                                @endif
                                                <input type="file" class="form-control @error('newsletter_popup_image_file') is-invalid @enderror" name="newsletter_popup_image_file" accept="image/png,image/jpeg,image/webp">
                                                @if (!empty($def['hint']))
                                                    <small class="text-muted d-block mt-1">{{ $def['hint'] }}</small>
                                                @endif
                                                @error('newsletter_popup_image_file')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            @elseif (in_array($key, [\App\Services\SiteSettingsService::KEY_SITE_DESCRIPTION, \App\Services\SiteSettingsService::KEY_SITE_MAINTENANCE_MESSAGE, \App\Services\SiteSettingsService::KEY_SITE_ADDRESS, \App\Services\SiteSettingsService::KEY_SITE_META_DESCRIPTION, \App\Services\SiteSettingsService::KEY_SITE_FOOTER_TEXT, \App\Services\SiteSettingsService::KEY_NEWSLETTER_POPUP_DESCRIPTION], true))
                                                <label class="form-label" for="input-{{ $key }}">{{ $def['label'] }}</label>
                                                <textarea class="form-control @error($key) is-invalid @enderror" name="{{ $key }}" id="input-{{ $key }}" rows="3">{{ $value }}</textarea>
                                                @if (!empty($def['hint']))
                                                    <small class="text-muted">{{ $def['hint'] }}</small>
                                                @endif
                                                @error($key)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            @elseif ($key === \App\Services\SiteSettingsService::KEY_SITE_LOCALE)
                                                <label class="form-label" for="input-{{ $key }}">{{ $def['label'] }}</label>
                                                <select class="form-select @error($key) is-invalid @enderror" name="{{ $key }}" id="input-{{ $key }}">
                                                    <option value="ar" {{ $value === 'ar' ? 'selected' : '' }}>العربية</option>
                                                    <option value="en" {{ $value === 'en' ? 'selected' : '' }}>English</option>
                                                </select>
                                                @if (!empty($def['hint']))
                                                    <small class="text-muted">{{ $def['hint'] }}</small>
                                                @endif
                                                @error($key)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            @else
                                                <label class="form-label" for="input-{{ $key }}">{{ $def['label'] }}</label>
                                                <input type="text" class="form-control @error($key) is-invalid @enderror" name="{{ $key }}" id="input-{{ $key }}" value="{{ $value }}">
                                                @if (!empty($def['hint']))
                                                    <small class="text-muted">{{ $def['hint'] }}</small>
                                                @endif
                                                @error($key)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="card-footer border-top">
                        <button type="submit" class="btn btn-primary">حفظ الإعدادات</button>
                        <a href="{{ route('admin.site-settings.index') }}" class="btn btn-secondary">إلغاء</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('script')
<script>
(function () {
    var picker = document.getElementById('picker-{{ \App\Services\SiteSettingsService::KEY_SITE_PRIMARY_COLOR }}');
    var input = document.getElementById('input-{{ \App\Services\SiteSettingsService::KEY_SITE_PRIMARY_COLOR }}');
    var preview = document.getElementById('preview-{{ \App\Services\SiteSettingsService::KEY_SITE_PRIMARY_COLOR }}');
    if (!picker || !input) return;

    function syncFromPicker() {
        input.value = picker.value;
        if (preview) preview.style.background = picker.value;
    }

    function syncFromInput() {
        var val = input.value.trim();
        if (/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(val)) {
            if (val.length === 4) {
                val = '#' + val.slice(1).split('').map(function (c) { return c + c; }).join('');
            }
            picker.value = val;
            input.value = val;
            if (preview) preview.style.background = val;
        }
    }

    picker.addEventListener('input', syncFromPicker);
    input.addEventListener('change', syncFromInput);
    input.addEventListener('blur', syncFromInput);
})();

(function () {
    var radiusInput = document.getElementById('input-{{ \App\Services\SiteSettingsService::KEY_SITE_BORDER_RADIUS }}');
    var radiusPreview = document.getElementById('preview-{{ \App\Services\SiteSettingsService::KEY_SITE_BORDER_RADIUS }}');
    if (!radiusInput || !radiusPreview) return;

    function syncRadiusPreview() {
        var val = Math.max(0, Math.min(50, parseInt(radiusInput.value, 10) || 0));
        radiusPreview.style.borderRadius = val + 'px';
    }

    radiusInput.addEventListener('input', syncRadiusPreview);
    radiusInput.addEventListener('change', syncRadiusPreview);
})();
</script>
@stop
