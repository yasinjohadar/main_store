(function () {
    'use strict';

    var frame = document.getElementById('slide_preview_frame');
    if (!frame) return;

    var preview = {
        badge: document.getElementById('slide_preview_badge'),
        subtitle: document.getElementById('slide_preview_subtitle'),
        title: document.getElementById('slide_preview_title'),
        desc: document.getElementById('slide_preview_desc'),
        btn: document.getElementById('slide_preview_btn'),
        bg: document.getElementById('slide_preview_bg'),
        product: document.getElementById('slide_preview_product'),
    };

    var fields = {
        badge: document.getElementById('slide_badge'),
        subtitle: document.getElementById('slide_subtitle'),
        title: document.getElementById('slide_title'),
        description: document.getElementById('slide_description'),
        button_text: document.getElementById('slide_button_text'),
        background_color: document.getElementById('slide_background_color'),
        accent_color: document.getElementById('slide_accent_color'),
    };

    var pickerBg = document.getElementById('picker_bg_color');
    var pickerAccent = document.getElementById('picker_accent_color');
    var bgInput = document.getElementById('slide_background_image');
    var productInput = document.getElementById('slide_product_image');

    function normalizeHex(val) {
        val = (val || '').trim();
        if (!/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(val)) return null;
        if (val.length === 4) {
            val = '#' + val.slice(1).split('').map(function (c) { return c + c; }).join('');
        }
        return val;
    }

    function bindColorSync(picker, input, cssVar) {
        if (!picker || !input) return;

        function applyColor(color) {
            frame.style.setProperty(cssVar, color);
        }

        picker.addEventListener('input', function () {
            input.value = picker.value;
            applyColor(picker.value);
        });

        function syncFromInput() {
            var val = normalizeHex(input.value);
            if (val) {
                picker.value = val;
                input.value = val;
                applyColor(val);
            }
        }

        input.addEventListener('change', syncFromInput);
        input.addEventListener('blur', syncFromInput);
    }

    bindColorSync(pickerBg, fields.background_color, '--preview-bg');
    bindColorSync(pickerAccent, fields.accent_color, '--preview-accent');

    function updateText() {
        if (preview.badge && fields.badge) {
            preview.badge.textContent = fields.badge.value || '';
        }
        if (preview.subtitle && fields.subtitle) {
            preview.subtitle.textContent = fields.subtitle.value || '';
        }
        if (preview.title && fields.title) {
            preview.title.textContent = fields.title.value || 'عنوان الشريحة';
        }
        if (preview.desc && fields.description) {
            preview.desc.textContent = fields.description.value || '';
        }
        if (preview.btn && fields.button_text) {
            preview.btn.textContent = fields.button_text.value || 'تسوق الآن';
        }
    }

    Object.keys(fields).forEach(function (key) {
        var el = fields[key];
        if (el) {
            el.addEventListener('input', updateText);
        }
    });

    if (bgInput && preview.bg) {
        bgInput.addEventListener('change', function () {
            var file = bgInput.files && bgInput.files[0];
            if (!file) return;
            preview.bg.style.backgroundImage = "url('" + URL.createObjectURL(file) + "')";
        });
    }

    if (productInput && preview.product) {
        productInput.addEventListener('change', function () {
            var file = productInput.files && productInput.files[0];
            if (!file) return;
            preview.product.src = URL.createObjectURL(file);
            preview.product.classList.remove('d-none');
        });
    }

    updateText();
})();
