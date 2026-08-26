(function () {
    'use strict';

    var prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function animateCount(el) {
        var target = parseInt(el.getAttribute('data-count'), 10) || 0;
        var suffix = el.getAttribute('data-suffix') || '';

        if (prefersReducedMotion) {
            el.textContent = target.toLocaleString('en-US') + suffix;
            return;
        }

        var duration = 1200;
        var start = performance.now();

        function easeOutExpo(t) {
            return t === 1 ? 1 : 1 - Math.pow(2, -10 * t);
        }

        function tick(now) {
            var progress = Math.min((now - start) / duration, 1);
            var value = Math.round(easeOutExpo(progress) * target);
            el.textContent = value.toLocaleString('en-US') + suffix;
            if (progress < 1) {
                requestAnimationFrame(tick);
            } else {
                el.classList.add('cmd-value-done');
            }
        }

        requestAnimationFrame(tick);
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.cmd-dashboard [data-count]').forEach(animateCount);
    });
})();
