<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\Services\ProductLayoutService;
use App\Services\ProductVariantOptionService;
use App\Services\Analytics\PageViewTracker;
use App\Services\Seo\SeoResolverService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected ProductLayoutService $layoutService,
        protected SeoResolverService $seoResolver,
        protected PageViewTracker $pageViewTracker,
        protected ProductVariantOptionService $variantOptionService,
    ) {}

    public function show(Request $request, $slug)
    {
        $product = Product::with([
            'category',
            'brand',
            'images',
            'attributes.values',
            'variants.attributeValues.attribute',
            'reviews',
        ])
            ->withAvg('reviews', 'rating')
            ->where('slug', $slug)
            ->where('status', 'active')
            ->where('is_visible', true)
            ->firstOrFail();

        $productLayout = $this->layoutService->resolve($request, $product);
        $productLayoutPartial = $this->layoutService->partial($productLayout);

        $reviews = Review::where('product_id', $product->id)
            ->where('status', 'approved')
            ->with('user')
            ->latest()
            ->get();

        $relatedProducts = $this->relatedQuery($product)->limit(4)->get();
        $upsellProducts = $product->upsellProducts();
        $crossSellProducts = $product->crossSellProducts();

        $vendorProducts = Product::active()
            ->where('id', '!=', $product->id)
            ->when($product->brand_id, fn ($q) => $q->where('brand_id', $product->brand_id))
            ->when(! $product->brand_id && $product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
            ->with(['category', 'images', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->inRandomOrder()
            ->limit(4)
            ->get();

        $frequentlyBoughtProducts = Product::active()
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
            ->with('images')
            ->inRandomOrder()
            ->limit(3)
            ->get();

        $sidebarProducts = $relatedProducts->count() >= 3
            ? $relatedProducts
            : Product::active()
                ->where('id', '!=', $product->id)
                ->with(['images', 'reviews'])
                ->withAvg('reviews', 'rating')
                ->inRandomOrder()
                ->limit(6)
                ->get();

        $this->pageViewTracker->track($request, $product);

        $seo = $this->seoResolver->forProduct($product);

        $variantOptions = $this->variantOptionService->forProduct($product);
        $variantsJson = $this->variantOptionService->variantsPayload($product);

        $analyticsProductView = [
            'item_id' => (string) $product->id,
            'item_name' => $product->name,
            'value' => (float) $product->price,
            'currency' => 'SAR',
        ];

        return view('frontend.pages.product.show', compact(
            'product',
            'reviews',
            'relatedProducts',
            'upsellProducts',
            'crossSellProducts',
            'vendorProducts',
            'frequentlyBoughtProducts',
            'sidebarProducts',
            'productLayout',
            'productLayoutPartial',
            'variantOptions',
            'variantsJson',
            'seo',
            'analyticsProductView',
        ));
    }

    public function quickView($slug)
    {
        $product = Product::with(['category', 'brand', 'images', 'attributes.values', 'variants.attributeValues.attribute', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->where('slug', $slug)
            ->where('status', 'active')
            ->where('is_visible', true)
            ->firstOrFail();

        $variantOptions = $this->variantOptionService->forProduct($product);
        $variantsJson = $this->variantOptionService->variantsPayload($product);

        return view('frontend.partials.quick-view-content', compact('product', 'variantOptions', 'variantsJson'));
    }

    protected function relatedQuery(Product $product)
    {
        return Product::active()
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
            ->with(['category', 'images', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->inRandomOrder();
    }
}
