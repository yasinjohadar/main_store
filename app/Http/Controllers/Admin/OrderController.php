<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\RespondsWithAdminTable;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\OrderStatus;
use App\Models\StockMovement;
use App\Services\LoyaltyService;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    use RespondsWithAdminTable;

    public function __construct(
        protected LoyaltyService $loyaltyService
    ) {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Order::with(['user', 'status', 'items'])
            ->withCount('items')
            ->orderByDesc('created_at');

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

        $perPageOptions = [10, 15, 25, 50, 100];
        $perPage = (int) $request->input('per_page', 15);
        if (! in_array($perPage, $perPageOptions, true)) {
            $perPage = 15;
        }

        $orders = $query->paginate($perPage);
        $statuses = OrderStatus::orderBy('order')->get();

        $data = compact('orders', 'statuses');

        return $this->adminTableView(
            $request,
            'admin.pages.orders.index',
            'admin.pages.orders.partials.table-body',
            $data
        );
    }

    public function show(Order $order)
    {
        $order->load([
            'user',
            'status',
            'shippingMethod',
            'items.product',
            'items.variant',
            'addresses',
            'payments.paymentMethod',
            'statusHistory.user',
            'statusHistory.newStatus',
            'returns.items',
            'notes.user',
        ]);

        $refundableAmount = app(\App\Services\OrderRefundService::class)->refundableAmount($order);

        return view('admin.pages.orders.show', compact('order', 'refundableAmount'));
    }

    public function inlineUpdateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'order_status_id' => 'required|exists:order_statuses,id',
        ]);

        $newStatusId = (int) $data['order_status_id'];

        if ($newStatusId !== (int) $order->order_status_id) {
            $this->applyOrderStatusChange($order, $newStatusId);
            $order->refresh();
        }

        $order->load('status');

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الطلب.',
            'display' => $this->orderStatusDisplay($order),
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'order_status_id' => 'required|exists:order_statuses,id',
            'admin_note' => 'nullable|string|max:2000',
        ]);

        $newStatusId = (int) $data['order_status_id'];

        if ($newStatusId !== (int) $order->order_status_id) {
            $this->applyOrderStatusChange($order, $newStatusId, $data['admin_note'] ?? null);
        } elseif (array_key_exists('admin_note', $data)) {
            $order->update(['admin_note' => $data['admin_note']]);
        }

        return back()->with('success', 'تم تحديث حالة الطلب وتسجيل الملاحظة.');
    }

    protected function applyOrderStatusChange(Order $order, int $newStatusId, ?string $adminNote = null): void
    {
        $oldStatusId = (int) $order->order_status_id;
        $oldStatus = OrderStatus::find($oldStatusId);
        $newStatus = OrderStatus::find($newStatusId);

        $order->update([
            'order_status_id' => $newStatusId,
            'admin_note' => $adminNote ?? $order->admin_note,
        ]);

        if ($oldStatusId !== $newStatusId) {
            \App\Events\OrderStatusChanged::dispatch(
                $order->fresh(['status', 'user']),
                $oldStatus?->slug
            );
        }

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'old_status_id' => $oldStatusId,
            'new_status_id' => $newStatusId,
            'changed_by' => Auth::id(),
            'note' => $adminNote,
        ]);

        app(ActivityLogger::class)->orderStatusChanged(
            $order,
            $oldStatus ? $oldStatus->name : '—',
            $newStatus ? $newStatus->name : '—'
        );

        $this->loyaltyService->awardPointsForOrder($order->fresh(['status', 'user']));

        if ($oldStatus && $newStatus) {
            $shouldReturnStock = in_array($newStatus->slug, ['cancelled', 'refunded'], true);
            $wasReducingStatus = in_array($oldStatus->slug, ['pending', 'processing', 'completed'], true);

            if ($shouldReturnStock && $wasReducingStatus) {
                $order->loadMissing('items.product', 'items.variant');

                foreach ($order->items as $item) {
                    $product = $item->product;
                    if (! $product || ! $product->track_quantity) {
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
    }

    protected function orderStatusDisplay(Order $order): array
    {
        return [
            'status_id' => $order->order_status_id,
            'status_name' => $order->status->name,
            'status_color' => $order->status->color ?? '#6c757d',
        ];
    }

    public function addNote(Request $request, Order $order)
    {
        $data = $request->validate([
            'note' => 'required|string|max:2000',
            'is_customer_visible' => 'boolean',
            'is_private' => 'boolean',
        ]);

        \App\Models\OrderNote::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'note' => $data['note'],
            'is_customer_visible' => $request->boolean('is_customer_visible'),
            'is_private' => $request->boolean('is_private', true),
        ]);

        if ($request->boolean('is_customer_visible')) {
            $email = $order->guest_email ?? $order->user?->email;
            if ($email) {
                \App\Jobs\SendOrderEmailJob::dispatch('order_status_changed', $email, [
                    'order_number' => $order->order_number,
                    'status' => 'ملاحظة جديدة',
                    'note' => $data['note'],
                ]);
            }
        }

        return back()->with('success', 'تمت إضافة الملاحظة.');
    }

    public function refund(Request $request, Order $order)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'restock' => 'boolean',
            'reason' => 'nullable|string|max:500',
        ]);

        $result = app(\App\Services\OrderRefundService::class)->refund(
            $order,
            (float) $data['amount'],
            $request->boolean('restock'),
            $data['reason'] ?? null,
        );

        if (! $result['success']) {
            return back()->withErrors(['refund' => $result['message'] ?? 'فشل الاسترداد']);
        }

        return back()->with('success', 'تم تنفيذ الاسترداد بنجاح.');
    }
}
