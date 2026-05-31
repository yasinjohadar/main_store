<div class="card">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">الشعار @if(empty($brand))<span class="text-danger">*</span>@endif</label>
                <input type="file" name="logo" class="form-control" accept="image/*" {{ empty($brand) ? 'required' : '' }}>
                <small class="text-muted">مقاس مقترح: 410×186 بكسل، PNG شفاف</small>
                @if(!empty($brand?->logo_url))
                    <div class="admin-homepage-preview mt-2">
                        <img src="{{ $brand->logo_url }}" alt="" class="admin-homepage-preview__img admin-homepage-preview__img--logo">
                    </div>
                    <div class="form-check mt-2">
                        <input type="checkbox" class="form-check-input" name="remove_logo" value="1" id="remove_logo">
                        <label class="form-check-label" for="remove_logo">حذف الشعار الحالي</label>
                    </div>
                @endif
            </div>
            <div class="col-md-6">
                <label class="form-label">الاسم <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $brand->name ?? '') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">الرابط</label>
                <input type="text" name="url" class="form-control" value="{{ old('url', $brand->url ?? '') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">الترتيب</label>
                <input type="number" name="sort_order" class="form-control" min="0"
                    value="{{ old('sort_order', $brand->sort_order ?? 0) }}">
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" class="form-check-input" name="is_active" value="1"
                        {{ old('is_active', $brand->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label">نشط</label>
                </div>
            </div>
        </div>
    </div>
</div>
