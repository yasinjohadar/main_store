/**
 * Product variant / attribute selection (product page + quick view).
 */
(function () {
    'use strict';

    function parseVariants(form) {
        try {
            return JSON.parse(form.getAttribute('data-variants') || '[]');
        } catch (e) {
            return [];
        }
    }

    function findContainer(form) {
        return form.closest('.product-details')
            || form.closest('.quick-view-modal__details')
            || form.closest('.store-product-info')
            || form.parentElement;
    }

    function initProductVariantForm(form) {
        if (!form || form.dataset.variantsInitialized === '1') {
            return;
        }

        var variants = parseVariants(form);
        if (!variants.length) {
            return;
        }

        form.dataset.variantsInitialized = '1';

        var container = findContainer(form);
        var variantInput = form.querySelector('[name="product_variant_id"]');
        var priceEl = container ? container.querySelector('.new-price') : null;
        var oldPriceEl = container ? container.querySelector('.old-price') : null;
        var submitBtn = form.querySelector('[type="submit"]');
        var skuEl = container ? container.querySelector('[data-product-sku]') : null;
        var qtyInput = form.querySelector('input[name="quantity"]');
        var attributeBlocks = container ? container.querySelectorAll('[data-attribute-id]') : [];
        var requiredCount = attributeBlocks.length;
        var selected = {};

        function allSelected() {
            return Object.keys(selected).length >= requiredCount;
        }

        function findVariant() {
            var ids = Object.values(selected).slice().sort(function (a, b) { return a - b; });
            return variants.find(function (v) {
                var vids = (v.attribute_value_ids || []).slice().sort(function (a, b) { return a - b; });
                return JSON.stringify(vids) === JSON.stringify(ids);
            });
        }

        function updateHints() {
            attributeBlocks.forEach(function (block) {
                var attrId = block.getAttribute('data-attribute-id');
                var hint = block.querySelector('.store-product-variation__hint');
                if (!hint) {
                    return;
                }
                if (!selected[attrId]) {
                    hint.textContent = 'اختر ' + (block.getAttribute('data-attribute-name') || '');
                    hint.classList.remove('d-none');
                } else {
                    hint.classList.add('d-none');
                }
            });
        }

        function setSubmitState(enabled, label) {
            if (!submitBtn) {
                return;
            }
            submitBtn.disabled = !enabled;
            var labelEl = submitBtn.querySelector('span');
            if (labelEl && label) {
                labelEl.textContent = label;
            }
        }

        function updateVariant() {
            updateHints();

            if (!allSelected()) {
                if (variantInput) {
                    variantInput.value = '';
                }
                setSubmitState(false, 'اختر الخيارات');
                return;
            }

            var match = findVariant();

            if (match) {
                if (variantInput) {
                    variantInput.value = match.id;
                }
                if (priceEl) {
                    priceEl.textContent = match.price.toFixed(2) + ' ر.س';
                }
                if (oldPriceEl && match.compare_at_price && match.compare_at_price > match.price) {
                    oldPriceEl.textContent = match.compare_at_price.toFixed(2) + ' ر.س';
                    oldPriceEl.style.display = '';
                } else if (oldPriceEl) {
                    oldPriceEl.style.display = 'none';
                }
                if (skuEl && match.sku) {
                    skuEl.textContent = match.sku;
                }
                if (qtyInput && match.stock_quantity > 0) {
                    qtyInput.max = match.stock_quantity;
                    if (parseInt(qtyInput.value, 10) > match.stock_quantity) {
                        qtyInput.value = match.stock_quantity;
                    }
                }
                setSubmitState(match.in_stock, match.in_stock ? 'أضف للسلة' : 'غير متوفر');
            } else {
                if (variantInput) {
                    variantInput.value = '';
                }
                setSubmitState(false, 'غير متوفر');
            }
        }

        function applySelection(selectionMap) {
            Object.keys(selectionMap || {}).forEach(function (attrId) {
                var valueId = selectionMap[attrId];
                selected[attrId] = parseInt(valueId, 10);
                var option = container.querySelector(
                    '.store-variant-option[data-attribute-id="' + attrId + '"][data-value-id="' + valueId + '"]'
                );
                if (option) {
                    option.parentElement.querySelectorAll('.store-variant-option').forEach(function (s) {
                        s.classList.remove('active');
                    });
                    option.classList.add('active');
                }
            });
            updateVariant();
        }

        container.querySelectorAll('.store-variant-option').forEach(function (el) {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                var attrId = el.getAttribute('data-attribute-id');
                selected[attrId] = parseInt(el.getAttribute('data-value-id'), 10);
                el.parentElement.querySelectorAll('.store-variant-option').forEach(function (s) {
                    s.classList.remove('active');
                });
                el.classList.add('active');
                updateVariant();
            });
        });

        var defaultVariant = variants.find(function (v) { return v.is_default && v.in_stock; })
            || variants.find(function (v) { return v.in_stock; })
            || variants.find(function (v) { return v.is_default; })
            || variants[0];

        if (defaultVariant && defaultVariant.selection) {
            applySelection(defaultVariant.selection);
        } else {
            updateVariant();
        }
    }

    function initProductVariants(root) {
        var scope = root || document;
        var forms = scope.querySelectorAll ? scope.querySelectorAll('[data-variants]') : [];

        if (scope.matches && scope.matches('[data-variants]')) {
            initProductVariantForm(scope);
            return;
        }

        forms.forEach(initProductVariantForm);
    }

    window.initProductVariants = initProductVariants;

    document.addEventListener('DOMContentLoaded', function () {
        initProductVariants(document);
    });
})();
