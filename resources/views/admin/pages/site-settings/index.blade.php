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
                    <p class="text-muted mb-0 small">اسم الموقع، الشعار، التواصل، وضع الصيانة، اللغة والمنطقة، ومحركات البحث.</p>
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
                                                <label class="form-label">{{ $def['label'] }}</label>
                                                @if ($value && \Illuminate\Support\Facades\Storage::disk(config('filesystems.default', 'public'))->exists($value))
                                                    <div class="mb-2">
                                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk(config('filesystems.default', 'public'))->url($value) }}" alt="الشعار" class="img-thumbnail" style="max-height: 60px;">
                                                    </div>
                                                @endif
                                                <input type="file" class="form-control @error('site_logo_file') is-invalid @enderror" name="site_logo_file" accept="image/*">
                                                @if (!empty($def['hint']))
                                                    <small class="text-muted">{{ $def['hint'] }}</small>
                                                @endif
                                                @error('site_logo_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            @elseif ($key === \App\Services\SiteSettingsService::KEY_SITE_FAVICON)
                                                <label class="form-label">{{ $def['label'] }}</label>
                                                @if ($value && \Illuminate\Support\Facades\Storage::disk(config('filesystems.default', 'public'))->exists($value))
                                                    <div class="mb-2">
                                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk(config('filesystems.default', 'public'))->url($value) }}" alt="Favicon" class="img-thumbnail" style="max-height: 32px;">
                                                    </div>
                                                @endif
                                                <input type="file" class="form-control @error('site_favicon_file') is-invalid @enderror" name="site_favicon_file" accept="image/*">
                                                @if (!empty($def['hint']))
                                                    <small class="text-muted">{{ $def['hint'] }}</small>
                                                @endif
                                                @error('site_favicon_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            @elseif (in_array($key, [\App\Services\SiteSettingsService::KEY_SITE_DESCRIPTION, \App\Services\SiteSettingsService::KEY_SITE_MAINTENANCE_MESSAGE, \App\Services\SiteSettingsService::KEY_SITE_ADDRESS, \App\Services\SiteSettingsService::KEY_SITE_META_DESCRIPTION, \App\Services\SiteSettingsService::KEY_SITE_FOOTER_TEXT], true))
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
