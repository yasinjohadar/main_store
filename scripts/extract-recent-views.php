<?php

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
        fn ($m) => $m[1] . '="{{ asset(\'frontend/assets/' . $m[2] . '\') }}"',
        $html
    );
    $html = str_replace('href="demo-rtl-product.html"', 'href="{{ route(\'frontend.shop.index\') }}"', $html);
    return $html;
}

$content = convertToBlade(extractLines($lines, 4533, 4669)) . "\n            </div>\n";
$path = $basePath . '/resources/views/frontend/partials/home/recent-views.blade.php';
file_put_contents($path, $content);
echo "Written: $path\n";
