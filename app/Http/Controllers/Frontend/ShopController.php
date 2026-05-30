<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ShopCatalogService;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function __construct(
        protected ShopCatalogService $catalogService
    ) {}

    public function index(Request $request)
    {
        $layout = $this->catalogService->resolveLayout($request);
        $products = $this->catalogService->paginate($request);
        $categories = $this->catalogService->getCategories();
        $brands = $this->catalogService->getBrands();
        $infiniteScrollUrl = $this->catalogService->infiniteScrollUrl($request);

        return view('frontend.pages.shop.index', compact(
            'products',
            'categories',
            'brands',
            'layout',
            'infiniteScrollUrl'
        ));
    }

    public function show($slug)
    {
        return redirect()->route('frontend.category.show', $slug);
    }
}
