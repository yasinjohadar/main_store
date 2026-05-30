/**
 * Custom category picker for header search (replaces native select dropdown).
 */
(function ($) {
    'use strict';

    function closePicker($picker) {
        $picker.removeClass('is-open');
        $picker.find('[data-search-category-toggle]').attr('aria-expanded', 'false');
    }

    function openPicker($picker) {
        $picker.addClass('is-open');
        $picker.find('[data-search-category-toggle]').attr('aria-expanded', 'true');
    }

    $(function () {
        $('[data-search-category-picker]').each(function () {
            var $picker = $(this);
            var $input = $picker.find('[data-search-category-input]');
            var $label = $picker.find('[data-search-category-label]');
            var $toggle = $picker.find('[data-search-category-toggle]');
            var $options = $picker.find('.store-header-search__category-option');

            $toggle.on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                if ($picker.hasClass('is-open')) {
                    closePicker($picker);
                } else {
                    $('[data-search-category-picker].is-open').each(function () {
                        closePicker($(this));
                    });
                    openPicker($picker);
                }
            });

            $options.on('click', function (e) {
                e.preventDefault();

                var $option = $(this);
                var value = $option.data('value');
                var text = $.trim($option.text());

                $input.val(value === undefined || value === null ? '' : String(value));
                $label.text(text);

                $options.removeClass('is-active');
                $option.addClass('is-active');

                closePicker($picker);
            });
        });

        $(document).on('click', function () {
            closePicker($('[data-search-category-picker].is-open'));
        });

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape') {
                closePicker($('[data-search-category-picker].is-open'));
            }
        });

        $('[data-search-category-menu]').on('click', function (e) {
            e.stopPropagation();
        });
    });
})(jQuery);
