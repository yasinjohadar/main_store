/**
 * Laravel compare: AJAX toggle + styled compare bar (replaces Wolmart client-only popup).
 */
(function ($) {
    'use strict';

    var cfg = window.LaravelCompare || {};
    var maxItems = cfg.maxItems || 4;

    function getCsrfToken() {
        return cfg.csrfToken || $('meta[name="csrf-token"]').attr('content');
    }

    function updateCompareCount(count) {
        $('.compare-count').text(count);
    }

    function setButtonState($btn, added) {
        $btn.toggleClass('added', added);
        $btn.toggleClass('w-icon-check-solid', added);
        $btn.toggleClass('w-icon-compare', !added);
        $btn.attr('aria-pressed', added ? 'true' : 'false');
        $btn.attr('title', added ? 'إزالة من المقارنة' : 'أضف للمقارنة');
    }

    function syncProductButtons(products) {
        var ids = (products || []).map(function (p) {
            return String(p.id);
        });

        $('.btn-add-compare').each(function () {
            var $btn = $(this);
            var id = String($btn.data('product-id'));
            setButtonState($btn, ids.indexOf(id) !== -1);
        });
    }

    function renderBar(products) {
        var $bar = $('[data-compare-bar]');
        var $list = $('[data-compare-bar-list]');
        var count = (products || []).length;
        var emptySlots = Math.max(0, maxItems - count);
        var html = '';

        (products || []).forEach(function (product) {
            html += '<li class="store-compare-bar__item" data-compare-item="' + product.id + '">' +
                '<a href="' + product.url + '" class="store-compare-bar__thumb">' +
                '<img src="' + product.image + '" alt="' + product.name + '" width="72" height="72" loading="lazy">' +
                '</a>' +
                '<button type="button" class="store-compare-bar__remove btn-compare-bar-remove" ' +
                'data-delete-url="' + product.delete_url + '" aria-label="إزالة ' + product.name + '">' +
                '<i class="fas fa-times"></i></button></li>';
        });

        for (var i = 0; i < emptySlots; i++) {
            html += '<li class="store-compare-bar__item store-compare-bar__item--empty" aria-hidden="true">' +
                '<span class="store-compare-bar__placeholder"><i class="w-icon-plus"></i></span></li>';
        }

        $list.html(html);

        var countLabel = count === 1 ? 'منتج' : 'منتجات';
        $('[data-compare-bar-count]').html('(<span>' + count + '</span> ' + countLabel + ')');

        var $start = $('[data-compare-bar-start]');
        if (count >= 2) {
            $start.removeClass('is-disabled').attr('aria-disabled', 'false').removeAttr('tabindex');
        } else {
            $start.addClass('is-disabled').attr('aria-disabled', 'true').attr('tabindex', '-1');
        }

        if (count > 0) {
            $bar.addClass('is-visible').attr('aria-hidden', 'false');
        } else {
            $bar.removeClass('is-visible').attr('aria-hidden', 'true');
            $('[data-compare-bar-overlay]').attr('hidden', true);
        }
    }

    function applyResponse(res) {
        if (typeof res.compare_count !== 'undefined') {
            updateCompareCount(res.compare_count);
        }

        renderBar(res.products || []);
        syncProductButtons(res.products || []);
    }

    function handleToggleClick(e) {
        var $btn = $(this);

        e.preventDefault();
        e.stopImmediatePropagation();

        if ($btn.hasClass('loading')) {
            return;
        }

        var productId = $btn.data('product-id');

        if (!productId) {
            return;
        }

        $btn.addClass('load-more-overlay loading');

        $.ajax({
            url: cfg.toggleUrl || '/compare/toggle',
            method: 'POST',
            data: {
                product_id: productId,
                _token: getCsrfToken()
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function (res) {
                $btn.removeClass('load-more-overlay loading');
                applyResponse(res);
            },
            error: function (xhr) {
                $btn.removeClass('load-more-overlay loading');

                var msg = 'تعذرت تحديث المقارنة';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }

                alert(msg);
            }
        });
    }

    function handleRemoveClick(e) {
        var $btn = $(this);

        e.preventDefault();

        if ($btn.hasClass('loading')) {
            return;
        }

        var deleteUrl = $btn.data('delete-url');

        if (!deleteUrl) {
            return;
        }

        $btn.addClass('loading');

        $.ajax({
            url: deleteUrl,
            method: 'POST',
            data: {
                _token: getCsrfToken(),
                _method: 'DELETE'
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function (res) {
                $btn.removeClass('loading');
                applyResponse(res);

                if (window.location.pathname.indexOf('/compare') !== -1) {
                    window.location.reload();
                }
            },
            error: function () {
                $btn.removeClass('loading');
                alert('تعذرت إزالة المنتج من المقارنة');
            }
        });
    }

    function handleClearClick(e) {
        e.preventDefault();

        $.ajax({
            url: cfg.clearUrl || '/compare/clear',
            method: 'POST',
            data: {
                _token: getCsrfToken()
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function (res) {
                applyResponse(res);

                if (window.location.pathname.indexOf('/compare') !== -1) {
                    window.location.reload();
                }
            },
            error: function () {
                alert('تعذرت مسح قائمة المقارنة');
            }
        });
    }

    function handleStartClick(e) {
        var $link = $(this);

        if ($link.hasClass('is-disabled')) {
            e.preventDefault();
            alert('أضف منتجين على الأقل للمقارنة');
        }
    }

    $(function () {
        $('body').on('click', '.btn-add-compare', handleToggleClick);
        $('body').on('click', '.btn-compare-bar-remove', handleRemoveClick);
        $('body').on('click', '.btn-compare-bar-clear', handleClearClick);
        $('body').on('click', '[data-compare-bar-start]', handleStartClick);

        if (cfg.initialProducts) {
            renderBar(cfg.initialProducts);
            syncProductButtons(cfg.initialProducts);
        }
    });
})(jQuery);
