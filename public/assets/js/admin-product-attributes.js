(function () {
    'use strict';

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function renderValuesPanel(panel, values, type, attributeName, attributeId, manageUrl) {
        panel.innerHTML = '';

        var header = document.createElement('div');
        header.className = 'product-attribute-picker__values-header';
        header.innerHTML = '<span class="product-attribute-picker__values-title">قيم «' + escapeHtml(attributeName) + '»</span>';

        var manageLink = document.createElement('a');
        manageLink.href = manageUrl || '#';
        manageLink.className = 'product-attribute-picker__manage-link';
        manageLink.textContent = 'إدارة القيم';
        header.appendChild(manageLink);
        panel.appendChild(header);

        if (!values || !values.length) {
            var empty = document.createElement('p');
            empty.className = 'text-muted small mb-0';
            empty.textContent = 'لا توجد قيم لهذه السمة بعد. أضف قيماً من صفحة إدارة السمات.';
            panel.appendChild(empty);
            return;
        }

        var list = document.createElement('div');
        list.className = 'product-attribute-picker__values-list';

        values.forEach(function (item) {
            if (type === 'color' && item.color_hex) {
                var swatch = document.createElement('span');
                swatch.className = 'product-attribute-picker__swatch';
                swatch.innerHTML =
                    '<span class="product-attribute-picker__swatch-dot" style="background-color:' + escapeHtml(item.color_hex) + '"></span>' +
                    '<span>' + escapeHtml(item.value) + '</span>' +
                    '<code class="product-attribute-picker__hex">' + escapeHtml(item.color_hex) + '</code>';
                list.appendChild(swatch);
            } else {
                var badge = document.createElement('span');
                badge.className = 'admin-table__badge admin-table__badge--role';
                badge.textContent = item.value;
                list.appendChild(badge);
            }
        });

        panel.appendChild(list);
    }

    function syncItem(item) {
        var checkbox = item.querySelector('.product-attribute-cb');
        var panel = item.querySelector('.product-attribute-picker__values');
        if (!checkbox || !panel) {
            return;
        }

        if (checkbox.checked) {
            panel.classList.remove('d-none');
            var values = [];
            try {
                values = JSON.parse(checkbox.getAttribute('data-values') || '[]');
            } catch (e) {
                values = [];
            }
            renderValuesPanel(
                panel,
                values,
                checkbox.getAttribute('data-attribute-type') || 'select',
                checkbox.getAttribute('data-attribute-name') || '',
                checkbox.getAttribute('data-attribute-id') || '',
                checkbox.getAttribute('data-manage-url') || '#'
            );
        } else {
            panel.classList.add('d-none');
            panel.innerHTML = '';
        }
    }

    function initProductAttributePicker(root) {
        var scope = root || document;
        scope.querySelectorAll('.product-attribute-picker__item').forEach(function (item) {
            var checkbox = item.querySelector('.product-attribute-cb');
            if (!checkbox || checkbox.hasAttribute('data-picker-bound')) {
                if (checkbox && checkbox.checked) {
                    syncItem(item);
                }
                return;
            }

            checkbox.setAttribute('data-picker-bound', '1');
            checkbox.addEventListener('change', function () {
                syncItem(item);
            });

            if (checkbox.checked) {
                syncItem(item);
            }
        });
    }

    window.initProductAttributePicker = initProductAttributePicker;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initProductAttributePicker();
        });
    } else {
        initProductAttributePicker();
    }
})();
