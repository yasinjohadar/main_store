<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Product::with('category')
            ->where('track_quantity', true)
            ->orderBy('stock_quantity')
            ->orderBy('name');

        if ($request->filled('query')) {
            $q = $request->input('query');
            $query->where(function ($qBuilder) use ($q) {
                $qBuilder->where('name', 'like', "%$q%")
                    ->orWhere('sku', 'like', "%$q%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('stock_status')) {
            $status = $request->input('stock_status');
            if ($status === 'out') {
                $query->where('stock_quantity', '<=', 0);
            } elseif ($status === 'low') {
                $threshold = config('store.low_stock_threshold', 5);
                $query->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', $threshold);
            } elseif ($status === 'in') {
                $threshold = config('store.low_stock_threshold', 5);
                $query->where('stock_quantity', '>', $threshold);
            }
        }

        $products = $query->paginate(20);
        $categories = Category::orderBy('name')->get();
        $lowStockThreshold = config('store.low_stock_threshold', 5);

        return view('admin.pages.inventory.index', compact('products', 'categories', 'lowStockThreshold'));
    }

    public function adjust(Request $request, Product $product)
    {
        $data = $request->validate([
            'new_quantity' => ['required', 'integer', 'min:0'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $old = (int) $product->stock_quantity;
        $new = (int) $data['new_quantity'];

        if ($old === $new) {
            return back()->with('success', 'لم يتم تغيير الكمية (القيمة نفسها).');
        }

        $change = $new - $old;
        $product->update(['stock_quantity' => $new]);

        $product->stockMovements()->create([
            'product_variant_id' => null,
            'quantity_change' => $change,
            'reason' => 'manual_adjustment',
            'reference_type' => null,
            'reference_id' => null,
            'note' => $data['note'] ?: 'تعديل يدوي للكمية من ' . $old . ' إلى ' . $new,
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'تم تحديث كمية المخزون وتسجيل الحركة.');
    }
}

