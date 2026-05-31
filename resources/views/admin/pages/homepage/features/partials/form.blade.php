@php
    $tones = ['blue' => 'أزرق', 'teal' => 'تركواز', 'amber' => 'كهرماني', 'violet' => 'بنفسجي'];
@endphp

<div class="card">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">أيقونة <span class="text-danger">*</span></label>
                <input type="text" name="icon" class="form-control" value="{{ old('icon', $feature->icon ?? '') }}"
                    placeholder="w-icon-truck" required>
                <small class="text-muted">مثال: w-icon-truck, w-icon-bag</small>
            </div>
            <div class="col-md-4">
                <label class="form-label">اللون</label>
                <select name="tone" class="form-select">
                    @foreach($tones as $value => $label)
                        <option value="{{ $value }}" @selected(old('tone', $feature->tone ?? 'blue') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">الترتيب</label>
                <input type="number" name="sort_order" class="form-control" min="0"
                    value="{{ old('sort_order', $feature->sort_order ?? 0) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">العنوان <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $feature->title ?? '') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">الوصف</label>
                <input type="text" name="description" class="form-control"
                    value="{{ old('description', $feature->description ?? '') }}">
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" class="form-check-input" name="is_active" value="1"
                        {{ old('is_active', $feature->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label">نشط</label>
                </div>
            </div>
        </div>
    </div>
</div>
