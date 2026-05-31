(function () {
    'use strict';

    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-' + (type === 'success' ? 'success' : 'danger') + ' alert-dismissible fade show';
        alertDiv.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>';
        const container = document.querySelector('.main-content');
        if (container) {
            container.insertBefore(alertDiv, container.firstChild);
        }
        setTimeout(function () {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 3500);
    }

    function updateSwitchUI(input, isActive) {
        input.checked = isActive;
        const label = document.querySelector('label[for="' + input.id + '"]');
        if (label) {
            label.textContent = isActive ? 'مفعّل' : 'موقوف';
        }
    }

    function initUserActiveSwitches() {
        document.querySelectorAll('.user-active-switch:not([data-toggle-bound])').forEach(function (input) {
            input.setAttribute('data-toggle-bound', '1');
            input.addEventListener('change', function () {
                const intendedActive = this.checked;
                const url = this.dataset.toggleUrl;
                const userName = this.dataset.userName || '';
                const userId = this.dataset.userId;

                this.checked = !intendedActive;

                if (!url || !window.AdminConfirmAction) {
                    return;
                }

                const isActivating = intendedActive;
                const title = isActivating ? 'تفعيل المستخدم' : 'إيقاف المستخدم';
                const message = isActivating
                    ? 'هل تريد تفعيل هذا المستخدم؟'
                    : 'هل تريد إيقاف تفعيل هذا المستخدم؟';
                const hint = isActivating
                    ? 'سيتمكن المستخدم من تسجيل الدخول بعد التفعيل.'
                    : 'لن يتمكن المستخدم من تسجيل الدخول حتى إعادة التفعيل.';
                const confirmText = isActivating ? 'نعم، فعّل' : 'نعم، أوقف';
                const icon = isActivating ? 'las la-user-check' : 'las la-user-slash';

                window.AdminConfirmAction.open({
                    url: url,
                    method: 'POST',
                    title: title,
                    message: message,
                    item: userName,
                    hint: hint,
                    confirmText: confirmText,
                    cancelText: 'إلغاء',
                    icon: icon,
                    body: {},
                    onSuccess: function (data) {
                        const isActive = Boolean(data.is_active);
                        updateSwitchUI(input, isActive);
                        showAlert(data.message || 'تم تحديث حالة التفعيل.', 'success');
                    },
                    onError: function (message) {
                        showAlert(message, 'error');
                    },
                }).catch(function (err) {
                    if (err && err.message !== 'cancelled') {
                        showAlert(err.message || 'حدث خطأ.', 'error');
                    }
                });
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initUserActiveSwitches);
    } else {
        initUserActiveSwitches();
    }

    document.addEventListener('admin:table-updated', initUserActiveSwitches);
})();
