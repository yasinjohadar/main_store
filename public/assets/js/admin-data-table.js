(function () {
    'use strict';

    var DEBOUNCE_MS = 400;
    var debounceTimers = {};

    function getTarget(form) {
        var selector = form.getAttribute('data-admin-table-target');
        if (!selector) {
            return null;
        }
        return document.querySelector(selector);
    }

    function getCard(target) {
        return target ? target.closest('.admin-table-card') : null;
    }

    function buildUrl(form, extraParams) {
        var url = new URL(form.getAttribute('action') || window.location.href, window.location.origin);
        var data = new FormData(form);

        data.forEach(function (value, key) {
            if (value !== '') {
                url.searchParams.set(key, value);
            } else {
                url.searchParams.delete(key);
            }
        });

        if (extraParams) {
            Object.keys(extraParams).forEach(function (key) {
                if (extraParams[key] === null || extraParams[key] === '') {
                    url.searchParams.delete(key);
                } else {
                    url.searchParams.set(key, extraParams[key]);
                }
            });
        }

        return url;
    }

    function setLoading(card, loading) {
        if (!card) {
            return;
        }
        card.classList.toggle('admin-table-card--loading', loading);
    }

    function fetchTable(url, target) {
        var card = getCard(target);
        setLoading(card, true);

        return fetch(url.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-Admin-Table': '1',
                'Accept': 'text/html',
            },
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('فشل تحميل البيانات');
                }
                return response.text();
            })
            .then(function (html) {
                target.innerHTML = html;
                window.history.pushState({ adminTable: true }, '', url.toString());
                document.dispatchEvent(new CustomEvent('admin:table-updated', {
                    detail: { target: target, url: url.toString() },
                }));
            })
            .catch(function () {
                window.location.href = url.toString();
            })
            .finally(function () {
                setLoading(card, false);
            });
    }

    function submitFilterForm(form, extraParams) {
        var target = getTarget(form);
        if (!target) {
            form.submit();
            return;
        }

        var url = buildUrl(form, extraParams);
        fetchTable(url, target);
    }

    function resetFilterForm(form) {
        form.querySelectorAll('input, select, textarea').forEach(function (field) {
            if (field.type === 'hidden' || field.type === 'submit' || field.type === 'button') {
                return;
            }
            if (field.type === 'checkbox' || field.type === 'radio') {
                field.checked = false;
                return;
            }
            field.value = '';
        });

        submitFilterForm(form, { page: null });
    }

    function handleFormSubmit(event) {
        var form = event.target.closest('.admin-table-filter-form');
        if (!form) {
            return;
        }

        event.preventDefault();
        submitFilterForm(form);
    }

    function handleResetClick(event) {
        var btn = event.target.closest('[data-admin-table-reset]');
        if (!btn) {
            return;
        }

        event.preventDefault();
        var form = btn.closest('.admin-table-filter-form') || document.querySelector(btn.getAttribute('data-admin-table-reset-form'));
        if (form) {
            resetFilterForm(form);
        }
    }

    function handlePaginationClick(event) {
        var link = event.target.closest('[data-admin-table] a.page-link, [data-admin-table] .pagination a');
        if (!link || !link.href) {
            return;
        }

        var target = link.closest('[data-admin-table]');
        if (!target) {
            return;
        }

        var card = target.closest('.admin-table-card');
        var form = card ? card.querySelector('.admin-table-filter-form') : null;

        event.preventDefault();
        var url = new URL(link.href, window.location.origin);

        if (form) {
            var merged = buildUrl(form);
            url.searchParams.forEach(function (value, key) {
                if (key !== 'page') {
                    merged.searchParams.set(key, value);
                }
            });
            if (url.searchParams.has('page')) {
                merged.searchParams.set('page', url.searchParams.get('page'));
            }
            fetchTable(merged, target);
        } else {
            fetchTable(url, target);
        }
    }

    function handleAutoSubmitChange(event) {
        var field = event.target;
        var form = field.closest('.admin-table-filter-form');
        if (!form || !form.hasAttribute('data-admin-table-autosubmit')) {
            return;
        }

        if (field.name === 'query') {
            return;
        }

        submitFilterForm(form);
    }

    function handleSearchInput(event) {
        var field = event.target;
        if (field.name !== 'query') {
            return;
        }

        var form = field.closest('.admin-table-filter-form');
        if (!form || !form.hasAttribute('data-admin-table-autosubmit')) {
            return;
        }

        var formId = form.getAttribute('id') || form.getAttribute('action') || 'default';
        clearTimeout(debounceTimers[formId]);
        debounceTimers[formId] = setTimeout(function () {
            submitFilterForm(form, { page: null });
        }, DEBOUNCE_MS);
    }

    function init() {
        document.addEventListener('submit', handleFormSubmit);
        document.addEventListener('click', handleResetClick);
        document.addEventListener('click', handlePaginationClick);
        document.addEventListener('change', handleAutoSubmitChange);
        document.addEventListener('input', handleSearchInput);

        window.addEventListener('popstate', function () {
            document.querySelectorAll('[data-admin-table]').forEach(function (target) {
                var card = target.closest('.admin-table-card');
                var form = card ? card.querySelector('.admin-table-filter-form') : null;
                if (form) {
                    var url = new URL(window.location.href);
                    form.querySelectorAll('input, select, textarea').forEach(function (field) {
                        if (!field.name || field.type === 'submit' || field.type === 'button') {
                            return;
                        }
                        if (url.searchParams.has(field.name)) {
                            field.value = url.searchParams.get(field.name);
                        } else if (field.type !== 'checkbox' && field.type !== 'radio') {
                            field.value = '';
                        }
                    });
                    fetchTable(url, target);
                }
            });
        });
    }

    window.AdminDataTable = {
        refresh: submitFilterForm,
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
