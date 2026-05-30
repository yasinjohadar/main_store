/**
 * Laravel cart: AJAX add-to-cart, dropdown refresh, remove from mini cart.
 */
(function ($) {
    'use strict';

    var cfg = window.LaravelCart || {};

    function getCsrfToken() {
        return cfg.csrfToken || $('meta[name="csrf-token"]').attr('content');
    }

    function getProductContext($form) {
        var $product = $form.closest('.product, .product-single, .product-popup, .product-wrap');

        return {
            name: $form.data('product-name')
                || $product.find('.product-name, .product-title, h2.product-title, h4.product-name').first().text().trim(),
            url: $form.data('product-url')
                || $product.find('.product-name a, .product-title a, a.product-name').first().attr('href')
                || '#',
            image: $form.data('product-image')
                || $product.find('.product-media img, .product-image img, figure img').first().attr('src')
                || ''
        };
    }

    function formatCountLabel(count) {
        return count + ' ' + (count === 1 ? 'منتج' : 'منتجات');
    }

    function updateCartCount(count) {
        $('.cart-count').text(count);
        $('[data-cart-count-label]').text(formatCountLabel(count));
    }

    function updateDropdown(html) {
        if (!html) {
            return;
        }

        $('[data-cart-dropdown-root]').each(function () {
            $(this).html(html);
        });
    }

    function applyCartPayload(res) {
        if (typeof res.cart_count !== 'undefined') {
            updateCartCount(res.cart_count);
        }

        if (res.count_label) {
            $('[data-cart-count-label]').text(res.count_label);
        }

        if (res.dropdown_html) {
            updateDropdown(res.dropdown_html);
        }
    }

    function refreshDropdown() {
        if (!cfg.dropdownUrl) {
            return;
        }

        $.ajax({
            url: cfg.dropdownUrl,
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: applyCartPayload
        });
    }

    function openCartPanel() {
        $('.cart-offcanvas').addClass('opened');
    }

    function closeCartPanel() {
        $('.cart-offcanvas').removeClass('opened');
    }

    function showMinipopup(product, message) {
        if (typeof Wolmart === 'undefined' || !Wolmart.Minipopup) {
            return;
        }

        Wolmart.Minipopup.open({
            productClass: ' product-cart store-minipopup-product',
            name: product.name,
            nameLink: product.url,
            imageSrc: product.image,
            imageLink: product.url,
            message: '<p class="store-minipopup__message">' + (message || 'تمت الإضافة إلى السلة') + '</p>',
            actionTemplate:
                '<a href="' + (cfg.cartUrl || '/cart') + '" class="btn btn-rounded btn-sm store-minipopup__btn">عرض السلة</a>' +
                '<a href="' + (cfg.checkoutUrl || '/checkout') + '" class="btn btn-dark btn-rounded btn-sm store-minipopup__btn store-minipopup__btn--dark">إتمام الشراء</a>'
        });
    }

    function handleSubmit(e) {
        var $form = $(this);
        var $btn = $form.find('.btn-add-cart');

        e.preventDefault();

        if (!$btn.length || $btn.hasClass('disabled') || $btn.prop('disabled')) {
            return;
        }

        $btn.addClass('load-more-overlay loading');

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function (res) {
                $btn.removeClass('load-more-overlay loading').addClass('added');

                var product = res.product || getProductContext($form);
                showMinipopup(product, res.message);
                applyCartPayload(res);
                openCartPanel();
            },
            error: function (xhr) {
                $btn.removeClass('load-more-overlay loading');

                var msg = 'تعذرت إضافة المنتج للسلة';

                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat()[0] || msg;
                    }
                }

                alert(msg);
            }
        });
    }

    function handleRemove(e) {
        var $btn = $(this);
        var rowId = $btn.data('cart-remove');

        if (!rowId || $btn.hasClass('is-loading')) {
            return;
        }

        e.preventDefault();
        e.stopPropagation();

        var baseUrl = cfg.removeUrlBase || '/cart';
        $btn.addClass('is-loading');

        $.ajax({
            url: baseUrl + '/' + encodeURIComponent(rowId),
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
                applyCartPayload(res);
            },
            error: function () {
                alert('تعذر حذف المنتج من السلة');
            },
            complete: function () {
                $btn.removeClass('is-loading');
            }
        });
    }

    $(function () {
        $('body').on('submit', 'form.cart-add-form', handleSubmit);
        $('body').on('click', '[data-cart-remove]', handleRemove);

        $('body').on('click', '.cart-offcanvas .cart-overlay', function (e) {
            e.preventDefault();
            closeCartPanel();
        });

        $('body').on('click', '.store-cart-dropdown-panel__close, .cart-offcanvas .cart-close', function (e) {
            e.preventDefault();
            closeCartPanel();
        });

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape') {
                closeCartPanel();
            }
        });
    });
})(jQuery);
