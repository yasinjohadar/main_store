<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\BlogPost;
use App\Models\HomeProductBlock;
use App\Services\HomePageService;

class HomeController extends Controller
{
    public function __construct(
        protected HomePageService $homePage
    ) {}

    public function index()
    {
        $productWith = ['category', 'images', 'reviews'];

        $composed = $this->homePage->compose();

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

        $homeProductBlocks = $composed['homeProductBlocks'];
        $blockProducts = [
            HomeProductBlock::KEY_PRODUCT_1 => $this->productsForBlock($homeProductBlocks->get(HomeProductBlock::KEY_PRODUCT_1), 8, $productWith),
            HomeProductBlock::KEY_PRODUCT_2 => $this->productsForBlock($homeProductBlocks->get(HomeProductBlock::KEY_PRODUCT_2), 8, $productWith),
            HomeProductBlock::KEY_PRODUCT_3 => $this->productsForBlock($homeProductBlocks->get(HomeProductBlock::KEY_PRODUCT_3), 8, $productWith),
        ];

        $dealProducts = $onSale->take(3);
        $blogPosts = BlogPost::published()->with(['author', 'category'])->latest('published_at')->limit(4)->get();

        return view('frontend.pages.index', array_merge($composed, compact(
            'categories',
            'featuredProducts',
            'newArrivals',
            'bestSellers',
            'topRated',
            'onSale',
            'blockProducts',
            'dealProducts',
            'blogPosts',
        )));
    }

    protected function productsForCategory(?Category $category, int $limit, array $with)
    {
        $query = Product::active()->with($with)->withAvg('reviews', 'rating');

        if ($category) {
            $query->where('category_id', $category->id);
        }

        return $query->latest()->limit($limit)->get();
    }

    protected function productsForBlock(?HomeProductBlock $block, int $limit, array $with)
    {
        return $this->productsForCategory($block?->category, $limit, $with);
    }
}
