(function () {
    'use strict';

    const MODAL_ID = 'adminConfirmActionModal';

    let modalInstance = null;
    let isSubmitting = false;
    let lastConfirmText = 'تأكيد';
    let lastCancelText = 'إلغاء';
    let pendingRequest = null;

    function getModalEl() {
        return document.getElementById(MODAL_ID);
    }

    function getModalInstance() {
        const el = getModalEl();
        if (!el || typeof bootstrap === 'undefined' || !bootstrap.Modal) {
            return null;
        }
        if (!modalInstance) {
            modalInstance = bootstrap.Modal.getOrCreateInstance(el);
        }
        return modalInstance;
    }

    function setText(id, text) {
        const el = document.getElementById(id);
        if (el) {
            el.textContent = text;
        }
    }

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function resetSubmitButton() {
        isSubmitting = false;
        pendingRequest = null;

        const submitBtn = document.getElementById('adminConfirmActionSubmit');
        const cancelTextEl = document.getElementById('adminConfirmActionCancelText');
        if (cancelTextEl) {
            cancelTextEl.textContent = lastCancelText;
        }
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="las la-check me-1" aria-hidden="true"></i><span id="adminConfirmActionSubmitText">' + lastConfirmText + '</span>';
        }
    }

    function populateModal(config) {
        const title = config.title || 'تأكيد الإجراء';
        const message = config.message || 'هل أنت متأكد من تنفيذ هذا الإجراء؟';
        const hint = config.hint || '';
        const item = config.item || '';
        const confirmText = config.confirmText || 'تأكيد';
        const cancelText = config.cancelText || 'إلغاء';
        const icon = config.icon || 'las la-power-off';

        lastConfirmText = confirmText;
        lastCancelText = cancelText;

        setText('adminConfirmActionTitle', title);
        setText('adminConfirmActionMessage', message);
        setText('adminConfirmActionSubmitText', confirmText);
        setText('adminConfirmActionCancelText', cancelText);

        const hintEl = document.getElementById('adminConfirmActionHint');
        if (hintEl) {
            const span = hintEl.querySelector('span');
            if (span) {
                span.textContent = hint;
            }
            hintEl.classList.toggle('d-none', !hint);
        }

        const itemEl = document.getElementById('adminConfirmActionItem');
        if (itemEl) {
            itemEl.textContent = item;
            itemEl.classList.toggle('d-none', !item);
        }

        const iconEl = document.getElementById('adminConfirmActionIcon');
        if (iconEl) {
            iconEl.className = icon + ' admin-confirm-action__icon';
        }

        const submitBtn = document.getElementById('adminConfirmActionSubmit');
        if (submitBtn) {
            submitBtn.disabled = !config.url;
        }
    }

    /**
     * Open confirm modal programmatically.
     * @param {Object} config - { url, method, title, message, item, hint, confirmText, cancelText, icon, onSuccess, onError }
     * @returns {Promise<object>|null}
     */
    function openConfirmAction(config) {
        if (!config || !config.url) {
            return null;
        }

        pendingRequest = config;
        populateModal(config);

        const modal = getModalInstance();
        if (modal) {
            modal.show();
        }

        return new Promise(function (resolve, reject) {
            pendingRequest._resolve = resolve;
            pendingRequest._reject = reject;
        });
    }

    function handleConfirmClick() {
        if (isSubmitting || !pendingRequest || !pendingRequest.url) {
            return;
        }

        isSubmitting = true;
        const submitBtn = document.getElementById('adminConfirmActionSubmit');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> جاري التنفيذ...';
        }

        const method = (pendingRequest.method || 'POST').toUpperCase();
        const headers = {
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        };

        if (pendingRequest.body !== undefined) {
            headers['Content-Type'] = 'application/json';
        }

        fetch(pendingRequest.url, {
            method: method,
            headers: headers,
            body: pendingRequest.body !== undefined ? JSON.stringify(pendingRequest.body) : undefined,
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, status: response.status, data: data };
                }).catch(function () {
                    return { ok: response.ok, status: response.status, data: null };
                });
            })
            .then(function (result) {
                const modal = getModalInstance();
                if (modal) {
                    modal.hide();
                }

                if (result.ok && result.data && result.data.success !== false) {
                    if (typeof pendingRequest.onSuccess === 'function') {
                        pendingRequest.onSuccess(result.data);
                    }
                    document.dispatchEvent(new CustomEvent('admin:action-confirmed', {
                        detail: { config: pendingRequest, data: result.data },
                    }));
                    if (pendingRequest._resolve) {
                        pendingRequest._resolve(result.data);
                    }
                } else {
                    const message = (result.data && result.data.message) || 'حدث خطأ أثناء تنفيذ الإجراء.';
                    if (typeof pendingRequest.onError === 'function') {
                        pendingRequest.onError(message, result.data);
                    }
                    if (pendingRequest._reject) {
                        pendingRequest._reject(new Error(message));
                    }
                }
            })
            .catch(function (err) {
                const modal = getModalInstance();
                if (modal) {
                    modal.hide();
                }
                if (typeof pendingRequest.onError === 'function') {
                    pendingRequest.onError(err.message || 'حدث خطأ غير متوقع.');
                }
                if (pendingRequest._reject) {
                    pendingRequest._reject(err);
                }
            })
            .finally(function () {
                resetSubmitButton();
            });
    }

    function handleTriggerClick(event) {
        const trigger = event.target.closest('[data-confirm-action]');
        if (!trigger || trigger.disabled) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        openConfirmAction({
            url: trigger.dataset.url || trigger.dataset.action || '',
            method: trigger.dataset.method || 'POST',
            title: trigger.dataset.title,
            message: trigger.dataset.message,
            item: trigger.dataset.item,
            hint: trigger.dataset.hint,
            confirmText: trigger.dataset.confirmText,
            cancelText: trigger.dataset.cancelText,
            icon: trigger.dataset.icon,
        });
    }

    function init() {
        document.addEventListener('click', handleTriggerClick);

        const submitBtn = document.getElementById('adminConfirmActionSubmit');
        if (submitBtn) {
            submitBtn.addEventListener('click', handleConfirmClick);
        }

        const modalEl = getModalEl();
        if (modalEl) {
            modalEl.addEventListener('hidden.bs.modal', function () {
                if (!isSubmitting && pendingRequest && pendingRequest._reject) {
                    pendingRequest._reject(new Error('cancelled'));
                }
                resetSubmitButton();
            });
        }
    }

    window.AdminConfirmAction = {
        open: openConfirmAction,
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
