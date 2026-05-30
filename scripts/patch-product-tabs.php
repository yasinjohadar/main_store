<?php

$path = dirname(__DIR__) . '/resources/views/frontend/partials/product/tabs.blade.php';
$lines = file($path, FILE_IGNORE_NEW_LINES);

$out = [];
foreach ($lines as $i => $line) {
    $num = $i + 1;

    if ($num === 13) {
        $out[] = '                                        <a href="#product-tab-reviews" class="nav-link">Customer Reviews ({{ $reviews->count() }})</a>';
        continue;
    }

    if ($num === 21) {
        $out[] = '                                                @if($product->description)';
        $out[] = '                                                    <div class="mb-4">{!! nl2br(e($product->description)) !!}</div>';
        $out[] = '                                                @elseif($product->short_description)';
        $out[] = '                                                    <p class="mb-4">{{ $product->short_description }}</p>';
        $out[] = '                                                @else';
        $out[] = '                                                    <p class="mb-4">No description available for this product.</p>';
        $out[] = '                                                @endif';
        continue;
    }

    if ($num >= 22 && $num <= 32) {
        continue;
    }

    if ($num === 156) {
        $out[] = '                                    @include(\'frontend.partials.product.reviews-tab\')';
        continue;
    }

    if ($num > 156 && $num <= 742) {
        continue;
    }

    $out[] = $line;
}

file_put_contents($path, implode("\n", $out) . "\n");
echo "Patched tabs.blade.php\n";
