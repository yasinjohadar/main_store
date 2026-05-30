/**
 * Laravel wishlist: AJAX toggle via .btn-add-wishlist (avoids Wolmart's fake .btn-wishlist handler).
 */
(function ($) {
    'use strict';

    var cfg = window.LaravelWishlist || {};

    function getProductContext($btn) {
        var $product = $btn.closest('.product, .product-single, .product-popup, .product-wrap');

        return {
            name: $btn.data('product-name')
                || $product.find('.product-name, .product-title, h2.product-title, h4.product-name').first().text().trim(),
            url: $btn.data('product-url')
                || $product.find('.product-name a, .product-title a, a.product-name').first().attr('href')
                || '#',
            image: $btn.data('product-image')
                || $product.find('.product-media img, .product-image img, figure img').first().attr('src')
                || ''
        };
    }

    function updateWishlistCount(count) {
        $('.wishlist-count').text(count);
    }

    function setButtonState($btn, added) {
        $btn.toggleClass('added', added);
        $btn.toggleClass('w-icon-heart-full', added);
        $btn.toggleClass('w-icon-heart', !added);
        $btn.attr('aria-pressed', added ? 'true' : 'false');
        $btn.attr('title', added ? 'إزالة من المفضلة' : 'أضف للمفضلة');
    }

    function showMinipopup(product, message) {
        if (typeof Wolmart === 'undefined' || !Wolmart.Minipopup) {
            return;
        }

        Wolmart.Minipopup.open({
            productClass: ' product-cart',
            name: product.name,
            nameLink: product.url,
            imageSrc: product.image,
            imageLink: product.url,
            message: '<p>' + message + '</p>',
            actionTemplate:
                '<a href="' + (cfg.wishlistUrl || '/wishlist') + '" class="btn btn-rounded btn-sm">عرض المفضلة</a>' +
                '<a href="' + (cfg.shopUrl || '/shop') + '" class="btn btn-dark btn-rounded btn-sm">متابعة التسوق</a>'
        });
    }

    function handleClick(e) {
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
            url: cfg.toggleUrl || '/wishlist/toggle',
            method: 'POST',
            data: {
                product_id: productId,
                _token: cfg.csrfToken || $('meta[name="csrf-token"]').attr('content')
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function (res) {
                $btn.removeClass('load-more-overlay loading');
                setButtonState($btn, !!res.added);

                if (typeof res.wishlist_count !== 'undefined') {
                    updateWishlistCount(res.wishlist_count);
                }

                var product = res.product || getProductContext($btn);

                if (res.added) {
                    showMinipopup(product, res.message || 'تمت إضافة المنتج إلى المفضلة');
                }
            },
            error: function (xhr) {
                $btn.removeClass('load-more-overlay loading');

                var msg = 'تعذرت تحديث المفضلة';

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

        e.preventDefault();
        e.stopImmediatePropagation();

        if ($btn.hasClass('loading')) {
            return;
        }

        var deleteUrl = $btn.data('delete-url');
        var $item = $btn.closest('[data-wishlist-item]');

        if (!deleteUrl) {
            return;
        }

        $btn.addClass('load-more-overlay loading');

        $.ajax({
            url: deleteUrl,
            method: 'POST',
            data: {
                _token: cfg.csrfToken || $('meta[name="csrf-token"]').attr('content'),
                _method: 'DELETE'
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function (res) {
                $btn.removeClass('load-more-overlay loading');

                if (typeof res.wishlist_count !== 'undefined') {
                    updateWishlistCount(res.wishlist_count);

                    var $pageCount = $('[data-wishlist-page-count]');
                    if ($pageCount.length) {
                        $pageCount.text(res.wishlist_count);
                    }
                }

                if ($item.length) {
                    $item.slideUp(280, function () {
                        $item.remove();

                        if (!$('[data-wishlist-item]').length) {
                            window.location.reload();
                        }
                    });
                }
            },
            error: function () {
                $btn.removeClass('load-more-overlay loading');
                alert('تعذرت إزالة المنتج من المفضلة');
            }
        });
    }

    $(function () {
        $('body').on('click', '.btn-add-wishlist', handleClick);
        $('body').on('click', '.btn-remove-wishlist', handleRemove);
    });
})(jQuery);
