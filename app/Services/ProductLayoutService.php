<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductLayoutService
{
    public const LAYOUTS = ['default', 'without-sidebar', 'gallery', 'variable'];

    public const DEFAULT = 'default';

    public function resolve(Request $request, Product $product): string
    {
        $layout = session('product_layout', self::DEFAULT);

        if ($request->has('layout') && $this->isValid($request->input('layout'))) {
            $layout = $request->input('layout');
        }

        if ($layout === 'variable' && ! $product->variants()->exists()) {
            $layout = self::DEFAULT;
        }

        session(['product_layout' => $layout]);

        return $layout;
    }

    public function partial(string $layout): string
    {
        return 'frontend.partials.product.layouts.' . $layout;
    }

    protected function isValid(string $layout): bool
    {
        return in_array($layout, self::LAYOUTS, true);
    }
}
