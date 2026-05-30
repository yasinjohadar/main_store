<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\WishlistService;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct(
        protected WishlistService $wishlist
    ) {}

    public function index()
    {
        $ids = $this->wishlist->ids();

        $wishlistItems = Product::query()
            ->whereIn('id', $ids)
            ->where('status', 'active')
            ->where('is_visible', true)
            ->with(['images', 'reviews', 'category'])
            ->withAvg('reviews', 'rating')
            ->get()
            ->sortBy(fn ($product) => array_search($product->id, $ids, true))
            ->values();

        return view('frontend.pages.wishlist.index', compact('wishlistItems'));
    }

    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $productId = (int) $validated['product_id'];
        $added = $this->wishlist->toggle($productId);
        $product = Product::findOrFail($productId);

        return response()->json([
            'success' => true,
            'added' => $added,
            'message' => $added ? 'تمت إضافة المنتج إلى المفضلة' : 'تمت إزالة المنتج من المفضلة',
            'wishlist_count' => $this->wishlist->count(),
            'product' => [
                'name' => $product->name,
                'slug' => $product->slug,
                'url' => route('frontend.product.show', $product->slug),
                'image' => $product->primary_image_url,
            ],
        ]);
    }

    public function destroy(Request $request, Product $product)
    {
        $this->wishlist->remove($product->id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تمت إزالة المنتج من المفضلة',
                'wishlist_count' => $this->wishlist->count(),
            ]);
        }

        return back()->with('success', 'تمت إزالة المنتج من المفضلة');
    }
}
