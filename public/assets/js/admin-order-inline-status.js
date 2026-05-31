(function () {
    'use strict';

    var statusesCache = null;
    var activeEditor = null;

    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function getStatuses() {
        if (statusesCache) {
            return statusesCache;
        }
        var el = document.getElementById('admin-order-statuses-json');
        if (!el) {
            return [];
        }
        try {
            statusesCache = JSON.parse(el.textContent);
        } catch (e) {
            statusesCache = [];
        }
        return statusesCache;
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

    function statusById(id) {
        return getStatuses().find(function (s) {
            return String(s.id) === String(id);
        });
    }

    function applyStatusDisplay(el, display) {
        if (!el || !display) {
            return;
        }
        el.textContent = display.status_name;
        el.setAttribute('data-original', display.status_id);
        el.setAttribute('data-status-name', display.status_name);
        el.setAttribute('data-status-color', display.status_color);
        el.style.backgroundColor = display.status_color;
    }

    function closeActiveEditor() {
        if (activeEditor && typeof activeEditor.cancel === 'function') {
            activeEditor.cancel();
        }
        activeEditor = null;
    }

    function startStatusEdit(el) {
        closeActiveEditor();

        var original = el.getAttribute('data-original');
        var url = el.getAttribute('data-update-url');
        var select = document.createElement('select');
        select.className = 'form-select form-select-sm admin-table__editable-input';

        getStatuses().forEach(function (status) {
            var option = document.createElement('option');
            option.value = String(status.id);
            option.textContent = status.name;
            if (String(status.id) === String(original)) {
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

        select.addEventListener('change', function () {
            var newStatusId = select.value;
            if (committed) {
                return;
            }

            if (String(newStatusId) === String(original)) {
                cancel();
                return;
            }

            committed = true;
            cleanup();

            var newStatus = statusById(newStatusId);
            var newLabel = newStatus ? newStatus.name : newStatusId;

            if (!window.AdminConfirmAction) {
                fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ order_status_id: parseInt(newStatusId, 10) }),
                })
                    .then(function (response) {
                        return response.json().then(function (data) {
                            return { ok: response.ok, data: data };
                        });
                    })
                    .then(function (result) {
                        if (result.ok && result.data && result.data.success) {
                            applyStatusDisplay(el, result.data.display);
                            showAlert('تم تحديث حالة الطلب.', 'success');
                        } else {
                            showAlert((result.data && result.data.message) || 'فشل تحديث الحالة.', 'error');
                        }
                    })
                    .catch(function () {
                        showAlert('فشل تحديث الحالة.', 'error');
                    });
                return;
            }

            window.AdminConfirmAction.open({
                url: url,
                method: 'PATCH',
                body: { order_status_id: parseInt(newStatusId, 10) },
                title: 'تغيير حالة الطلب',
                message: 'هل تريد تغيير حالة الطلب إلى:',
                item: newLabel,
                hint: 'سيُسجَّل التغيير في سجل حالة الطلب.',
                confirmText: 'نعم، غيّر',
                cancelText: 'إلغاء',
                icon: 'las la-exchange-alt',
                onSuccess: function (data) {
                    if (data && data.display) {
                        applyStatusDisplay(el, data.display);
                    }
                    showAlert('تم تحديث حالة الطلب.', 'success');
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

    function handleClick(e) {
        if (e.target.closest('.admin-table__editable-input')) {
            return;
        }

        var statusEl = e.target.closest('.admin-table__editable--order-status');
        if (statusEl) {
            e.preventDefault();
            startStatusEdit(statusEl);
        }
    }

    function handleKeydown(e) {
        if (e.key !== 'Enter') {
            return;
        }
        var editable = e.target.closest('.admin-table__editable--order-status[tabindex="0"]');
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
