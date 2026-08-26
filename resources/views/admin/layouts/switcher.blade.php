{{--
    لوحة إعدادات العرض المختصرة.

    التصميم مبني فوق مبدّل قالب Ynex ولا يستبدله: ملفّا
    assets/js/custom-switcher.min.js و assets/js/custom.js يربطان الأحداث
    بعناصر الـ input الأصلية مباشرة وبدون فحص null، فحذف أي منها يرمي
    TypeError ويُعطّل المبدّل بالكامل. لذلك تبقى كل العناصر الأصلية في
    الصفحة داخل .tp-legacy المخفي، وتُمرّر أزرار اللوحة الأنيقة نقراتها
    إليها عبر assets/js/theme-panel.js.

    الخيارات المعروضة: المظهر (فاتح/داكن)، القائمة الجانبية (موسّعة/مصغّرة)،
    اللون الأساسي. وما عدا ذلك (الاتجاه، أنماط التنقل، تخطيطات القائمة،
    عرض الصفحة، مواضع القائمة/الهيدر، شاشة التحميل، ألوان القائمة/الهيدر،
    الخلفيات وصور الخلفية) لا يزال يعمل بقيمه الافتراضية عبر العناصر
    المخفية أدناه، لكنه غير مُتاح من واجهة اللوحة الأنيقة.
--}}
<div class="offcanvas offcanvas-end theme-panel" tabindex="-1" id="switcher-canvas"
     aria-labelledby="offcanvasRightLabel">

    <div class="offcanvas-header tp-header">
        <div class="tp-header-titles">
            <h5 class="tp-title" id="offcanvasRightLabel">إعدادات العرض</h5>
            <p class="tp-subtitle">خصّص المظهر بسرعة</p>
        </div>
        <button type="button" class="tp-close" data-bs-dismiss="offcanvas" aria-label="إغلاق">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div class="offcanvas-body tp-body">

        {{-- المظهر: فاتح / داكن --}}
        <section class="tp-card">
            <header class="tp-card-head">
                <span class="tp-card-icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                    </svg>
                </span>
                <span class="tp-card-titles">
                    <strong>المظهر</strong>
                    <small>فاتح أو داكن</small>
                </span>
            </header>
            <div class="tp-options tp-options-2" role="group" aria-label="المظهر">
                <button type="button" class="tp-option" data-tp-click="#switcher-light-theme" data-tp-group="mode" data-tp-value="light">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                    </svg>
                    <span>فاتح</span>
                </button>
                <button type="button" class="tp-option" data-tp-click="#switcher-dark-theme" data-tp-group="mode" data-tp-value="dark">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                    </svg>
                    <span>داكن</span>
                </button>
            </div>
        </section>

        {{-- القائمة الجانبية: موسّعة / مصغّرة --}}
        <section class="tp-card">
            <header class="tp-card-head">
                <span class="tp-card-icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 4.5v15m-3.75-15h13.5A2.25 2.25 0 0 1 21 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 17.25V6.75A2.25 2.25 0 0 1 5.25 4.5Z" />
                    </svg>
                </span>
                <span class="tp-card-titles">
                    <strong>القائمة الجانبية</strong>
                    <small>شكل الشريط الجانبي</small>
                </span>
            </header>
            <div class="tp-options tp-options-2" role="group" aria-label="القائمة الجانبية">
                <button type="button" class="tp-option" data-tp-click="#switcher-default-menu" data-tp-group="sidebar" data-tp-value="default">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <span>موسّعة</span>
                </button>
                <button type="button" class="tp-option" data-tp-click="#switcher-icon-overlay" data-tp-group="sidebar" data-tp-value="overlay">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4.5M9 9H4.5M9 9 3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5 5.25 5.25" />
                    </svg>
                    <span>مصغّرة</span>
                </button>
            </div>
        </section>

        {{-- اللون الأساسي --}}
        <section class="tp-card">
            <header class="tp-card-head">
                <span class="tp-card-icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672 13.684 16.6m0 0-2.51 2.225.569-9.47 5.227 7.917-3.286-.672ZM12 2.25V4.5m5.834.166-1.591 1.591M20.25 10.5H18M7.757 14.743l-1.59 1.59M6 10.5H3.75m4.007-4.243-1.59-1.59" />
                    </svg>
                </span>
                <span class="tp-card-titles">
                    <strong>اللون الأساسي</strong>
                    <small>اختر لون الواجهة</small>
                </span>
            </header>
            <div class="tp-swatches">
                {{-- منتقي لون مخصّص — custom.js يبني Pickr داخل هذين العنصرين --}}
                <div class="tp-swatch-custom" title="لون مخصّص">
                    <div class="theme-container-primary"></div>
                    <div class="pickr-container-primary"></div>
                </div>

                <button type="button" class="tp-swatch" style="--tp-swatch: 244, 86, 86;"
                        data-tp-click="#switcher-primary4" data-tp-group="color" data-tp-value="244, 86, 86"
                        aria-label="أحمر"></button>
                <button type="button" class="tp-swatch" style="--tp-swatch: 80, 198, 118;"
                        data-tp-click="#switcher-primary3" data-tp-group="color" data-tp-value="80, 198, 118"
                        aria-label="أخضر"></button>
                <button type="button" class="tp-swatch" style="--tp-swatch: 170, 82, 216;"
                        data-tp-click="#switcher-primary2" data-tp-group="color" data-tp-value="170, 82, 216"
                        aria-label="بنفسجي"></button>
                <button type="button" class="tp-swatch" style="--tp-swatch: 49, 176, 176;"
                        data-tp-click="#switcher-primary1" data-tp-group="color" data-tp-value="49, 176, 176"
                        aria-label="فيروزي"></button>
                <button type="button" class="tp-swatch" style="--tp-swatch: 58, 88, 146;"
                        data-tp-click="#switcher-primary" data-tp-group="color" data-tp-value="58, 88, 146"
                        aria-label="كحلي"></button>
            </div>
        </section>

        <button type="button" class="tp-reset" data-tp-click="#reset-all">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            <span>إعادة الضبط الافتراضي</span>
        </button>
    </div>

    {{--
        عناصر Ynex الأصلية — مخفية لكن موجودة في الصفحة.
        لا تحذف أي عنصر من هنا: custom-switcher.min.js يقرأها بـ
        querySelector ثم يستدعي addEventListener عليها بدون فحص null،
        و custom.js يستدعي appendChild على حاويات pickr.
    --}}
    <div class="tp-legacy" aria-hidden="true">
        {{-- Theme Color Mode --}}
        <input type="radio" name="theme-style" id="switcher-light-theme" checked>
        <input type="radio" name="theme-style" id="switcher-dark-theme">

        {{-- Directions --}}
        <input type="radio" name="direction" id="switcher-ltr" checked>
        <input type="radio" name="direction" id="switcher-rtl">

        {{-- Navigation Styles --}}
        <input type="radio" name="navigation-style" id="switcher-vertical" checked>
        <input type="radio" name="navigation-style" id="switcher-horizontal">

        {{-- Vertical & Horizontal Menu Styles --}}
        <input type="radio" name="navigation-menu-styles" id="switcher-menu-click">
        <input type="radio" name="navigation-menu-styles" id="switcher-menu-hover">
        <input type="radio" name="navigation-menu-styles" id="switcher-icon-click">
        <input type="radio" name="navigation-menu-styles" id="switcher-icon-hover">

        {{-- Sidemenu Layout Styles --}}
        <input type="radio" name="sidemenu-layout-styles" id="switcher-default-menu" checked>
        <input type="radio" name="sidemenu-layout-styles" id="switcher-closed-menu">
        <input type="radio" name="sidemenu-layout-styles" id="switcher-icontext-menu">
        <input type="radio" name="sidemenu-layout-styles" id="switcher-icon-overlay">
        <input type="radio" name="sidemenu-layout-styles" id="switcher-detached">
        <input type="radio" name="sidemenu-layout-styles" id="switcher-double-menu">

        {{-- Page Styles --}}
        <input type="radio" name="page-styles" id="switcher-regular" checked>
        <input type="radio" name="page-styles" id="switcher-classic">
        <input type="radio" name="page-styles" id="switcher-modern">

        {{-- Layout Width Styles --}}
        <input type="radio" name="layout-width" id="switcher-full-width" checked>
        <input type="radio" name="layout-width" id="switcher-boxed">

        {{-- Menu Positions --}}
        <input type="radio" name="menu-positions" id="switcher-menu-fixed" checked>
        <input type="radio" name="menu-positions" id="switcher-menu-scroll">

        {{-- Header Positions --}}
        <input type="radio" name="header-positions" id="switcher-header-fixed" checked>
        <input type="radio" name="header-positions" id="switcher-header-scroll">

        {{-- Loader --}}
        <input type="radio" name="page-loader" id="switcher-loader-enable" checked>
        <input type="radio" name="page-loader" id="switcher-loader-disable" checked>

        {{-- Menu Colors --}}
        <input class="color-input color-white" type="radio" name="menu-colors" id="switcher-menu-light" checked>
        <input class="color-input color-dark" type="radio" name="menu-colors" id="switcher-menu-dark">
        <input class="color-input color-primary" type="radio" name="menu-colors" id="switcher-menu-primary">
        <input class="color-input color-gradient" type="radio" name="menu-colors" id="switcher-menu-gradient">
        <input class="color-input color-transparent" type="radio" name="menu-colors" id="switcher-menu-transparent">

        {{-- Header Colors --}}
        <input class="color-input color-white" type="radio" name="header-colors" id="switcher-header-light" checked>
        <input class="color-input color-dark" type="radio" name="header-colors" id="switcher-header-dark">
        <input class="color-input color-primary" type="radio" name="header-colors" id="switcher-header-primary">
        <input class="color-input color-gradient" type="radio" name="header-colors" id="switcher-header-gradient">
        <input class="color-input color-transparent" type="radio" name="header-colors" id="switcher-header-transparent">

        {{-- Theme Primary --}}
        <input class="color-input color-primary-1" type="radio" name="theme-primary" id="switcher-primary">
        <input class="color-input color-primary-2" type="radio" name="theme-primary" id="switcher-primary1">
        <input class="color-input color-primary-3" type="radio" name="theme-primary" id="switcher-primary2">
        <input class="color-input color-primary-4" type="radio" name="theme-primary" id="switcher-primary3">
        <input class="color-input color-primary-5" type="radio" name="theme-primary" id="switcher-primary4">

        {{-- Theme Background --}}
        <input class="color-input color-bg-1" type="radio" name="theme-background" id="switcher-background">
        <input class="color-input color-bg-2" type="radio" name="theme-background" id="switcher-background1">
        <input class="color-input color-bg-3" type="radio" name="theme-background" id="switcher-background2">
        <input class="color-input color-bg-4" type="radio" name="theme-background" id="switcher-background3">
        <input class="color-input color-bg-5" type="radio" name="theme-background" id="switcher-background4">
        <div class="theme-container-background"></div>
        <div class="pickr-container-background"></div>

        {{-- Menu With Background Image --}}
        <input class="bgimage-input bg-img1" type="radio" name="theme-background" id="switcher-bg-img">
        <input class="bgimage-input bg-img2" type="radio" name="theme-background" id="switcher-bg-img1">
        <input class="bgimage-input bg-img3" type="radio" name="theme-background" id="switcher-bg-img2">
        <input class="bgimage-input bg-img4" type="radio" name="theme-background" id="switcher-bg-img3">
        <input class="bgimage-input bg-img5" type="radio" name="theme-background" id="switcher-bg-img4">

        {{-- Reset --}}
        <a href="javascript:void(0);" id="reset-all">إعادة ضبط</a>
    </div>
</div>
