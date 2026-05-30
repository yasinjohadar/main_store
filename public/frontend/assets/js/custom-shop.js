/**
 * Shop layout: infinite scroll + horizontal filter toggles.
 */
(function ($) {
    'use strict';

    var loading = false;

    function buildPageUrl(baseUrl, page) {
        var url = new URL(baseUrl, window.location.origin);
        url.searchParams.set('page', page);
        return url.toString();
    }

    function hasMore($wrapper) {
        var value = $wrapper.data('has-more');
        return value === 1 || value === '1';
    }

    function setInfiniteStatus($status, state) {
        if (!$status.length) {
            return;
        }

        $status.removeClass('is-loading is-done is-error');

        if (state === 'loading') {
            $status.removeAttr('hidden').addClass('is-loading');
        } else if (state === 'done') {
            $status.removeAttr('hidden').addClass('is-done');
        } else if (state === 'error') {
            $status.removeAttr('hidden').addClass('is-error');
        } else {
            $status.attr('hidden', 'hidden');
        }
    }

    function cleanupWolmartOverlay($wrapper) {
        $wrapper.next('.load-more-overlay').remove();
        $wrapper.siblings('.load-more-overlay').remove();
    }

    function initInfiniteScroll() {
        var $wrapper = $('.product-wrapper.store-shop-products--infinite');

        if (!$wrapper.length) {
            return;
        }

        cleanupWolmartOverlay($wrapper);

        var baseUrl = $wrapper.data('url');
        var $status = $('[data-infinite-status]');

        if (!hasMore($wrapper)) {
            setInfiniteStatus($status, 'idle');
            return;
        }

        setInfiniteStatus($status, 'idle');

        function loadMore() {
            if (loading || !hasMore($wrapper)) {
                return;
            }

            var nextPage = parseInt($wrapper.data('page') || 1, 10) + 1;
            loading = true;
            setInfiniteStatus($status, 'loading');

            $.ajax({
                url: buildPageUrl(baseUrl, nextPage),
                method: 'GET',
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function (res) {
                    if (res.html) {
                        $wrapper.append(res.html);
                    }

                    $wrapper.data('page', nextPage);
                    $wrapper.data('has-more', res.has_more ? '1' : '0');

                    if (res.has_more) {
                        setInfiniteStatus($status, 'idle');
                    } else {
                        setInfiniteStatus($status, 'done');
                    }
                },
                error: function () {
                    setInfiniteStatus($status, 'error');
                },
                complete: function () {
                    loading = false;
                    cleanupWolmartOverlay($wrapper);
                }
            });
        }

        function onScroll() {
            if (!hasMore($wrapper) || loading) {
                return;
            }

            var rect = $wrapper[0].getBoundingClientRect();
            var threshold = window.innerHeight + 200;

            if (rect.bottom <= threshold) {
                loadMore();
            }
        }

        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    function initHorizontalFilters() {
        $('body').on('click', '.store-shop-horizontal-filters .select-menu-toggle', function (e) {
            e.preventDefault();
            var $item = $(this).closest('.select-menu');
            $item.toggleClass('opened').siblings('.select-menu.opened').removeClass('opened');
        });

        $(document).on('click', function (e) {
            if (!$(e.target).closest('.store-shop-horizontal-filters .select-menu').length) {
                $('.store-shop-horizontal-filters .select-menu.opened').removeClass('opened');
            }
        });
    }

    function initToolbarOptions() {
        $('body').on('click', '.store-shop-toolbar__options-toggle', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).closest('.store-shop-toolbar__options').toggleClass('show');
        });

        $(document).on('click', function () {
            $('.store-shop-toolbar__options.show').removeClass('show');
        });
    }

    $(function () {
        initInfiniteScroll();
        initHorizontalFilters();
        initToolbarOptions();
    });
})(jQuery);
