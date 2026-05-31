<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\Analytics\PageViewTracker;
use App\Services\Seo\SeoResolverService;
use App\Services\ShopCatalogService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        protected ShopCatalogService $catalogService,
        protected SeoResolverService $seoResolver,
        protected PageViewTracker $pageViewTracker,
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

        $this->pageViewTracker->track($request, $category);
        $seo = $this->seoResolver->forCategory($category);

        return view('frontend.pages.category.show', compact(
            'category',
            'products',
            'categories',
            'brands',
            'layout',
            'infiniteScrollUrl',
            'seo',
        ));
    }
}
