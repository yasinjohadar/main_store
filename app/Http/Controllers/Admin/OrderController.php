<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\OrderStatus;
use App\Models\StockMovement;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\LoyaltyService;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(
        protected LoyaltyService $loyaltyService
    ) {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Order::with(['user', 'status', 'items'])->orderByDesc('created_at');

        if ($request->filled('order_number')) {
            $query->where('order_number', 'like', '%' . $request->input('order_number') . '%');
        }
        if ($request->filled('status')) {
            $query->whereHas('status', fn ($q) => $q->where('slug', $request->input('status')));
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        $orders = $query->paginate(15);
        $statuses = OrderStatus::orderBy('order')->get();

        return view('admin.pages.orders.index', compact('orders', 'statuses'));
    }

    public function show(Order $order)
    {
        $order->load([
            'user',
            'status',
            'items.product',
            'items.variant',
            'addresses',
            'payments',
            'statusHistory.user',
            'statusHistory.newStatus',
            'returns.items',
        ]);
        return view('admin.pages.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'order_status_id' => 'required|exists:order_statuses,id',
            'admin_note' => 'nullable|string|max:2000',
        ]);

        $oldStatusId = $order->order_status_id;
        $newStatusId = (int) $data['order_status_id'];

        $oldStatus = OrderStatus::find($oldStatusId);
        $newStatus = OrderStatus::find($newStatusId);

        $order->update([
            'order_status_id' => $newStatusId,
            'admin_note' => $data['admin_note'] ?? $order->admin_note,
        ]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'old_status_id' => $oldStatusId,
            'new_status_id' => $newStatusId,
            'changed_by' => Auth::id(),
            'note' => $data['admin_note'] ?? null,
        ]);

        app(ActivityLogger::class)->orderStatusChanged(
            $order,
            $oldStatus ? $oldStatus->name : '—',
            $newStatus ? $newStatus->name : '—'
        );

        // منح نقاط الولاء عند إكمال الطلب
        $this->loyaltyService->awardPointsForOrder($order->fresh(['status', 'user']));

        // منطق إعادة المخزون عند إلغاء/استرجاع الطلب
        if ($oldStatus && $newStatus) {
            $shouldReturnStock = in_array($newStatus->slug, ['cancelled', 'refunded'], true);
            $wasReducingStatus = in_array($oldStatus->slug, ['pending', 'processing', 'completed'], true);

            if ($shouldReturnStock && $wasReducingStatus) {
                $order->loadMissing('items.product', 'items.variant');

                foreach ($order->items as $item) {
                    $product = $item->product;
                    if (!$product || !$product->track_quantity) {
                        continue;
                    }

                    $variant = $item->variant;
                    $qty = $item->quantity;

                    if ($variant) {
                        $variant->increment('stock_quantity', $qty);
                    } else {
                        $product->increment('stock_quantity', $qty);
                    }

                    StockMovement::create([
                        'product_id' => $product->id,
                        'product_variant_id' => $variant?->id,
                        'quantity_change' => $qty,
                        'reason' => $newStatus->slug === 'refunded' ? 'order_refund' : 'order_cancel',
                        'reference_type' => Order::class,
                        'reference_id' => $order->id,
                        'note' => 'إرجاع مخزون بسبب تغيير حالة الطلب إلى ' . $newStatus->name,
                        'created_by' => Auth::id(),
                    ]);
                }
            }
        }

        return back()->with('success', 'تم تحديث حالة الطلب وتسجيل الملاحظة.');
    }
}
