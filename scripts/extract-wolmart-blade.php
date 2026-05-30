<?php

/**
 * One-time extractor: demo-rtl.html → Blade partials/components.
 * Run: php scripts/extract-wolmart-blade.php
 */

$basePath = dirname(__DIR__);
$htmlFile = $basePath . '/resources/views/frontend/html pages/demo-rtl.html';
$lines = file($htmlFile, FILE_IGNORE_NEW_LINES);

function extractLines(array $lines, int $start, int $end): string
{
    return implode("\n", array_slice($lines, $start - 1, $end - $start + 1));
}

function convertToBlade(string $html): string
{
    $html = preg_replace('/url\(assets\/([^)]+)\)/', "url({{ asset('frontend/assets/$1') }})", $html);

    $html = preg_replace_callback(
        '/\b(href|src|data-zoom-image)="assets\/([^"]+)"/',
        function ($m) {
            return $m[1] . '="{{ asset(\'frontend/assets/' . $m[2] . '\') }}"';
        },
        $html
    );

    $html = preg_replace_callback(
        "/\b(href|src|data-zoom-image)='assets\/([^']+)'/",
        function ($m) {
            return $m[1] . "='{{ asset('frontend/assets/" . $m[2] . "') }}'";
        },
        $html
    );

    $html = preg_replace_callback(
        "/wf\.src = 'assets\/([^']+)'/",
        fn ($m) => "wf.src = '{{ asset('frontend/assets/{$m[1]}') }}'",
        $html
    );

    $routeMap = [
        'href="demo-rtl.html"' => 'href="{{ route(\'frontend.home\') }}"',
        "href='demo-rtl.html'" => "href=\"{{ route('frontend.home') }}\"",
        'href="demo-rtl-shop.html"' => 'href="{{ route(\'frontend.shop.index\') }}"',
        'href="cart.html"' => 'href="{{ route(\'frontend.cart.index\') }}"',
        'href="checkout.html"' => 'href="{{ route(\'frontend.checkout.index\') }}"',
        'href="wishlist.html"' => 'href="{{ route(\'frontend.wishlist\') }}"',
        'href="compare.html"' => 'href="{{ route(\'frontend.compare\') }}"',
        'href="blog.html"' => 'href="{{ route(\'frontend.blog.index\') }}"',
        'href="about-us.html"' => 'href="{{ route(\'frontend.about\') }}"',
        'href="contact-us.html"' => 'href="{{ route(\'frontend.contact\') }}"',
        'href="faq.html"' => 'href="{{ route(\'frontend.faq\') }}"',
        'href="my-account.html"' => 'href="{{ route(\'frontend.account\') }}"',
        'href="login.html"' => 'href="{{ route(\'login\') }}"',
        'href="shop-list.html"' => 'href="{{ route(\'frontend.shop.index\') }}"',
        'href="shop-banner-sidebar.html"' => 'href="{{ route(\'frontend.shop.index\') }}"',
        'href="product-default.html"' => 'href="{{ route(\'frontend.shop.index\') }}"',
        'href="post-single.html"' => 'href="{{ route(\'frontend.blog.index\') }}"',
    ];

    foreach ($routeMap as $search => $replace) {
        $html = str_replace($search, $replace, $html);
    }

    return $html;
}

