<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\Request;

class TrackOrderController extends Controller
{
    public function index(Request $request)
    {
        $order = null;
        $statusTimeline = OrderStatus::orderBy('order')->get();
        $paymentMethodLabel = null;

        if ($request->isMethod('post') || $request->filled('order_number')) {
            $validated = $request->validate([
                'order_number' => 'required|string|max:50',
                'email' => 'required|email|max:255',
            ], [
                'order_number.required' => 'يرجى إدخال رقم الطلب',
                'email.required' => 'يرجى إدخال البريد الإلكتروني',
                'email.email' => 'يرجى إدخال بريد إلكتروني صحيح',
            ]);

            $order = Order::query()
                ->where('order_number', $validated['order_number'])
                ->whereHas('addresses', fn ($query) => $query->where('email', $validated['email']))
                ->with([
                    'status',
                    'items.product',
                    'addresses',
                    'payments.paymentMethod',
                    'statusHistory.newStatus',
                    'shippingMethod',
                ])
                ->first();

            if (! $order) {
                return back()
                    ->withInput()
                    ->withErrors(['track' => 'لم يتم العثور على طلب مطابق. تحقق من رقم الطلب والبريد الإلكتروني.']);
            }

            $paymentMethodLabel = $this->resolvePaymentMethodLabel($order);
        }

        return view('frontend.pages.track-order.index', compact(
            'order',
            'statusTimeline',
            'paymentMethodLabel'
        ));
    }

    protected function resolvePaymentMethodLabel(Order $order): ?string
    {
        $payment = $order->payments->first();

        if ($payment?->paymentMethod?->name) {
            return $payment->paymentMethod->name;
        }

        $method = data_get($payment?->metadata, 'method')
            ?? data_get($payment?->metadata, 'payment_method');

        return match ($method) {
            'bank_transfer' => 'تحويل بنكي',
            'cod' => 'الدفع عند الاستلام',
            'credit_card' => 'بطاقة ائتمان',
            default => null,
        };
    }
}
