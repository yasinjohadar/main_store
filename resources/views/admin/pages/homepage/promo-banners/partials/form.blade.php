<div class="card">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">الصورة @if(empty($banner))<span class="text-danger">*</span>@endif</label>
                <input type="file" name="image" class="form-control" accept="image/*" {{ empty($banner) ? 'required' : '' }}>
                <small class="text-muted">مقاس مقترح: 610×280 بكسل</small>
                @if(!empty($banner?->image_url))
                    <div class="admin-homepage-preview mt-2">
                        <img src="{{ $banner->image_url }}" alt="" class="admin-homepage-preview__img">
                    </div>
                    <div class="form-check mt-2">
                        <input type="checkbox" class="form-check-input" name="remove_image" value="1" id="remove_image">
                        <label class="form-check-label" for="remove_image">حذف الصورة الحالية</label>
                    </div>
                @endif
            </div>
            <div class="col-md-3">
                <label class="form-label">لون الخلفية</label>
                <input type="color" name="background_color" class="form-control form-control-color w-100"
                    value="{{ old('background_color', $banner->background_color ?? '#dfe3e0') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">النمط</label>
                <select name="tone" class="form-select">
                    <option value="light" @selected(old('tone', $banner->tone ?? 'light') === 'light')>فاتح</option>
                    <option value="dark" @selected(old('tone', $banner->tone ?? '') === 'dark')>داكن</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">الشارة</label>
                <input type="text" name="badge" class="form-control" value="{{ old('badge', $banner->badge ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">العنوان <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $banner->title ?? '') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">العنوان الفرعي</label>
                <input type="text" name="subtitle" class="form-control" value="{{ old('subtitle', $banner->subtitle ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">تسمية السعر</label>
                <input type="text" name="price_label" class="form-control" value="{{ old('price_label', $banner->price_label ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">نص السعر</label>
                <input type="text" name="price_text" class="form-control" value="{{ old('price_text', $banner->price_text ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">رابط الزر</label>
                <input type="text" name="button_url" class="form-control" value="{{ old('button_url', $banner->button_url ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">الترتيب</label>
                <input type="number" name="sort_order" class="form-control" min="0"
                    value="{{ old('sort_order', $banner->sort_order ?? 0) }}">
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" class="form-check-input" name="is_active" value="1"
                        {{ old('is_active', $banner->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label">نشط</label>
                </div>
            </div>
        </div>
    </div>
</div>