function writeFile(string $path, string $content): void
{
    $dir = dirname($path);
    if (! is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($path, $content);
    echo "Written: {$path}\n";
}

$viewsBase = $basePath . '/resources/views';

// Head content (lines 4-51, inside head - skip outer html/head tags)
$headRaw = extractLines($lines, 4, 51);
$headRaw = preg_replace('/<title>.*?<\/title>/s', '<title>@yield(\'title\', config(\'app.name\', \'Wolmart\'))</title>', $headRaw);
$headRaw = convertToBlade($headRaw);
writeFile($viewsBase . '/frontend/layouts/head.blade.php', trim($headRaw) . "\n@stack('styles')\n");

// Header components
writeFile($viewsBase . '/components/frontend/header-top.blade.php', convertToBlade(extractLines($lines, 58, 100)));
writeFile($viewsBase . '/components/frontend/header-middle.blade.php', convertToBlade(extractLines($lines, 103, 222)));
writeFile($viewsBase . '/components/frontend/header-bottom.blade.php', convertToBlade(extractLines($lines, 225, 806)));

// main-header wraps header tag
$mainHeader = "<header class=\"header\">\n"
    . file_get_contents($viewsBase . '/components/frontend/header-top.blade.php')
    . "\n" . file_get_contents($viewsBase . '/components/frontend/header-middle.blade.php')
    . "\n" . file_get_contents($viewsBase . '/components/frontend/header-bottom.blade.php')
    . "\n</header>\n";
writeFile($viewsBase . '/frontend/layouts/main-header.blade.php', $mainHeader);

// Footer (4675-4861)
writeFile($viewsBase . '/frontend/layouts/footer.blade.php', convertToBlade(extractLines($lines, 4675, 4861)));

// Overlays
writeFile($viewsBase . '/components/frontend/sticky-footer.blade.php', convertToBlade(extractLines($lines, 4867, 4957)));
writeFile($viewsBase . '/components/frontend/scroll-top.blade.php', convertToBlade(extractLines($lines, 4960, 4966)));
writeFile($viewsBase . '/components/frontend/mobile-menu.blade.php', convertToBlade(extractLines($lines, 4968, 5429)));
writeFile($viewsBase . '/components/frontend/newsletter-popup.blade.php', convertToBlade(extractLines($lines, 5433, 5452)));
writeFile($viewsBase . '/components/frontend/quick-view-modal.blade.php', convertToBlade(extractLines($lines, 5456, 5622)));

// Footer scripts
$scripts = extractLines($lines, 5626, 5638);
$scripts = convertToBlade($scripts);
writeFile($viewsBase . '/frontend/layouts/footer-scripts.blade.php', $scripts . "\n@stack('scripts')\n");

// Main content sections
writeFile($viewsBase . '/frontend/partials/home/hero-slider.blade.php', convertToBlade(extractLines($lines, 813, 977)));
writeFile($viewsBase . '/frontend/partials/home/icon-boxes.blade.php', convertToBlade(extractLines($lines, 979, 1036)));
writeFile($viewsBase . '/frontend/partials/home/category-banners.blade.php', convertToBlade(extractLines($lines, 1038, 1074)));
writeFile($viewsBase . '/frontend/partials/home/deals-section.blade.php', convertToBlade(extractLines($lines, 1076, 1655)));
writeFile($viewsBase . '/frontend/partials/home/top-categories.blade.php', convertToBlade(extractLines($lines, 1658, 1760)));
writeFile($viewsBase . '/frontend/partials/home/popular-departments.blade.php', convertToBlade(extractLines($lines, 1763, 3290)));
writeFile($viewsBase . '/frontend/partials/home/product-wrapper-1.blade.php', convertToBlade(extractLines($lines, 3291, 3626)));
writeFile($viewsBase . '/frontend/partials/home/product-wrapper-2.blade.php', convertToBlade(extractLines($lines, 3628, 4000)));
writeFile($viewsBase . '/frontend/partials/home/product-wrapper-3.blade.php', convertToBlade(extractLines($lines, 4002, 4326)));
writeFile($viewsBase . '/frontend/partials/home/brands.blade.php', convertToBlade(extractLines($lines, 4327, 4427)));
writeFile($viewsBase . '/frontend/partials/home/blog-section.blade.php', convertToBlade(extractLines($lines, 4429, 4531)));
writeFile($viewsBase . '/frontend/partials/home/recent-views.blade.php', convertToBlade(extractLines($lines, 4533, 4669)) . "\n            </div>\n");

echo "Done.\n";
