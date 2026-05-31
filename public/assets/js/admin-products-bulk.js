(function () {
    'use strict';

    function getProductCheckboxes() {
        return Array.prototype.slice.call(document.querySelectorAll('input[name="ids[]"]'));
    }

    function initProductsBulk() {
        var selectAll = document.getElementById('select-all-products');
        var compareBtn = document.getElementById('btn-compare-selected');

        function updateCompareState() {
            if (!compareBtn) {
                return;
            }
            var selectedIds = getProductCheckboxes().filter(function (cb) { return cb.checked; }).map(function (cb) { return cb.value; });
            compareBtn.disabled = selectedIds.length < 2;
            compareBtn.dataset.selectedIds = JSON.stringify(selectedIds);
        }

        if (selectAll && !selectAll.hasAttribute('data-bulk-bound')) {
            selectAll.setAttribute('data-bulk-bound', '1');
            selectAll.addEventListener('change', function () {
                var checked = this.checked;
                getProductCheckboxes().forEach(function (cb) {
                    cb.checked = checked;
                });
                updateCompareState();
            });
        }

        getProductCheckboxes().forEach(function (cb) {
            if (cb.hasAttribute('data-bulk-bound')) {
                return;
            }
            cb.setAttribute('data-bulk-bound', '1');
            cb.addEventListener('change', updateCompareState);
        });

        if (compareBtn && !compareBtn.hasAttribute('data-compare-bound')) {
            compareBtn.setAttribute('data-compare-bound', '1');
            compareBtn.addEventListener('click', function () {
                var ids = JSON.parse(this.dataset.selectedIds || '[]');
                var baseUrl = this.getAttribute('data-compare-url');
                if (!ids || ids.length < 2 || !baseUrl) {
                    return;
                }
                var params = new URLSearchParams();
                ids.forEach(function (id) {
                    params.append('ids[]', id);
                });
                window.location.href = baseUrl + '?' + params.toString();
            });
        }

        updateCompareState();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initProductsBulk);
    } else {
        initProductsBulk();
    }

    document.addEventListener('admin:table-updated', initProductsBulk);
})();
