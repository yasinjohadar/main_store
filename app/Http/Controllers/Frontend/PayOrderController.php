<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Services\CheckoutService;
use App\Services\Payments\PaymentGatewayManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayOrderController extends Controller
{
    public function __construct(
        protected PaymentGatewayManager $paymentGatewayManager,
        protected CheckoutService $checkoutService,
    ) {}

    public function show(Order $order)
    {
        $this->authorizeOrder($order);

        if (! in_array($order->status?->slug, ['pending'], true)) {
            return redirect()->route('frontend.account.orders.show', $order)
                ->withErrors(['error' => 'لا يمكن الدفع لهذا الطلب.']);
        }

        $order->load(['items', 'status', 'payments.paymentMethod']);
        $paymentMethods = PaymentMethod::active()->orderBy('order')->get();

        return view('frontend.pages.checkout.pay-order', compact('order', 'paymentMethods'));
    }

    public function store(Request $request, Order $order)
    {
        $this->authorizeOrder($order);

        if (! in_array($order->status?->slug, ['pending'], true)) {
            return back()->withErrors(['error' => 'لا يمكن الدفع لهذا الطلب.']);
        }

        $validated = $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        $paymentMethod = PaymentMethod::active()->findOrFail($validated['payment_method_id']);
        $payment = $order->payments()->latest()->first();

        if (! $payment) {
            $payment = $order->payments()->create([
                'payment_method_id' => $paymentMethod->id,
                'amount' => $order->total,
                'currency' => $order->currency,
                'status' => 'pending',
            ]);
        } else {
            $payment->update(['payment_method_id' => $paymentMethod->id]);
        }

        $result = $this->paymentGatewayManager->initiate($paymentMethod, $order, $payment);

        if (! empty($result['redirect_url'])) {
            return redirect()->away($result['redirect_url']);
        }

        return redirect()->route('frontend.checkout.success', $order)
            ->with('success', 'تم بدء عملية الدفع.');
    }

    protected function authorizeOrder(Order $order): void
    {
        if ($order->user_id) {
            if (Auth::id() !== $order->user_id) {
                abort(403);
            }

            return;
        }

        if ($order->guest_email && Auth::check() && Auth::user()->email === $order->guest_email) {
            return;
        }

        abort(403);
    }
}
