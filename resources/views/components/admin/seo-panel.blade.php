@props([
    'prefix' => '',
    'entityType' => 'product',
    'values' => [],
    'titleField' => 'name',
    'contentField' => 'description',
])

@php
    $p = $prefix ? $prefix . '_' : '';
    $v = fn ($key, $default = '') => old($p . $key, $values[$key] ?? $default);
@endphp

<div class="card border mt-4 store-seo-panel" data-entity-type="{{ $entityType }}">
    <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="mb-0"><i class="bi bi-search me-2"></i>تحسين محركات البحث (SEO)</h6>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-seo-ai-suggest>
                <i class="bi bi-stars me-1"></i>توليد بالذكاء الاصطناعي
            </button>
            <span class="badge bg-primary" data-seo-score-badge>—</span>
        </div>
    </div>
    <div class="card-body">
        <ul class="list-unstyled small mb-3 d-none" data-seo-checklist></ul>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Meta Title <small class="text-muted">(50–60 حرف)</small></label>
                <input type="text" name="{{ $p }}meta_title" class="form-control seo-field" maxlength="255"
                       value="{{ $v('meta_title') }}" data-seo-field="meta_title">
                <div class="form-text"><span data-seo-title-count>0</span> حرف</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Focus Keyword</label>
                <input type="text" name="{{ $p }}focus_keyword" class="form-control seo-field"
                       value="{{ $v('focus_keyword') }}" data-seo-field="focus_keyword">
            </div>
            <div class="col-12">
                <label class="form-label">Meta Description <small class="text-muted">(120–160 حرف)</small></label>
                <textarea name="{{ $p }}meta_description" class="form-control seo-field" rows="2"
                          data-seo-field="meta_description">{{ $v('meta_description') }}</textarea>
                <div class="form-text"><span data-seo-desc-count>0</span> حرف</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Meta Keywords</label>
                <input type="text" name="{{ $p }}meta_keywords" class="form-control"
                       value="{{ $v('meta_keywords') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Canonical URL</label>
                <input type="url" name="{{ $p }}canonical_url" class="form-control seo-field"
                       value="{{ $v('canonical_url') }}" data-seo-field="canonical_url"
                       placeholder="يُولَّد تلقائياً إن تُرك فارغاً">
            </div>
            <div class="col-md-4">
                <label class="form-label">Robots</label>
                <select name="{{ $p }}robots_meta" class="form-select seo-field" data-seo-field="robots_meta">
                    <option value="index,follow" @selected($v('robots_meta', 'index,follow') === 'index,follow')>index, follow</option>
                    <option value="noindex,follow" @selected($v('robots_meta') === 'noindex,follow')>noindex, follow</option>
                    <option value="index,nofollow" @selected($v('robots_meta') === 'index,nofollow')>index, nofollow</option>
                    <option value="noindex,nofollow" @selected($v('robots_meta') === 'noindex,nofollow')>noindex, nofollow</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Schema Type</label>
                <input type="text" name="{{ $p }}schema_type" class="form-control"
                       value="{{ $v('schema_type', $entityType === 'product' ? 'Product' : ($entityType === 'blog' ? 'Article' : 'WebPage')) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">OG Image</label>
                <input type="text" name="{{ $p }}og_image" class="form-control seo-field"
                       value="{{ $v('og_image') }}" data-seo-field="og_image" placeholder="مسار أو URL">
            </div>
            <div class="col-md-6">
                <label class="form-label">OG Title</label>
                <input type="text" name="{{ $p }}og_title" class="form-control" value="{{ $v('og_title') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">OG Description</label>
                <input type="text" name="{{ $p }}og_description" class="form-control" value="{{ $v('og_description') }}">
            </div>
        </div>

        <div class="mt-3 p-3 bg-light rounded serp-preview">
            <div class="small text-muted mb-1">معاينة Google</div>
            <div class="text-primary serp-preview__title" data-serp-title>{{ $v('meta_title', 'عنوان الصفحة') }}</div>
            <div class="text-success small serp-preview__url">{{ url('/') }}/...</div>
            <div class="small serp-preview__desc" data-serp-desc>{{ $v('meta_description', 'وصف الصفحة يظهر هنا...') }}</div>
        </div>
    </div>
</div>

