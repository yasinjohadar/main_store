<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ShopCatalogService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        protected ShopCatalogService $catalogService
    ) {}

    public function show(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)
            ->where('status', 'active')
            ->withCount('products')
            ->firstOrFail();

        $layout = $this->catalogService->resolveLayout($request);
        $products = $this->catalogService->paginate($request, $category);
        $categories = $this->catalogService->getCategories();
        $brands = $this->catalogService->getBrands();
        $infiniteScrollUrl = $this->catalogService->infiniteScrollUrl($request, $category);

        return view('frontend.pages.category.show', compact(
            'category',
            'products',
            'categories',
            'brands',
            'layout',
            'infiniteScrollUrl'
        ));
    }
}
