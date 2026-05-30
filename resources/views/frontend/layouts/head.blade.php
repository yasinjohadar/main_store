    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">

    <meta name="keywords" content="Marketplace ecommerce responsive HTML5 Template" />
    <meta name="description"
        content="Wolmart is powerful marketplace &amp; ecommerce responsive Html5 Template.">
    <meta name="author" content="D-THEMES">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('frontend/assets/images/icons/favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- WebFont.js -->
    <script>
        WebFontConfig = {
            google: {
                families: [
                    'Cairo:400,500,600,700',
                    'Alexandria:400,500,600,700,800'
                ]
            }
        };
        (function (d) {
            var wf = d.createElement('script'), s = d.scripts[0];
            wf.src = '{{ asset('frontend/assets/js/webfont.js') }}';
            wf.async = true;
            s.parentNode.insertBefore(wf, s);
        })(document);
    </script>

    <link rel="preload" href="{{ asset('frontend/assets/vendor/fontawesome-free/webfonts/fa-regular-400.woff2') }}" as="font" type="font/woff2"
        crossorigin="anonymous">
    <link rel="preload" href="{{ asset('frontend/assets/vendor/fontawesome-free/webfonts/fa-solid-900.woff2') }}" as="font" type="font/woff2"
        crossorigin="anonymous">
    <link rel="preload" href="{{ asset('frontend/assets/vendor/fontawesome-free/webfonts/fa-brands-400.woff2') }}" as="font" type="font/woff2"
        crossorigin="anonymous">
    <link rel="preload" href="{{ asset('frontend/assets/fonts/wolmart.woff?png09e') }}" as="font" type="font/woff" crossorigin="anonymous">

    <!-- Vendor CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/vendor/fontawesome-free/css/all.min.css') }}">

    <!-- Plugins CSS -->
    <!-- <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/swiper/swiper-bundle.min.css') }}"> -->
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/vendor/animate/animate.min.css') }}">
    <!-- Link Swiper's CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/swiper/swiper-bundle.min.css') }}">

    <!-- Default CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/demo-rtl.min.css') }}">
