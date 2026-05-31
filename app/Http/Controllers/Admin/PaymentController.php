<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\OrderRefundService;
use App\Services\Payments\StripeCredentialResolver;
use App\Services\Payments\StripePaymentSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        protected StripeCredentialResolver $stripeCredentials,
        protected StripePaymentSyncService $stripeSync,
        protected OrderRefundService $orderRefundService,
    ) {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $query = Payment::query()
            ->with(['order', 'paymentMethod'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('payment_method_id')) {
            $query->where('payment_method_id', $request->integer('payment_method_id'));
        }
        if ($request->filled('order_number')) {
            $query->whereHas('order', fn ($q) => $q->where('order_number', 'like', '%' . $request->input('order_number') . '%'));
        }
        if ($request->filled('transaction_id')) {
            $term = '%' . $request->input('transaction_id') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('transaction_id', 'like', $term)
                    ->orWhere('metadata->stripe_session_id', 'like', $term)
                    ->orWhere('metadata->stripe_payment_intent_id', 'like', $term);
            });
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        $payments = $query->paginate(20)->withQueryString();
        $paymentMethods = PaymentMethod::orderBy('order')->get();

        $statusLabels = $this->statusLabels();

        return view('admin.pages.payments.index', compact(
            'payments',
            'paymentMethods',
            'statusLabels',
        ));
    }

    public function show(Payment $payment): View
    {
        $payment->load([
            'order.user',
            'order.status',
            'paymentMethod',
            'events' => fn ($q) => $q->orderByDesc('created_at'),
        ]);

        $statusLabels = $this->statusLabels();
        $stripeSessionId = $payment->metadata['stripe_session_id'] ?? $payment->transaction_id;
        $stripePaymentIntentId = $this->stripeSync->paymentIntentId($payment);

        $stripeLinks = [
            'session' => $payment->isStripe()
                ? $this->stripeCredentials->dashboardCheckoutSessionUrl($stripeSessionId, $payment->paymentMethod)
                : null,
            'payment' => $payment->isStripe()
                ? $this->stripeCredentials->dashboardPaymentUrl($stripePaymentIntentId, $payment->paymentMethod)
                : null,
        ];

        $refundableAmount = $payment->order
            ? $this->orderRefundService->refundableAmount($payment->order)
            : 0;

        return view('admin.pages.payments.show', compact(
            'payment',
            'statusLabels',
            'stripeSessionId',
            'stripePaymentIntentId',
            'stripeLinks',
            'refundableAmount',
        ));
    }

    public function refund(Request $request, Payment $payment): RedirectResponse
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['nullable', 'string', 'max:500'],
            'restock' => ['sometimes', 'boolean'],
        ]);

        if (! $payment->order) {
            return back()->withErrors(['refund' => 'الطلب غير مرتبط بهذه العملية.']);
        }

        if ($payment->status !== 'completed') {
            return back()->withErrors(['refund' => 'لا يمكن استرداد دفعة غير مكتملة.']);
        }

        $result = $this->orderRefundService->refund(
            $payment->order,
            (float) $request->input('amount'),
            $request->boolean('restock'),
            $request->input('reason'),
        );

        if (! ($result['success'] ?? false)) {
            return back()->withErrors(['refund' => $result['message'] ?? 'فشل الاسترداد']);
        }

        return redirect()
            ->route('admin.payments.show', $payment)
            ->with('success', $result['message'] ?? 'تم تنفيذ الاسترداد.');
    }

    protected function statusLabels(): array
    {
        return [
            'pending' => ['label' => 'قيد الانتظار', 'class' => 'admin-table__badge--warning'],
            'completed' => ['label' => 'مكتمل', 'class' => 'admin-table__badge--success'],
            'failed' => ['label' => 'فشل', 'class' => 'admin-table__badge--danger'],
            'cancelled' => ['label' => 'ملغى', 'class' => 'admin-table__badge--secondary'],
            'refunded' => ['label' => 'مسترد', 'class' => 'admin-table__badge--secondary'],
        ];
    }
}
