/**
 * Load product quick view via AJAX instead of static template.
 */
(function ($) {
    'use strict';

    function initQuickViewQty($product) {
        $product.find('.quick-view-modal__form .qty-input-group').each(function () {
            var $group = $(this);
            var $input = $group.find('input.quantity');

            if (!$input.length) {
                return;
            }

            var min = parseInt($input.attr('min'), 10) || 1;
            var max = parseInt($input.attr('max'), 10) || 100;

            $group.off('click.quickViewQty').on('click.quickViewQty', '.quantity-minus, .quantity-plus', function (e) {
                e.preventDefault();
                e.stopPropagation();

                var val = parseInt($input.val(), 10) || min;

                if ($(this).hasClass('quantity-minus') && val > min) {
                    $input.val(val - 1);
                } else if ($(this).hasClass('quantity-plus') && val < max) {
                    $input.val(val + 1);
                }
            });
        });
    }

    function initQuickViewPopup($content) {
        if (typeof Wolmart === 'undefined') {
            return;
        }

        var $container = $content instanceof jQuery ? $content : $($content);
        var $product = $container.find('.product-single');

        if (!$product.length) {
            $product = $container.hasClass('product-single') ? $container : $container;
        }

        Wolmart.productSingle($product);

        // Re-init quantity after popup layout is painted
        setTimeout(function () {
            $product.find('.quantity').each(function () {
                $(this).removeData('quantityInput');
            });
            Wolmart.initQtyInput($product.find('.quantity'));
            initQuickViewQty($product);
        }, 150);

        Wolmart.ratingTooltip();
    }

    function destroyQuickViewSlider() {
        $('.mfp-product .swiper-container').each(function () {
            var slider = $(this).data('slider');
            if (slider && typeof slider.destroy === 'function') {
                slider.destroy();
            }
        });
    }

    $(function () {
        $('body').on('click', '.btn-quickview', function (e) {
            var url = $(this).attr('href');

            if (!url || url === '#') {
                return;
            }

            e.preventDefault();
            e.stopImmediatePropagation();

            $.magnificPopup.open({
                items: {
                    src: url,
                    type: 'ajax'
                },
                mainClass: 'mfp-product mfp-fade',
                removalDelay: 300,
                tLoading: 'جاري التحميل...',
                callbacks: {
                    ajaxContentAdded: function () {
                        var $content = this.contentContainer || $(this.content);
                        initQuickViewPopup($content);
                    },
                    close: function () {
                        destroyQuickViewSlider();
                    }
                }
            });
        });
    });
})(jQuery);
