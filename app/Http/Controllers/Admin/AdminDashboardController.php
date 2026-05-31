<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Order;
use App\Models\PageView;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        [$start, $end, $preset] = $this->resolveDateRange($request);
        $days = max(1, $start->copy()->startOfDay()->diffInDays($end->copy()->startOfDay()) + 1);
        [$prevStart, $prevEnd] = $this->previousPeriod($start, $end, $days);

        $paidStatuses = ['processing', 'completed'];

        $currentOrders = $this->ordersInRange($start, $end, $paidStatuses);
        $prevOrders = $this->ordersInRange($prevStart, $prevEnd, $paidStatuses);

        $currentRevenue = (float) $currentOrders->sum('total');
        $prevRevenue = (float) $prevOrders->sum('total');
        $ordersCount = $currentOrders->count();
        $prevOrdersCount = $prevOrders->count();
        $aov = $ordersCount > 0 ? $currentRevenue / $ordersCount : 0.0;
        $prevAov = $prevOrdersCount > 0 ? $prevRevenue / $prevOrdersCount : 0.0;

        $pageViews = PageView::whereBetween('created_at', [$start, $end])->count();
        $prevPageViews = PageView::whereBetween('created_at', [$prevStart, $prevEnd])->count();
        $addToCart = AnalyticsEvent::where('event_name', 'add_to_cart')->whereBetween('created_at', [$start, $end])->count();
        $prevAddToCart = AnalyticsEvent::where('event_name', 'add_to_cart')->whereBetween('created_at', [$prevStart, $prevEnd])->count();

        $newCustomers = User::whereBetween('created_at', [$start, $end])->count();
        $prevNewCustomers = User::whereBetween('created_at', [$prevStart, $prevEnd])->count();

        $kpis = [
            'revenue' => ['value' => $currentRevenue, 'trend' => $this->trend($currentRevenue, $prevRevenue)],
            'orders' => ['value' => $ordersCount, 'trend' => $this->trend($ordersCount, $prevOrdersCount)],
            'aov' => ['value' => $aov, 'trend' => $this->trend($aov, $prevAov)],
            'page_views' => ['value' => $pageViews, 'trend' => $this->trend($pageViews, $prevPageViews)],
            'add_to_cart' => ['value' => $addToCart, 'trend' => $this->trend($addToCart, $prevAddToCart)],
            'new_customers' => ['value' => $newCustomers, 'trend' => $this->trend($newCustomers, $prevNewCustomers)],
        ];

        $funnel = [
            ['label' => 'زيارات', 'value' => $pageViews],
            ['label' => 'إضافة للسلة', 'value' => $addToCart],
            ['label' => 'طلبات', 'value' => Order::whereBetween('created_at', [$start, $end])->count()],
            ['label' => 'مبيعات مؤكدة', 'value' => $ordersCount],
        ];

        $recentOrders = Order::with(['user', 'status'])
            ->latest()
            ->limit(8)
            ->get();

        $pendingReviews = Review::where('status', 'pending')->count();
        $recentReviews = Review::with('user', 'product')->latest()->limit(5)->get();

        $threshold = (int) config('store.low_stock_threshold', 5);
        $lowStockProducts = Product::query()
            ->where('track_quantity', true)
            ->where('stock_quantity', '<=', $threshold)
            ->where('status', 'active')
            ->orderBy('stock_quantity')
            ->limit(6)
            ->get(['id', 'name', 'sku', 'stock_quantity']);

        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('order_statuses', 'orders.order_status_id', '=', 'order_statuses.id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->whereIn('order_statuses.slug', $paidStatuses)
            ->selectRaw('order_items.product_name, SUM(order_items.quantity) as qty, SUM(order_items.total) as revenue')
            ->groupBy('order_items.product_name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        $catalogStats = [
            'categories' => Category::count(),
            'products' => Product::count(),
            'reviews' => Review::count(),
            'posts' => BlogPost::count(),
        ];

        $pendingOrdersCount = Order::whereHas('status', fn ($q) => $q->where('slug', 'pending'))->count();
        $lowStockCount = Product::query()
            ->where('track_quantity', true)
            ->where('stock_quantity', '<=', $threshold)
            ->where('status', 'active')
            ->count();
        $conversionRate = $pageViews > 0 ? round(($ordersCount / $pageViews) * 100, 2) : 0.0;

        return view('admin.dashboard', [
            'preset' => $preset,
            'filters' => ['start' => $start->toDateString(), 'end' => $end->toDateString()],
            'kpis' => $kpis,
            'funnel' => $funnel,
            'recentOrders' => $recentOrders,
            'pendingReviews' => $pendingReviews,
            'recentReviews' => $recentReviews,
            'lowStockProducts' => $lowStockProducts,
            'topProducts' => $topProducts,
            'catalogStats' => $catalogStats,
            'lowStockThreshold' => $threshold,
            'pendingOrdersCount' => $pendingOrdersCount,
            'lowStockCount' => $lowStockCount,
            'conversionRate' => $conversionRate,
        ]);
    }

    protected function resolveDateRange(Request $request): array
    {
        $preset = $request->input('preset', 'last_7_days');
        $today = Carbon::today();

        switch ($preset) {
            case 'today':
                $start = $today;
                $end = $today;
                break;
            case 'last_30_days':
                $start = $today->copy()->subDays(29);
                $end = $today;
                break;
            case 'this_month':
                $start = $today->copy()->startOfMonth();
                $end = $today;
                break;
            case 'last_7_days':
            default:
                $start = $today->copy()->subDays(6);
                $end = $today;
                $preset = 'last_7_days';
                break;
        }

        return [$start->copy()->startOfDay(), $end->copy()->endOfDay(), $preset];
    }

    protected function previousPeriod(Carbon $start, Carbon $end, int $days): array
    {
        $prevEnd = $start->copy()->subDay()->endOfDay();
        $prevStart = $prevEnd->copy()->subDays($days - 1)->startOfDay();

        return [$prevStart, $prevEnd];
    }

    protected function ordersInRange(Carbon $start, Carbon $end, array $statusSlugs)
    {
        return Order::with('status')
            ->whereBetween('created_at', [$start, $end])
            ->whereHas('status', fn ($q) => $q->whereIn('slug', $statusSlugs))
            ->get();
    }

    protected function trend(float|int $current, float|int $previous): ?float
    {
        if ($previous <= 0) {
            return $current > 0 ? 100.0 : null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