@once
    @push('scripts')
    <script>
        (function () {
            var scoreUrl = @json(route('admin.seo.score'));
            var suggestUrl = @json(route('admin.seo.suggest'));
            var csrf = @json(csrf_token());

            document.querySelectorAll('.store-seo-panel').forEach(function (panel) {
                var titleInput = panel.querySelector('[data-seo-field="meta_title"]');
                var descInput = panel.querySelector('[data-seo-field="meta_description"]');
                var focusInput = panel.querySelector('[data-seo-field="focus_keyword"]');
                var ogImageInput = panel.querySelector('[data-seo-field="og_image"]');
                var canonicalInput = panel.querySelector('[data-seo-field="canonical_url"]');
                var serpTitle = panel.querySelector('[data-serp-title]');
                var serpDesc = panel.querySelector('[data-serp-desc]');
                var titleCount = panel.querySelector('[data-seo-title-count]');
                var descCount = panel.querySelector('[data-seo-desc-count]');
                var scoreBadge = panel.querySelector('[data-seo-score-badge]');
                var checklist = panel.querySelector('[data-seo-checklist]');
                var aiBtn = panel.querySelector('[data-seo-ai-suggest]');
                var entityType = panel.getAttribute('data-entity-type') || 'product';
                var scoreTimer;

                function updatePreview() {
                    var t = titleInput ? titleInput.value : '';
                    var d = descInput ? descInput.value : '';
                    if (serpTitle) serpTitle.textContent = t || 'عنوان الصفحة';
                    if (serpDesc) serpDesc.textContent = d || 'وصف الصفحة...';
                    if (titleCount) titleCount.textContent = t.length;
                    if (descCount) descCount.textContent = d.length;
                }

                function fetchScore() {
                    if (!scoreUrl) return;
                    clearTimeout(scoreTimer);
                    scoreTimer = setTimeout(function () {
                        fetch(scoreUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                meta_title: titleInput ? titleInput.value : '',
                                meta_description: descInput ? descInput.value : '',
                                focus_keyword: focusInput ? focusInput.value : '',
                                og_image: ogImageInput ? ogImageInput.value : '',
                                canonical_url: canonicalInput ? canonicalInput.value : '',
                            }),
                        })
                        .then(function (r) { return r.json(); })
                        .then(function (data) {
                            if (scoreBadge) {
                                scoreBadge.textContent = (data.score || 0) + '/100';
                                scoreBadge.className = 'badge ' + (data.score >= 70 ? 'bg-success' : (data.score >= 40 ? 'bg-warning text-dark' : 'bg-danger'));
                            }
                            if (checklist && data.checks) {
                                checklist.classList.remove('d-none');
                                checklist.innerHTML = data.checks.map(function (c) {
                                    return '<li class="' + (c.passed ? 'text-success' : 'text-danger') + '">' +
                                        (c.passed ? '✓' : '✗') + ' ' + c.label + ' <span class="text-muted">(' + c.hint + ')</span></li>';
                                }).join('');
                            }
                        })
                        .catch(function () {});
                    }, 400);
                }

                panel.querySelectorAll('.seo-field').forEach(function (el) {
                    el.addEventListener('input', function () {
                        updatePreview();
                        fetchScore();
                    });
                    el.addEventListener('change', fetchScore);
                });

                if (aiBtn) {
                    aiBtn.addEventListener('click', function () {
                        var form = panel.closest('form');
                        var titleEl = form ? (form.querySelector('[name="title"]') || form.querySelector('[name="name"]')) : null;
                        var contentEl = form ? (form.querySelector('[name="content"]') || form.querySelector('[name="description"]') || form.querySelector('[name="short_description"]')) : null;
                        var title = titleEl ? titleEl.value : (titleInput ? titleInput.value : '');
                        if (!title) {
                            alert('أدخل عنوان المنتج/المقال أولاً');
                            return;
                        }
                        aiBtn.disabled = true;
                        fetch(suggestUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                entity_type: entityType === 'blog' ? 'blog' : entityType,
                                title: title,
                                content: contentEl ? contentEl.value : '',
                            }),
                        })
                        .then(function (r) { return r.json(); })
                        .then(function (data) {
                            if (titleInput && data.meta_title) titleInput.value = data.meta_title;
                            if (descInput && data.meta_description) descInput.value = data.meta_description;
                            if (focusInput && data.focus_keyword) focusInput.value = data.focus_keyword;
                            var ogTitle = panel.querySelector('[name$="og_title"]');
                            var ogDesc = panel.querySelector('[name$="og_description"]');
                            var schemaType = panel.querySelector('[name$="schema_type"]');
                            if (ogTitle && data.og_title) ogTitle.value = data.og_title;
                            if (ogDesc && data.og_description) ogDesc.value = data.og_description;
                            if (schemaType && data.schema_type) schemaType.value = data.schema_type;
                            updatePreview();
                            fetchScore();
                        })
                        .finally(function () { aiBtn.disabled = false; });
                    });
                }

                updatePreview();
                fetchScore();
            });
        })();
    </script>
    @endpush
@endonce
