<?php

/**
 * Extract demo-rtl-shop.html → frontend/partials/shop/*.blade.php
 * Run: php scripts/extract-shop-blade.php
 */

$basePath = dirname(__DIR__);
$htmlFile = $basePath . '/resources/views/frontend/html pages/demo-rtl-shop.html';
$lines = file($htmlFile, FILE_IGNORE_NEW_LINES);
$out = $basePath . '/resources/views/frontend/partials/shop';

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

    $routes = [
        'href="demo-rtl.html"' => 'href="{{ route(\'frontend.home\') }}"',
        'href="demo-rtl-shop.html"' => 'href="{{ route(\'frontend.shop.index\') }}"',
        'href="demo-rtl-product.html"' => 'href="{{ route(\'frontend.shop.index\') }}"',
        'href="shop-list.html"' => 'href="{{ route(\'frontend.shop.index\') }}"',
    ];

    foreach ($routes as $search => $replace) {
        $html = str_replace($search, $replace, $html);
    }

    return $html;
}

function write(string $path, string $content): void
{
    if (! is_dir(dirname($path))) {
        mkdir(dirname($path), 0755, true);
    }
    file_put_contents($path, $content);
    echo "Written: $path\n";
}

write("$out/brands.blade.php", convertToBlade(extractLines($lines, 839, 907)));
write("$out/banner-default.blade.php", convertToBlade(extractLines($lines, 826, 836)));

echo "Done.\n";
