<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.pages.products.import');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);
        $imported = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row[0]) || str_starts_with((string) $row[0], 'variant:')) {
                continue;
            }

            try {
                Product::updateOrCreate(
                    ['sku' => $row[3] ?: null, 'slug' => $row[2] ?: Str::slug($row[1])],
                    [
                        'name' => $row[1],
                        'slug' => $row[2] ?: Str::slug($row[1]),
                        'price' => $row[4] ?? 0,
                        'compare_at_price' => $row[5] ?: null,
                        'stock_quantity' => (int) ($row[6] ?? 0),
                        'category_id' => $row[7] ?: null,
                        'status' => $row[8] ?: 'draft',
                        'is_digital' => ! empty($row[9]),
                        'upsell_product_ids' => json_decode($row[10] ?? '[]', true),
                        'cross_sell_product_ids' => json_decode($row[11] ?? '[]', true),
                        'is_visible' => ($row[8] ?? '') === 'active',
                    ],
                );
                $imported++;
            } catch (\Throwable $e) {
                $errors[] = ($row[1] ?? 'unknown') . ': ' . $e->getMessage();
            }
        }

        fclose($handle);

        $message = "تم استيراد {$imported} منتج.";
        if ($errors) {
            $message .= ' أخطاء: ' . count($errors);
        }

        return back()->with('success', $message)->with('import_errors', $errors);
    }
}
