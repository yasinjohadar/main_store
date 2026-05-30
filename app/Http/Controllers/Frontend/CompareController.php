<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CompareService;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    public function __construct(
        protected CompareService $compare
    ) {}

    public function index()
    {
        $compareItems = $this->compare->products();

        return view('frontend.pages.compare.index', compact('compareItems'));
    }

    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $productId = (int) $validated['product_id'];
        $added = $this->compare->toggle($productId);
        $product = Product::findOrFail($productId);

        return response()->json([
            'success' => true,
            'added' => $added,
            'message' => $added ? 'تمت إضافة المنتج للمقارنة' : 'تمت إزالة المنتج من المقارنة',
            'compare_count' => $this->compare->count(),
            'compare_url' => route('frontend.compare'),
            'products' => $this->compare->barPayload(),
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'url' => route('frontend.product.show', $product->slug),
                'image' => $product->primary_image_url,
            ],
        ]);
    }

    public function destroy(Request $request, Product $product)
    {
        $this->compare->remove($product->id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تمت إزالة المنتج من المقارنة',
                'compare_count' => $this->compare->count(),
                'compare_url' => route('frontend.compare'),
                'products' => $this->compare->barPayload(),
            ]);
        }

        return back()->with('success', 'تمت إزالة المنتج من المقارنة');
    }

    public function clear(Request $request)
    {
        $this->compare->clear();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم مسح قائمة المقارنة',
                'compare_count' => 0,
                'compare_url' => route('frontend.compare'),
                'products' => [],
            ]);
        }

        return back()->with('success', 'تم مسح قائمة المقارنة');
    }
}
