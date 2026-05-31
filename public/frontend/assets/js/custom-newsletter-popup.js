(function ($) {
    'use strict';

    function initStoreNewsletterPopup() {
        var cfg = window.storeNewsletterPopup || {};

        if (cfg.enabled === false) {
            return;
        }

        if (!$('body').hasClass('home')) {
            return;
        }

        if (typeof Wolmart === 'undefined' || typeof Wolmart.getCookie !== 'function') {
            return;
        }

        if (Wolmart.getCookie('hideNewsletterPopup') === 'true') {
            return;
        }

        if (!document.querySelector('.newsletter-popup')) {
            return;
        }

        var delay = parseInt(cfg.delay_ms || cfg.delayMs, 10);
        if (isNaN(delay) || delay < 1000) {
            delay = 7500;
        }

        setTimeout(function () {
            Wolmart.popup({
                items: {
                    src: '.newsletter-popup',
                },
                type: 'inline',
                tLoading: '',
                mainClass: 'mfp-newsletter mfp-fadein-popup',
                callbacks: {
                    beforeClose: function () {
                        var checkbox = document.getElementById('hide-newsletter-popup');
                        if (checkbox && checkbox.checked) {
                            Wolmart.setCookie('hideNewsletterPopup', true, 7);
                        }
                    },
                },
            });
        }, delay);

        $(document).on('submit', '.store-newsletter-popup-form', function (event) {
            event.preventDefault();
        });
    }

    $(function () {
        initStoreNewsletterPopup();
    });
})(jQuery);
