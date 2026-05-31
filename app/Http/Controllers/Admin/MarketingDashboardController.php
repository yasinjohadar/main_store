<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Models\BlogPost;
use App\Models\Order;
use App\Models\PageView;
use App\Models\Product;
use App\Services\Seo\SeoScoreService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MarketingDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, SeoScoreService $seoScore)
    {
        [$start, $end] = $this->dateRange($request);

        $pageViews = PageView::whereBetween('created_at', [$start, $end])->count();
        $productViews = PageView::where('viewable_type', Product::class)->whereBetween('created_at', [$start, $end])->count();
        $blogViews = PageView::where('viewable_type', BlogPost::class)->whereBetween('created_at', [$start, $end])->count();

        $ctaClicks = AnalyticsEvent::where('event_name', 'cta_click')->whereBetween('created_at', [$start, $end])->count();
        $addToCart = AnalyticsEvent::where('event_name', 'add_to_cart')->whereBetween('created_at', [$start, $end])->count();

        $orders = Order::whereBetween('created_at', [$start, $end])->count();
        $revenue = (float) Order::whereBetween('created_at', [$start, $end])->sum('total');

        $topProducts = PageView::query()
            ->selectRaw('viewable_id, count(*) as views')
            ->where('viewable_type', Product::class)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('viewable_id')
            ->orderByDesc('views')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $product = Product::find($row->viewable_id);

                return [
                    'name' => $product?->name ?? '#' . $row->viewable_id,
                    'views' => $row->views,
                ];
            });

        $topPosts = PageView::query()
            ->selectRaw('viewable_id, count(*) as views')
            ->where('viewable_type', BlogPost::class)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('viewable_id')
            ->orderByDesc('views')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $post = BlogPost::find($row->viewable_id);

                return [
                    'title' => $post?->title ?? '#' . $row->viewable_id,
                    'views' => $row->views,
                    'cta_clicks' => AnalyticsEvent::where('event_name', 'cta_click')
                        ->where('payload->content_url', 'like', '%' . ($post?->slug ?? '') . '%')
                        ->count(),
                ];
            });

        $seoIssues = Product::query()
            ->where(function ($q) {
                $q->whereNull('meta_description')->orWhere('meta_description', '');
            })
            ->limit(10)
            ->get(['id', 'name', 'meta_title', 'meta_description']);

        $seoScores = Product::limit(20)->get()->map(function ($product) use ($seoScore) {
            return [
                'name' => $product->name,
                'score' => $seoScore->score([
                    'meta_title' => $product->meta_title ?: $product->name,
                    'meta_description' => $product->meta_description,
                    'focus_keyword' => $product->focus_keyword,
                    'og_image' => $product->og_image,
                    'canonical_url' => $product->canonical_url,
                ])['score'],
            ];
        })->sortBy('score')->take(10)->values();

        $utmSources = PageView::query()
            ->selectRaw('utm_source, count(*) as total')
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('utm_source')
            ->groupBy('utm_source')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $seoAudit = Cache::get('seo_audit_latest');

        return view('admin.pages.marketing.dashboard', [
            'filters' => ['start' => $start->toDateString(), 'end' => $end->toDateString()],
            'kpis' => compact('pageViews', 'productViews', 'blogViews', 'ctaClicks', 'addToCart', 'orders', 'revenue'),
            'topProducts' => $topProducts,
            'topPosts' => $topPosts,
            'seoIssues' => $seoIssues,
            'seoScores' => $seoScores,
            'utmSources' => $utmSources,
            'seoAudit' => $seoAudit,
        ]);
    }

    protected function dateRange(Request $request): array
    {
        $end = Carbon::parse($request->input('end', now()->toDateString()))->endOfDay();
        $start = Carbon::parse($request->input('start', now()->subDays(30)->toDateString()))->startOfDay();

        return [$start, $end];
    }
}
