<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Services\CartPresenter;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\OrderTotalsService;
use App\Services\Payments\Gateways\PayPalGateway;
use App\Services\Payments\StripePaymentSyncService;
use App\Services\SiteSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected CartPresenter $cartPresenter,
        protected OrderTotalsService $orderTotalsService,
        protected CheckoutService $checkoutService,
    ) {}

    public function index(Request $request)
    {
        $cart = $this->cartService->getCart();
        if ($cart->items->isEmpty()) {
            return $this->redirectIfEmptyCart();
        }

        $viewData = $this->cartPresenter->toViewData($cart);
        $shippingAddress = $this->defaultShippingAddress($request);
        $totals = $this->orderTotalsService->calculate(
            $cart,
            $shippingAddress,
            $request->integer('shipping_method_id') ?: null,
        );

        $user = Auth::user();
        $paymentMethods = $this->availablePaymentMethods();
        $loyaltyBalance = $user?->loyalty_points_balance ?? 0;
        $loyaltyEnabled = config('store.loyalty_redemption_enabled', true) && $user;

        return view('frontend.pages.checkout.index', array_merge($viewData, [
            'totals' => $totals,
            'paymentMethods' => $paymentMethods,
            'shippingAddress' => $shippingAddress,
            'loyaltyBalance' => $loyaltyBalance,
            'loyaltyEnabled' => $loyaltyEnabled,
        ]));
    }

    public function store(Request $request)
    {
        $cart = $this->cartService->getCart();
        if ($cart->items->isEmpty()) {
            return $this->redirectIfEmptyCart();
        }

        $paymentMethods = $this->availablePaymentMethods();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'address' => 'required|string|max:500',
            'shipping_country' => 'nullable|string|size:2',
            'shipping_method_id' => 'nullable|exists:shipping_methods,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'notes' => 'nullable|string|max:1000',
            'points_to_redeem' => 'nullable|integer|min:0',
        ]);

        if ($paymentMethods->isEmpty()) {
            return back()->withInput()->withErrors(['payment_method_id' => 'لا توجد طرق دفع مفعّلة. يرجى التواصل مع الإدارة.']);
        }

        $validated['shipping_country'] = $validated['shipping_country'] ?? config('store.default_country', 'SA');
        $validated['shipping_city'] = $validated['city'];
        $validated['shipping_postal_code'] = $validated['zip_code'] ?? null;
        $validated['shipping_address_line_1'] = $validated['address'];
        $validated['customer_note'] = $validated['notes'] ?? null;

        $user = Auth::user();
        $result = $this->checkoutService->placeOrder($validated, $user);

        if (! $result['success']) {
            return back()->withInput()->withErrors(['error' => $result['message'] ?? 'فشل إنشاء الطلب']);
        }

        $order = $result['order'];
        session()->put('checkout_last_order_id', $order->id);

        if (! empty($result['redirect_url'])) {
            return redirect()->away($result['redirect_url']);
        }

        return redirect()->route('frontend.checkout.success', $order)
            ->with('success', 'تم تأكيد طلبك بنجاح!');
    }

    public function success(Request $request, Order $order, StripePaymentSyncService $stripeSync): RedirectResponse|\Illuminate\View\View
    {
        $this->authorizeOrderConfirmation($order);

        if ($request->filled('session_id')) {
            $stripeSync->syncFromCheckoutSession($request->query('session_id'), $order);
            $order->refresh();
        }

        $order->load([
            'items.product.images',
            'items.downloads.file',
            'status',
            'addresses',
            'payments.paymentMethod',
            'shippingMethod',
            'user',
        ]);

        return view('frontend.pages.checkout.success', compact('order'));
    }

    public function invoice(Order $order)
    {
        $this->authorizeOrderConfirmation($order);

        $order->load([
            'items.product',
            'status',
            'addresses',
            'payments.paymentMethod',
            'shippingMethod',
            'user',
        ]);

        $store = $this->invoiceStoreProfile();

        return view('frontend.pages.checkout.invoice', compact('order', 'store'));
    }

    public function paypalReturn(Request $request, Order $order)
    {
        $payment = $order->payments()->latest()->first();
        if (! $payment) {
            return redirect()->route('frontend.checkout.index')->withErrors(['error' => 'الدفع غير موجود']);
        }

        $paypalOrderId = $request->query('token') ?? $payment->metadata['paypal_order_id'] ?? null;
        if ($paypalOrderId) {
            app(PayPalGateway::class)->captureOrder($payment, $paypalOrderId);
        }

        session()->put('checkout_last_order_id', $order->id);

        return redirect()->route('frontend.checkout.success', $order);
    }

    protected function authorizeOrderConfirmation(Order $order): void
    {
        if (! $this->canViewOrderConfirmation($order)) {
            abort(403);
        }
    }

    protected function canViewOrderConfirmation(Order $order): bool
    {
        if ($order->user_id) {
            return Auth::id() === $order->user_id;
        }

        return session('checkout_last_order_id') === $order->id
            || (Auth::check() && $order->guest_email && Auth::user()->email === $order->guest_email);
    }

    protected function availablePaymentMethods()
    {
        $methods = PaymentMethod::active()->orderBy('order')->get();

        if ($methods->isEmpty()) {
            PaymentMethod::query()->firstOrCreate(
                ['slug' => 'cod'],
                [
                    'name' => 'الدفع عند الاستلام',
                    'driver' => 'cod',
                    'is_active' => true,
                    'order' => 0,
                ]
            );

            $methods = PaymentMethod::active()->orderBy('order')->get();
        }

        return $methods;
    }

    protected function invoiceStoreProfile(): array
    {
        return [
            'name' => site_setting(SiteSettingsService::KEY_SITE_NAME, config('app.name')),
            'logo' => site_setting_url(SiteSettingsService::KEY_SITE_LOGO),
            'email' => site_setting(SiteSettingsService::KEY_SITE_CONTACT_EMAIL),
            'phone' => site_setting(SiteSettingsService::KEY_SITE_CONTACT_PHONE),
            'address' => site_setting(SiteSettingsService::KEY_SITE_ADDRESS),
            'whatsapp' => site_setting(SiteSettingsService::KEY_SITE_WHATSAPP_NUMBER),
        ];
    }

    protected function redirectIfEmptyCart()
    {
        $orderId = session('checkout_last_order_id');
        if ($orderId) {
            $order = Order::find($orderId);
            if ($order && $this->canViewOrderConfirmation($order)) {
                return redirect()->route('frontend.checkout.success', $order)
                    ->with('success', 'تم تأكيد طلبك بنجاح!');
            }
        }

        return redirect()->route('frontend.cart.index')->withErrors(['cart' => 'السلة فارغة']);
    }

    protected function defaultShippingAddress(Request $request): array
    {
        $user = Auth::user();
        $address = $user?->addresses()->where('type', 'shipping')->orderByDesc('is_default')->first();

        return [
            'country' => old('shipping_country', $address?->country ?? config('store.default_country', 'SA')),
            'state' => old('shipping_state', $address?->state),
            'city' => old('city', $address?->city ?? 'الرياض'),
            'postal_code' => old('zip_code', $address?->postal_code),
        ];
    }
}
