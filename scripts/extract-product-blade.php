<?php

/**
 * Extract demo-rtl-product.html → frontend/partials/product/*.blade.php
 * Run: php scripts/extract-product-blade.php
 */

$basePath = dirname(__DIR__);
$htmlFile = $basePath . '/resources/views/frontend/html pages/demo-rtl-product.html';
$lines = file($htmlFile, FILE_IGNORE_NEW_LINES);
$viewsBase = $basePath . '/resources/views';

function extractLines(array $lines, int $start, int $end): string
{
    return implode("\n", array_slice($lines, $start - 1, $end - $start + 1));
}

function convertToBlade(string $html): string
{
    $html = preg_replace('/url\(assets\/([^)]+)\)/', "url({{ asset('frontend/assets/$1') }})", $html);

    $html = preg_replace_callback(
        '/\b(href|src|data-zoom-image)="assets\/([^"]+)"/',
        fn ($m) => $m[1] . '="{{ asset(\'frontend/assets/' . $m[2] . '\') }}"',
        $html
    );

    $routeMap = [
        'href="demo-rtl.html"' => 'href="{{ route(\'frontend.home\') }}"',
        'href="demo-rtl-shop.html"' => 'href="{{ route(\'frontend.shop.index\') }}"',
        'href="demo-rtl-product.html"' => 'href="{{ route(\'frontend.shop.index\') }}"',
        'href="cart.html"' => 'href="{{ route(\'frontend.cart.index\') }}"',
        'href="blog-listing.html"' => 'href="{{ route(\'frontend.blog.index\') }}"',
        'href="shop-boxed-banner.html"' => 'href="{{ route(\'frontend.shop.index\') }}"',
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

$out = $viewsBase . '/frontend/partials/product';

writeFile("$out/frequently-bought.blade.php", convertToBlade(extractLines($lines, 1059, 1119)));
writeFile("$out/tabs.blade.php", convertToBlade(extractLines($lines, 1120, 1863)));
writeFile("$out/vendor-products.blade.php", convertToBlade(extractLines($lines, 1864, 2057)));
writeFile("$out/sidebar.blade.php", convertToBlade(extractLines($lines, 2236, 2445)));

echo "Done.\n";
