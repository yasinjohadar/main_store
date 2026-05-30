<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\Services\ProductLayoutService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected ProductLayoutService $layoutService
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

        return view('frontend.pages.product.show', compact(
            'product',
            'reviews',
            'relatedProducts',
            'vendorProducts',
            'frequentlyBoughtProducts',
            'sidebarProducts',
            'productLayout',
            'productLayoutPartial'
        ));
    }

    public function quickView($slug)
    {
        $product = Product::with(['category', 'brand', 'images', 'attributes.values', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->where('slug', $slug)
            ->where('status', 'active')
            ->where('is_visible', true)
            ->firstOrFail();

        return view('frontend.partials.quick-view-content', compact('product'));
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
