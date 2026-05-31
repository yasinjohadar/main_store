(function () {
    'use strict';

    var TYPE_OPTIONS = [
        { value: 'percentage', label: 'نسبة مئوية' },
        { value: 'fixed_amount', label: 'مبلغ ثابت' },
        { value: 'buy_x_get_y', label: 'اشترِ واحصل على' },
    ];

    var STATUS_OPTIONS = [
        { value: 'active', label: 'نشط', class: 'admin-table__badge--success' },
        { value: 'inactive', label: 'غير نشط', class: 'admin-table__badge--secondary' },
        { value: 'expired', label: 'منتهي', class: 'admin-table__badge--danger' },
    ];

    var APPLICABLE_OPTIONS = [
        { value: 'entire_store', label: 'المتجر كامل' },
        { value: 'specific_products', label: 'منتجات محددة' },
        { value: 'specific_categories', label: 'تصنيفات محددة' },
    ];

    var activeEditor = null;

    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
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

        var codeEl = row.querySelector('.admin-table__editable--code');
        if (codeEl) {
            codeEl.textContent = display.code;
            codeEl.setAttribute('data-original', display.code);
        }

        var nameEl = row.querySelector('.admin-table__editable--name');
        if (nameEl) {
            nameEl.textContent = display.name;
            nameEl.setAttribute('data-original', display.name);
        }

        var typeEl = row.querySelector('.admin-table__editable--type');
        if (typeEl) {
            typeEl.textContent = display.type_label;
            typeEl.setAttribute('data-original', display.type);
        }

        var valueEl = row.querySelector('.admin-table__editable--value');
        if (valueEl) {
            valueEl.textContent = display.value_formatted;
            valueEl.setAttribute('data-original', display.value);
            valueEl.setAttribute('data-coupon-type', display.type);
        }

        var limitEl = row.querySelector('.admin-table__editable--usage-limit');
        if (limitEl) {
            limitEl.textContent = display.usage_limit !== null && display.usage_limit !== undefined && display.usage_limit !== ''
                ? display.usage_limit
                : '—';
            limitEl.setAttribute('data-original', display.usage_limit ?? '');
        }

        var statusEl = row.querySelector('.admin-table__editable--status');
        if (statusEl) {
            statusEl.textContent = display.status_label;
            statusEl.setAttribute('data-original', display.status);
            statusEl.setAttribute('data-status-label', display.status_label);
            statusEl.className = 'admin-table__badge admin-table__editable admin-table__editable--status ' + display.status_class;
        }

        var startsEl = row.querySelector('.admin-table__editable--starts-at');
        if (startsEl) {
            startsEl.textContent = display.starts_at_display;
            startsEl.setAttribute('data-original', display.starts_at || '');
        }

        var expiresEl = row.querySelector('.admin-table__editable--expires-at');
        if (expiresEl) {
            expiresEl.textContent = display.expires_at_display;
            expiresEl.setAttribute('data-original', display.expires_at || '');
        }

        var applicableEl = row.querySelector('.admin-table__editable--applicable');
        if (applicableEl) {
            applicableEl.textContent = display.applicable_to_label;
            applicableEl.setAttribute('data-original', display.applicable_to);
        }
    }

    function saveInline(el, field, value, originalValue, options) {
        var url = el.getAttribute('data-update-url');
        var row = el.closest('[data-coupon-row]');
        options = options || {};

        if (!url || String(value) === String(originalValue)) {
            return Promise.resolve(null);
        }

        el.classList.add('admin-table__editable--saving');

        return patchField(url, field, value)
            .then(function (result) {
                if (result.ok && result.data && result.data.success) {
                    applyDisplay(row, result.data.display);
                    if (options.showSuccess !== false) {
                        showAlert('تم حفظ التعديل.', 'success');
                    }
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
        if (original === '—') {
            original = '';
        }

        var input = document.createElement('input');
        input.type = options && options.type === 'number' ? 'number' : (options && options.type === 'date' ? 'date' : 'text');
        input.className = 'form-control form-control-sm admin-table__editable-input';
        input.value = original;

        if (input.type === 'number') {
            input.min = '0';
            input.step = options && options.step ? options.step : '0.01';
        }

        var parent = el.parentNode;
        var wrapper = document.createElement('span');
        wrapper.className = 'admin-table__editable--editing d-inline-block';
        wrapper.style.minWidth = options && options.minWidth ? options.minWidth : '90px';

        el.style.display = 'none';
        parent.insertBefore(wrapper, el);
        wrapper.appendChild(input);
        input.focus();
        if (input.type === 'text') {
            input.select();
        }

        var committed = false;

        function cleanup(revert) {
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
            if (input.type === 'number' && newValue === '' && field !== 'usage_limit') {
                cleanup(true);
                return;
            }
            cleanup(false);

            var payloadValue = input.type === 'number'
                ? (newValue === '' ? null : parseFloat(newValue))
                : (newValue === '' ? null : newValue);

            saveInline(el, field, payloadValue, original, { showSuccess: false }).catch(function () {});
        }

        function cancel() {
            if (!committed) {
                cleanup(true);
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

    function startSelectEdit(el, field, optionsList, confirmConfig) {
        closeActiveEditor();

        var original = el.getAttribute('data-original') || '';
        var url = el.getAttribute('data-update-url');
        var select = document.createElement('select');
        select.className = 'form-select form-select-sm admin-table__editable-input';

        optionsList.forEach(function (opt) {
            var option = document.createElement('option');
            option.value = opt.value;
            option.textContent = opt.label;
            if (String(opt.value) === String(original)) {
                option.selected = true;
            }
            select.appendChild(option);
        });

        var parent = el.parentNode;
        var wrapper = document.createElement('span');
        wrapper.className = 'admin-table__editable--editing d-inline-block';
        wrapper.style.minWidth = '130px';

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

        function doSave(newValue, newLabel) {
            if (confirmConfig && window.AdminConfirmAction) {
                window.AdminConfirmAction.open({
                    url: url,
                    method: 'PATCH',
                    body: { field: field, value: newValue },
                    title: confirmConfig.title,
                    message: confirmConfig.message,
                    item: newLabel,
                    hint: confirmConfig.hint || '',
                    confirmText: confirmConfig.confirmText || 'نعم، غيّر',
                    cancelText: 'إلغاء',
                    icon: confirmConfig.icon || 'las la-exchange-alt',
                    onSuccess: function (data) {
                        if (data && data.display) {
                            applyDisplay(el.closest('[data-coupon-row]'), data.display);
                        }
                        showAlert('تم حفظ التعديل.', 'success');
                    },
                    onError: function (message) {
                        showAlert(message, 'error');
                    },
                }).catch(function () {});
            } else {
                saveInline(el, field, newValue, original).catch(function () {});
            }
        }

        select.addEventListener('change', function () {
            if (committed) {
                return;
            }

            var newValue = select.value;
            if (String(newValue) === String(original)) {
                cancel();
                return;
            }

            committed = true;
            cleanup();

            var selected = optionsList.find(function (o) { return String(o.value) === String(newValue); });
            var newLabel = selected ? selected.label : newValue;
            doSave(newValue, newLabel);
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

    function handleClick(e) {
        if (e.target.closest('.admin-table__editable-input')) {
            return;
        }

        var map = [
            { sel: '.admin-table__editable--code', field: 'code', type: 'text', minWidth: '100px' },
            { sel: '.admin-table__editable--name', field: 'name', type: 'text', minWidth: '140px' },
            { sel: '.admin-table__editable--value', field: 'value', type: 'number', minWidth: '80px' },
            { sel: '.admin-table__editable--usage-limit', field: 'usage_limit', type: 'number', minWidth: '70px', step: '1' },
            { sel: '.admin-table__editable--starts-at', field: 'starts_at', type: 'date', minWidth: '130px' },
            { sel: '.admin-table__editable--expires-at', field: 'expires_at', type: 'date', minWidth: '130px' },
        ];

        for (var i = 0; i < map.length; i++) {
            var el = e.target.closest(map[i].sel);
            if (el) {
                e.preventDefault();
                startTextEdit(el, map[i].field, map[i]);
                return;
            }
        }

        var typeEl = e.target.closest('.admin-table__editable--type');
        if (typeEl) {
            e.preventDefault();
            startSelectEdit(typeEl, 'type', TYPE_OPTIONS);
            return;
        }

        var applicableEl = e.target.closest('.admin-table__editable--applicable');
        if (applicableEl) {
            e.preventDefault();
            startSelectEdit(applicableEl, 'applicable_to', APPLICABLE_OPTIONS, {
                title: 'تغيير نطاق التطبيق',
                message: 'هل تريد تغيير نطاق تطبيق الكوبون إلى:',
                hint: 'للمنتجات أو التصنيفات المحددة يجب ربطها من صفحة التعديل أولاً.',
            });
            return;
        }

        var statusEl = e.target.closest('.admin-table__editable--status');
        if (statusEl) {
            e.preventDefault();
            startSelectEdit(statusEl, 'status', STATUS_OPTIONS, {
                title: 'تغيير حالة الكوبون',
                message: 'هل تريد تغيير حالة الكوبون إلى:',
                hint: 'سيُطبَّق التغيير فوراً.',
            });
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
