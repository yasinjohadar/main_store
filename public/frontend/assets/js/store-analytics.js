(function () {
    'use strict';

    var cfg = window.StoreAnalytics || {};
    if (!cfg.ga4Id && !cfg.metaPixelId && !cfg.beaconUrl) {
        return;
    }

    function pushEvent(name, params) {
        if (typeof gtag === 'function' && cfg.ga4Id) {
            gtag('event', name, params || {});
        }
        if (cfg.beaconUrl) {
            fetch(cfg.beaconUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': cfg.csrfToken || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ event_name: name, payload: params || {}, url: window.location.href }),
                keepalive: true,
            }).catch(function () {});
        }
    }

    window.StoreAnalyticsPush = pushEvent;

    document.addEventListener('click', function (e) {
        var cta = e.target.closest('[data-analytics-cta]');
        if (cta) {
            pushEvent('cta_click', {
                cta_label: cta.getAttribute('data-analytics-cta') || cta.textContent.trim(),
                content_url: window.location.pathname,
                content_type: cta.getAttribute('data-content-type') || 'page',
            });
            if (typeof fbq === 'function') {
                fbq('trackCustom', 'CTAClick', { label: cta.getAttribute('data-analytics-cta') });
            }
        }

        var share = e.target.closest('[data-analytics-share]');
        if (share) {
            pushEvent('share_click', {
                method: share.getAttribute('data-analytics-share'),
                content_url: window.location.pathname,
                content_type: share.getAttribute('data-content-type') || 'article',
            });
        }
    });

    if (cfg.productView) {
        pushEvent('view_item', { items: [cfg.productView], value: cfg.productView.value, currency: cfg.productView.currency || 'SAR' });
        if (typeof fbq === 'function') {
            fbq('track', 'ViewContent', {
                content_name: cfg.productView.item_name,
                content_ids: [cfg.productView.item_id],
                value: cfg.productView.value,
                currency: cfg.productView.currency || 'SAR',
            });
        }
    }

    if (cfg.itemList && cfg.itemList.items && cfg.itemList.items.length) {
        pushEvent('view_item_list', cfg.itemList);
    }

    if (cfg.searchTerm) {
        pushEvent('search', { search_term: cfg.searchTerm });
    }

    if (cfg.beginCheckout) {
        pushEvent('begin_checkout', cfg.beginCheckout);
        if (typeof fbq === 'function') {
            fbq('track', 'InitiateCheckout', {
                value: cfg.beginCheckout.value,
                currency: cfg.beginCheckout.currency || 'SAR',
            });
        }
    }

    if (cfg.purchase) {
        pushEvent('purchase', cfg.purchase);
        if (typeof fbq === 'function') {
            fbq('track', 'Purchase', {
                value: cfg.purchase.value,
                currency: cfg.purchase.currency || 'SAR',
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-analytics-add-cart]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var payload = {
                    item_id: btn.getAttribute('data-product-id'),
                    item_name: btn.getAttribute('data-product-name'),
                    value: parseFloat(btn.getAttribute('data-product-price') || 0),
                    currency: 'SAR',
                };
                pushEvent('add_to_cart', { items: [payload], value: payload.value, currency: payload.currency });
                if (typeof fbq === 'function') {
                    fbq('track', 'AddToCart', payload);
                }
            });
        });
    });
})();
