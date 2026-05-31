<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ShopCatalogService;
use App\Services\Seo\SeoResolverService;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function __construct(
        protected ShopCatalogService $catalogService,
        protected SeoResolverService $seoResolver,
    ) {}

    public function index(Request $request)
    {
        $layout = $this->catalogService->resolveLayout($request);
        $products = $this->catalogService->paginate($request);
        $categories = $this->catalogService->getCategories();
        $brands = $this->catalogService->getBrands();
        $infiniteScrollUrl = $this->catalogService->infiniteScrollUrl($request);

        $seo = $this->seoResolver->forSite('المتجر', 'تصفح منتجاتنا واختر ما يناسبك');

        $analyticsSearch = $request->filled('search') ? $request->search : null;
        $analyticsItemList = [
            'item_list_name' => $request->filled('search') ? 'Search Results' : 'Shop',
            'items' => $products->getCollection()->map(fn ($p) => [
                'item_id' => (string) $p->id,
                'item_name' => $p->name,
                'price' => (float) $p->price,
            ])->values()->all(),
        ];

        return view('frontend.pages.shop.index', compact(
            'products',
            'categories',
            'brands',
            'layout',
            'infiniteScrollUrl',
            'seo',
            'analyticsSearch',
            'analyticsItemList',
        ));
    }

    public function show($slug)
    {
        return redirect()->route('frontend.category.show', $slug);
    }
}
