<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ShopCatalogService;
use Illuminate\Http\Request;

class ShopCatalogController extends Controller
{
    public function __construct(
        protected ShopCatalogService $catalogService
    ) {}

    public function products(Request $request)
    {
        $layout = $this->catalogService->resolveLayout($request);
        $category = null;

        if ($request->filled('category_slug')) {
            $category = Category::where('slug', $request->category_slug)
                ->where('status', 'active')
                ->firstOrFail();
        }

        $products = $this->catalogService->paginate($request, $category);

        return response()->json([
            'html' => $this->catalogService->renderProductItems($products, $layout),
            'has_more' => $products->hasMorePages(),
            'next_page' => $products->currentPage() + 1,
        ]);
    }
}
