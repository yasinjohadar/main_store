@php
    use App\Support\SiteTheme;
    $slide = $slide ?? null;
    $defaults = [
        'title' => old('title', $slide->title ?? ''),
        'subtitle' => old('subtitle', $slide->subtitle ?? ''),
        'description' => old('description', $slide->description ?? ''),
        'badge' => old('badge', $slide->badge ?? ''),
        'background_color' => old('background_color', $slide->background_color ?? '#e8edf3'),
        'accent_color' => old('accent_color', $slide->accent_color ?? SiteTheme::primary()),
        'button_text' => old('button_text', $slide->button_text ?? 'تسوق الآن'),
        'button_url' => old('button_url', $slide->button_url ?? route('frontend.shop.index')),
        'sort_order' => old('sort_order', $slide->sort_order ?? 0),
        'is_active' => old('is_active', $slide->is_active ?? true),
    ];
    $bgPreview = $slide?->background_image_url;
    $productPreview = $slide?->product_image_url;
@endphp

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent">
                <h6 class="mb-0">النصوص</h6>
            </div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">الشارة <small class="text-muted">(اختياري)</small></label>
                    <input type="text" class="form-control" name="badge" id="slide_badge" value="{{ $defaults['badge'] }}" maxlength="100" data-slide-preview>
                    <small class="text-muted">مثال: خصم 30%</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">العنوان الفرعي</label>
                    <input type="text" class="form-control" name="subtitle" id="slide_subtitle" value="{{ $defaults['subtitle'] }}" maxlength="255" data-slide-preview>
                </div>
                <div class="col-12">
                    <label class="form-label">العنوان الرئيسي <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" id="slide_title" value="{{ $defaults['title'] }}" required maxlength="255" data-slide-preview>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">الوصف</label>
                    <textarea class="form-control" name="description" id="slide_description" rows="3" maxlength="1000" data-slide-preview>{{ $defaults['description'] }}</textarea>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent">
                <h6 class="mb-0">الصور</h6>
            </div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">صورة الخلفية @if(!$slide)<span class="text-danger">*</span>@endif</label>
                    @if($slide && $bgPreview)
                        <div class="mb-2">
                            <img src="{{ $bgPreview }}" alt="" class="img-thumbnail" style="max-height: 80px;" id="slide_bg_existing">
                            <div class="form-check mt-2">
                                <input type="hidden" name="remove_background_image" value="0">
                                <input class="form-check-input" type="checkbox" name="remove_background_image" value="1" id="remove_background_image">
                                <label class="form-check-label text-danger" for="remove_background_image">حذف صورة الخلفية</label>
                            </div>
                        </div>
                    @endif
                    <input type="file" class="form-control @error('background_image') is-invalid @enderror" name="background_image" id="slide_background_image" accept="image/*" data-slide-bg-input>
                    <small class="text-muted">مقاس مقترح: 1920×700 تقريباً</small>
                    @error('background_image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">صورة المنتج <small class="text-muted">(اختياري)</small></label>
                    @if($slide && $productPreview)
                        <div class="mb-2">
                            <img src="{{ $productPreview }}" alt="" class="img-thumbnail" style="max-height: 80px;" id="slide_product_existing">
                            <div class="form-check mt-2">
                                <input type="hidden" name="remove_product_image" value="0">
                                <input class="form-check-input" type="checkbox" name="remove_product_image" value="1" id="remove_product_image">
                                <label class="form-check-label text-danger" for="remove_product_image">حذف صورة المنتج</label>
                            </div>
                        </div>
                    @endif
                    <input type="file" class="form-control @error('product_image') is-invalid @enderror" name="product_image" id="slide_product_image" accept="image/*" data-slide-product-input>
                    <small class="text-muted">PNG شفاف يُفضّل للمنتج</small>
                    @error('product_image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent">
                <h6 class="mb-0">الألوان والزر</h6>
            </div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">لون الخلفية</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="color" class="form-control form-control-color" id="picker_bg_color" value="{{ $defaults['background_color'] }}">
                        <input type="text" class="form-control" name="background_color" id="slide_background_color" value="{{ $defaults['background_color'] }}" maxlength="7" dir="ltr" data-slide-preview>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">لون التمييز</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="color" class="form-control form-control-color" id="picker_accent_color" value="{{ $defaults['accent_color'] }}">
                        <input type="text" class="form-control" name="accent_color" id="slide_accent_color" value="{{ $defaults['accent_color'] }}" maxlength="7" dir="ltr" data-slide-preview>
                    </div>
                    <small class="text-muted">يُستخدم للشارة والزر</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">نص الزر <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('button_text') is-invalid @enderror" name="button_text" id="slide_button_text" value="{{ $defaults['button_text'] }}" required data-slide-preview>
                    @error('button_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">رابط الزر <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('button_url') is-invalid @enderror" name="button_url" id="slide_button_url" value="{{ $defaults['button_url'] }}" required dir="ltr" placeholder="/shop أو https://..." data-slide-preview>
                    @error('button_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">الترتيب</label>
                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" name="sort_order" value="{{ $defaults['sort_order'] }}" min="0">
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="slide_is_active" {{ filter_var($defaults['is_active'], FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                        <label class="form-check-label" for="slide_is_active">نشط — يظهر في الرئيسية</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="admin-slide-preview sticky-top" style="top: 5rem;">
            <p class="fw-semibold mb-2">معاينة الشريحة</p>
            <div class="admin-slide-preview__frame" id="slide_preview_frame" style="--preview-bg: {{ $defaults['background_color'] }}; --preview-accent: {{ $defaults['accent_color'] }};">
                <div class="admin-slide-preview__bg" id="slide_preview_bg" @if($bgPreview) style="background-image: url('{{ $bgPreview }}');" @endif></div>
                <div class="admin-slide-preview__overlay"></div>
                @if($productPreview)
                    <img src="{{ $productPreview }}" alt="" class="admin-slide-preview__product" id="slide_preview_product">
                @else
                    <img src="" alt="" class="admin-slide-preview__product d-none" id="slide_preview_product">
                @endif
                <div class="admin-slide-preview__content">
                    <span class="admin-slide-preview__badge" id="slide_preview_badge">{{ $defaults['badge'] ?: 'شارة' }}</span>
                    <p class="admin-slide-preview__subtitle" id="slide_preview_subtitle">{{ $defaults['subtitle'] ?: 'عنوان فرعي' }}</p>
                    <h3 class="admin-slide-preview__title" id="slide_preview_title">{{ $defaults['title'] ?: 'عنوان الشريحة' }}</h3>
                    <p class="admin-slide-preview__desc" id="slide_preview_desc">{{ $defaults['description'] ?: 'وصف الشريحة يظهر هنا' }}</p>
                    <span class="admin-slide-preview__btn" id="slide_preview_btn">{{ $defaults['button_text'] }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
