/*
 * جسر لوحة إعدادات العرض.
 *
 * اللوحة الأنيقة لا تُطبّق أي شيء بنفسها: كل زر يحمل data-tp-click بمُحدِّد
 * عنصر Ynex الأصلي المخفي، فنُمرّر النقرة إليه ليقوم custom-switcher.min.js
 * بالعمل (تعيين data-* على <html> وتخزين الاختيار في localStorage).
 *
 * ثم نُزامن حالة "نشط" بصرياً من مصدر الحقيقة نفسه — سمات <html>
 * و localStorage — لا من نقرات المستخدم، حتى تظهر الحالة الصحيحة عند
 * تحميل الصفحة وبعد إعادة الضبط.
 */
(function () {
    'use strict';

    const panel = document.getElementById('switcher-canvas');
    if (!panel) {
        return;
    }

    const html = document.documentElement;

    /* —— تمرير النقرات إلى عناصر Ynex المخفية —— */
    panel.querySelectorAll('[data-tp-click]').forEach(function (control) {
        control.addEventListener('click', function () {
            const target = document.querySelector(control.dataset.tpClick);
            if (!target) {
                return;
            }

            target.click();

            // custom-switcher يكتب على <html> و localStorage بشكل متزامن،
            // لكن إعادة الضبط تعمل عبر reload، فنؤجّل المزامنة لإطار واحد.
            window.requestAnimationFrame(sync);
        });
    });

    /* —— قراءة الحالة الفعلية —— */

    function currentMode() {
        return html.getAttribute('data-theme-mode') === 'dark' ? 'dark' : 'light';
    }

    function currentSidebar() {
        // القالب لا يضع data-vertical-style في الحالة الافتراضية
        return html.getAttribute('data-vertical-style') === 'overlay' ? 'overlay' : 'default';
    }

    function currentColor() {
        const stored = localStorage.getItem('primaryRGB');
        if (stored) {
            return normalizeRgb(stored);
        }

        const inline = html.style.getPropertyValue('--primary-rgb');
        return inline ? normalizeRgb(inline) : '';
    }

    // "58, 88, 146" و "58,88,146" يجب أن يتطابقا مع قيمة data-tp-value
    function normalizeRgb(value) {
        return value.split(',').map(function (part) {
            return part.trim();
        }).join(', ');
    }

    /* —— مزامنة الحالة البصرية —— */

    function markGroup(group, activeValue) {
        panel.querySelectorAll('[data-tp-group="' + group + '"]').forEach(function (control) {
            const isActive = control.dataset.tpValue === activeValue;
            control.classList.toggle('is-active', isActive);
            control.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
    }

    function sync() {
        markGroup('mode', currentMode());
        markGroup('sidebar', currentSidebar());
        markGroup('color', currentColor());
    }

    sync();
    document.addEventListener('DOMContentLoaded', sync);

    // زر الطيّ في الهيدر وحفظ localStorage عند التحميل يغيّران السمات
    // من خارج اللوحة، فنراقب <html> بدل الاعتماد على نقراتنا وحدها.
    new MutationObserver(sync).observe(html, {
        attributes: true,
        attributeFilter: ['data-theme-mode', 'data-vertical-style', 'style'],
    });
})();
