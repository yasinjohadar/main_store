(function () {
    'use strict';

    const MODAL_ID = 'adminConfirmDeleteModal';
    const FORM_ID = 'adminConfirmDeleteForm';

    let modalInstance = null;
    let isSubmitting = false;
    let lastConfirmText = 'نعم، احذف';
    let lastCancelText = 'إلغاء';

    function getModalEl() {
        return document.getElementById(MODAL_ID);
    }

    function getFormEl() {
        return document.getElementById(FORM_ID);
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

    function populateModal(trigger) {
        const title = trigger.dataset.title || 'تأكيد الحذف';
        const message = trigger.dataset.message || 'هل أنت متأكد من حذف هذا العنصر؟';
        const warning = trigger.dataset.warning || 'لن يمكن التراجع عن هذا الإجراء.';
        const item = trigger.dataset.item || '';
        const method = (trigger.dataset.method || 'DELETE').toUpperCase();
        const confirmText = trigger.dataset.confirmText || 'نعم، احذف';
        const cancelText = trigger.dataset.cancelText || 'إلغاء';
        lastConfirmText = confirmText;
        lastCancelText = cancelText;
        const icon = trigger.dataset.icon || 'las la-trash-alt';
        const action = trigger.dataset.action || '';

        setText('adminConfirmDeleteTitle', title);
        setText('adminConfirmDeleteMessage', message);
        setText('adminConfirmDeleteSubmitText', confirmText);
        setText('adminConfirmDeleteCancelText', cancelText);

        const warningEl = document.getElementById('adminConfirmDeleteWarning');
        if (warningEl) {
            const span = warningEl.querySelector('span');
            if (span) {
                span.textContent = warning;
            }
            warningEl.classList.toggle('d-none', !warning);
        }

        const itemEl = document.getElementById('adminConfirmDeleteItem');
        if (itemEl) {
            itemEl.textContent = item;
            itemEl.classList.toggle('d-none', !item);
        }

        const iconEl = document.getElementById('adminConfirmDeleteIcon');
        if (iconEl) {
            iconEl.className = icon + ' admin-confirm-delete__icon';
        }

        const form = getFormEl();
        if (form) {
            form.action = action;
            const methodInput = document.getElementById('adminConfirmDeleteMethod');
            if (methodInput) {
                methodInput.value = method;
            }
        }

        const submitBtn = document.getElementById('adminConfirmDeleteSubmit');
        if (submitBtn) {
            submitBtn.disabled = !action;
        }
    }

    function resetSubmitButton() {
        isSubmitting = false;
        const submitBtn = document.getElementById('adminConfirmDeleteSubmit');
        const cancelTextEl = document.getElementById('adminConfirmDeleteCancelText');
        if (cancelTextEl) {
            cancelTextEl.textContent = lastCancelText;
        }
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="las la-trash-alt me-1" aria-hidden="true"></i><span id="adminConfirmDeleteSubmitText">' + lastConfirmText + '</span>';
        }
    }

    function handleTriggerClick(event) {
        const trigger = event.target.closest('[data-confirm-delete]');
        if (!trigger || trigger.disabled) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        populateModal(trigger);

        const modal = getModalInstance();
        if (modal) {
            modal.show();
        }
    }

    function handleConfirmClick() {
        if (isSubmitting) {
            return;
        }

        const form = getFormEl();
        if (!form || !form.action) {
            return;
        }

        isSubmitting = true;
        const submitBtn = document.getElementById('adminConfirmDeleteSubmit');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> جاري الحذف...';
        }

        form.submit();
    }

    function init() {
        document.addEventListener('click', handleTriggerClick);

        const submitBtn = document.getElementById('adminConfirmDeleteSubmit');
        if (submitBtn) {
            submitBtn.addEventListener('click', handleConfirmClick);
        }

        const modalEl = getModalEl();
        if (modalEl) {
            modalEl.addEventListener('hidden.bs.modal', resetSubmitButton);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
