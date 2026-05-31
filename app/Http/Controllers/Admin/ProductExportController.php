<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function export(): StreamedResponse
    {
        $filename = 'products-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'id', 'name', 'slug', 'sku', 'price', 'compare_at_price', 'stock_quantity',
                'category_id', 'status', 'is_digital', 'upsell_ids', 'cross_sell_ids',
            ]);

            Product::with('variants')->chunk(100, function ($products) use ($handle) {
                foreach ($products as $product) {
                    fputcsv($handle, [
                        $product->id,
                        $product->name,
                        $product->slug,
                        $product->sku,
                        $product->price,
                        $product->compare_at_price,
                        $product->stock_quantity,
                        $product->category_id,
                        $product->status,
                        $product->is_digital ? 1 : 0,
                        json_encode($product->upsell_product_ids ?? []),
                        json_encode($product->cross_sell_product_ids ?? []),
                    ]);

                    foreach ($product->variants as $variant) {
                        fputcsv($handle, [
                            'variant:' . $product->id,
                            $variant->name ?? '',
                            '',
                            $variant->sku,
                            $variant->price,
                            '',
                            $variant->stock_quantity,
                            '',
                            '',
                            '',
                            '',
                            '',
                        ]);
                    }
                }
            });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
