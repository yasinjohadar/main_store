(function () {
    'use strict';

    var STATUS_OPTIONS = [
        { value: 'active', label: 'نشط', class: 'admin-table__badge--success' },
        { value: 'draft', label: 'مسودة', class: 'admin-table__badge--secondary' },
        { value: 'archived', label: 'أرشيف', class: 'admin-table__badge--danger' },
    ];

    var categoriesCache = null;
    var activeEditor = null;

    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function getCategories() {
        if (categoriesCache) {
            return categoriesCache;
        }
        var el = document.getElementById('admin-product-categories-json');
        if (!el) {
            return [];
        }
        try {
            categoriesCache = JSON.parse(el.textContent);
        } catch (e) {
            categoriesCache = [];
        }
        return categoriesCache;
    }

    function showAlert(message, type) {
        var alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-' + (type === 'success' ? 'success' : 'danger') + ' alert-dismissible fade show';
        alertDiv.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>';
        var container = document.querySelector('.main-content');
        if (container) {
            container.insertBefore(alertDiv, container.firstChild);
        }
        setTimeout(function () {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 4000);
    }

    function statusLabel(value) {
        var opt = STATUS_OPTIONS.find(function (o) { return o.value === value; });
        return opt ? opt.label : value;
    }

    function statusClass(value) {
        var opt = STATUS_OPTIONS.find(function (o) { return o.value === value; });
        return opt ? opt.class : 'admin-table__badge--secondary';
    }

    function closeActiveEditor() {
        if (activeEditor && typeof activeEditor.cancel === 'function') {
            activeEditor.cancel();
        }
        activeEditor = null;
    }

    function patchField(url, field, value) {
        return fetch(url, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ field: field, value: value }),
        }).then(function (response) {
            return response.json().then(function (data) {
                return { ok: response.ok, status: response.status, data: data };
            }).catch(function () {
                return { ok: response.ok, status: response.status, data: null };
            });
        });
    }

    function applyDisplay(row, display) {
        if (!row || !display) {
            return;
        }

        var nameEl = row.querySelector('.admin-table__editable-name');
        if (nameEl && display.name) {
            nameEl.textContent = display.name;
            nameEl.setAttribute('data-original', display.name);
        }

        var priceEl = row.querySelector('.admin-table__editable--price');
        if (priceEl && display.price_formatted) {
            priceEl.textContent = display.price_formatted;
            priceEl.setAttribute('data-original', display.price);
        }

        var categoryEl = row.querySelector('.admin-table__editable--category');
        if (categoryEl) {
            categoryEl.textContent = display.category_name || '—';
            categoryEl.setAttribute('data-original', display.category_id || '');
        }

        var statusEl = row.querySelector('.admin-table__editable--status');
        if (statusEl && display.status) {
            statusEl.textContent = display.status_label;
            statusEl.setAttribute('data-original', display.status);
            statusEl.setAttribute('data-status-label', display.status_label);
            statusEl.className = 'admin-table__badge admin-table__editable admin-table__editable--status ' + display.status_class;
        }
    }

    function saveInline(el, field, value, originalValue) {
        var url = el.getAttribute('data-update-url');
        var row = el.closest('[data-product-row]');

        if (!url || String(value) === String(originalValue)) {
            return Promise.resolve(null);
        }

        el.classList.add('admin-table__editable--saving');

        return patchField(url, field, value)
            .then(function (result) {
                if (result.ok && result.data && result.data.success) {
                    applyDisplay(row, result.data.display);
                    return result.data;
                }

                var message = (result.data && result.data.message)
                    || (result.data && result.data.errors && Object.values(result.data.errors).flat().join(' '))
                    || 'فشل حفظ التعديل.';
                showAlert(message, 'error');
                throw new Error(message);
            })
            .finally(function () {
                el.classList.remove('admin-table__editable--saving');
            });
    }

    function startTextEdit(el, field, options) {
        closeActiveEditor();

        var original = el.getAttribute('data-original') || el.textContent.trim();
        var input = document.createElement('input');
        input.type = options && options.type === 'number' ? 'number' : 'text';
        input.className = 'form-control form-control-sm admin-table__editable-input';
        input.value = original;
        if (input.type === 'number') {
            input.min = '0';
            input.step = '0.01';
        }

        var parent = el.parentNode;
        var wrapper = document.createElement('span');
        wrapper.className = 'admin-table__editable--editing d-inline-block';
        wrapper.style.minWidth = field === 'name' ? '160px' : '90px';

        el.style.display = 'none';
        parent.insertBefore(wrapper, el);
        wrapper.appendChild(input);
        input.focus();
        if (input.type !== 'number') {
            input.select();
        }

        var committed = false;

        function finish(revert) {
            if (committed) {
                return;
            }
            committed = true;
            wrapper.remove();
            el.style.display = '';
            if (activeEditor && activeEditor.el === el) {
                activeEditor = null;
            }
            if (revert) {
                return;
            }
        }

        function commit() {
            var newValue = input.value.trim();
            if (input.type === 'number' && newValue === '') {
                finish(true);
                return;
            }
            finish(false);
            saveInline(el, field, input.type === 'number' ? parseFloat(newValue) : newValue, original).catch(function () {
                if (field === 'name') {
                    el.textContent = original;
                } else if (field === 'price') {
                    el.textContent = original;
                }
            });
        }

        function cancel() {
            if (!committed) {
                committed = true;
                wrapper.remove();
                el.style.display = '';
                if (activeEditor && activeEditor.el === el) {
                    activeEditor = null;
                }
            }
        }

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                commit();
            } else if (e.key === 'Escape') {
                e.preventDefault();
                cancel();
            }
        });

        input.addEventListener('blur', function () {
            setTimeout(function () {
                if (!committed) {
                    commit();
                }
            }, 120);
        });

        activeEditor = { el: el, cancel: cancel };
    }

    function startCategoryEdit(el) {
        closeActiveEditor();

        var original = el.getAttribute('data-original') || '';
        var select = document.createElement('select');
        select.className = 'form-select form-select-sm admin-table__editable-input';

        var emptyOpt = document.createElement('option');
        emptyOpt.value = '';
        emptyOpt.textContent = '— بدون تصنيف —';
        select.appendChild(emptyOpt);

        getCategories().forEach(function (cat) {
            var opt = document.createElement('option');
            opt.value = String(cat.id);
            opt.textContent = cat.name;
            if (String(cat.id) === String(original)) {
                opt.selected = true;
            }
            select.appendChild(opt);
        });

        var parent = el.parentNode;
        var wrapper = document.createElement('span');
        wrapper.className = 'admin-table__editable--editing d-inline-block';
        wrapper.style.minWidth = '140px';

        el.style.display = 'none';
        parent.insertBefore(wrapper, el);
        wrapper.appendChild(select);
        select.focus();

        var committed = false;

        function cleanup() {
            wrapper.remove();
            el.style.display = '';
            if (activeEditor && activeEditor.el === el) {
                activeEditor = null;
            }
        }

        function cancel() {
            if (!committed) {
                committed = true;
                cleanup();
            }
        }

        select.addEventListener('change', function () {
            if (committed) {
                return;
            }
            committed = true;
            var newValue = select.value;
            cleanup();
            saveInline(el, 'category_id', newValue || null, original).catch(function () {});
        });

        select.addEventListener('blur', function () {
            setTimeout(function () {
                if (!committed) {
                    cancel();
                }
            }, 150);
        });

        select.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                e.preventDefault();
                cancel();
            }
        });

        activeEditor = { el: el, cancel: cancel };
    }

    function startStatusEdit(el) {
        closeActiveEditor();

        var original = el.getAttribute('data-original');
        var url = el.getAttribute('data-update-url');
        var select = document.createElement('select');
        select.className = 'form-select form-select-sm admin-table__editable-input';

        STATUS_OPTIONS.forEach(function (opt) {
            var option = document.createElement('option');
            option.value = opt.value;
            option.textContent = opt.label;
            if (opt.value === original) {
                option.selected = true;
            }
            select.appendChild(option);
        });

        var parent = el.parentNode;
        var wrapper = document.createElement('span');
        wrapper.className = 'admin-table__editable--editing d-inline-block';
        wrapper.style.minWidth = '110px';

        el.style.display = 'none';
        parent.insertBefore(wrapper, el);
        wrapper.appendChild(select);
        select.focus();

        var committed = false;

        function cleanup() {
            wrapper.remove();
            el.style.display = '';
            if (activeEditor && activeEditor.el === el) {
                activeEditor = null;
            }
        }

        function cancel() {
            if (!committed) {
                committed = true;
                cleanup();
            }
        }

        select.addEventListener('change', function () {
            var newStatus = select.value;
            if (committed) {
                return;
            }

            if (newStatus === original) {
                cancel();
                return;
            }

            committed = true;
            cleanup();

            if (!window.AdminConfirmAction) {
                saveInline(el, 'status', newStatus, original);
                return;
            }

            var newLabel = statusLabel(newStatus);

            window.AdminConfirmAction.open({
                url: url,
                method: 'PATCH',
                body: { field: 'status', value: newStatus },
                title: 'تغيير حالة المنتج',
                message: 'هل تريد تغيير حالة المنتج إلى:',
                item: newLabel,
                hint: 'سيُطبَّق التغيير فوراً على المنتج في المتجر.',
                confirmText: 'نعم، غيّر',
                cancelText: 'إلغاء',
                icon: 'las la-exchange-alt',
                onSuccess: function (data) {
                    if (data && data.display) {
                        applyDisplay(el.closest('[data-product-row]'), data.display);
                    }
                    showAlert('تم تحديث حالة المنتج.', 'success');
                },
                onError: function (message) {
                    showAlert(message, 'error');
                },
            }).catch(function () {});
        });

        select.addEventListener('blur', function () {
            setTimeout(function () {
                if (!committed) {
                    cancel();
                }
            }, 150);
        });

        select.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                e.preventDefault();
                cancel();
            }
        });

        activeEditor = { el: el, cancel: cancel };
    }

    function handleNameEditClick(e) {
        var btn = e.target.closest('.admin-table__name-edit');
        if (!btn) {
            return;
        }
        e.preventDefault();
        e.stopPropagation();
        var nameEl = btn.closest('.admin-table__name-row');
        nameEl = nameEl ? nameEl.querySelector('.admin-table__editable-name') : null;
        if (nameEl) {
            startTextEdit(nameEl, 'name', { type: 'text' });
        }
    }

    function handleClick(e) {
        if (e.target.closest('.admin-table__editable-input')) {
            return;
        }

        var priceEl = e.target.closest('.admin-table__editable--price');
        if (priceEl) {
            e.preventDefault();
            startTextEdit(priceEl, 'price', { type: 'number' });
            return;
        }

        var categoryEl = e.target.closest('.admin-table__editable--category');
        if (categoryEl) {
            e.preventDefault();
            startCategoryEdit(categoryEl);
            return;
        }

        var statusEl = e.target.closest('.admin-table__editable--status');
        if (statusEl) {
            e.preventDefault();
            startStatusEdit(statusEl);
        }
    }

    function handleKeydown(e) {
        if (e.key !== 'Enter') {
            return;
        }
        var editable = e.target.closest('.admin-table__editable[tabindex="0"]');
        if (editable && !e.target.closest('.admin-table__editable--editing')) {
            e.preventDefault();
            editable.click();
        }
    }

    function init() {
        document.addEventListener('click', handleNameEditClick);
        document.addEventListener('click', handleClick);
        document.addEventListener('keydown', handleKeydown);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    document.addEventListener('admin:table-updated', function () {
        closeActiveEditor();
    });
})();
