<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\BlogPost;

class HomeController extends Controller
{
    public function index()
    {
        $productWith = ['category', 'images', 'reviews'];

        $categories = Category::active()->root()->ordered()->withCount(['products' => function ($q) {
            $q->where('status', 'active')->where('is_visible', true);
        }])->limit(12)->get();

        $baseQuery = fn () => Product::active()
            ->with($productWith)
            ->withAvg('reviews', 'rating');

        $newArrivals = $baseQuery()->latest()->limit(10)->get();
        $bestSellers = $baseQuery()->orderByDesc('stock_quantity')->limit(10)->get();
        $topRated = $baseQuery()->has('reviews')->orderByDesc('reviews_avg_rating')->limit(10)->get();
        $featuredProducts = $baseQuery()->featured()->latest()->limit(10)->get();
        $onSale = $baseQuery()
            ->whereNotNull('compare_at_price')
            ->whereColumn('compare_at_price', '>', 'price')
            ->latest()
            ->limit(10)
            ->get();

        $sectionCategories = $categories->take(3);
        $fashionProducts = $this->productsForCategory($sectionCategories->get(0), 8, $productWith);
        $electronicsProducts = $this->productsForCategory($sectionCategories->get(1), 8, $productWith);
        $homeProducts = $this->productsForCategory($sectionCategories->get(2), 8, $productWith);

        $dealProducts = $onSale->take(3);

        $blogPosts = BlogPost::published()->with(['author', 'category'])->latest('published_at')->limit(4)->get();

        return view('frontend.pages.index', compact(
            'categories',
            'featuredProducts',
            'newArrivals',
            'bestSellers',
            'topRated',
            'onSale',
            'fashionProducts',
            'electronicsProducts',
            'homeProducts',
            'sectionCategories',
            'dealProducts',
            'blogPosts'
        ));
    }

    protected function productsForCategory(?Category $category, int $limit, array $with)
    {
        $query = Product::active()->with($with)->withAvg('reviews', 'rating');

        if ($category) {
            $query->where('category_id', $category->id);
        }

        return $query->latest()->limit($limit)->get();
    }
}
