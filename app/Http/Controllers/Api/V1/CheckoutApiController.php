<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\OrderTotalsService;
use Illuminate\Http\Request;

class CheckoutApiController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected OrderTotalsService $totalsService,
        protected CheckoutService $checkoutService,
    ) {}

    public function totals(Request $request)
    {
        $cart = $this->cartService->getCart();
        $address = [
            'country' => $request->input('country', config('store.default_country', 'SA')),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'postal_code' => $request->input('postal_code'),
        ];

        return response()->json(
            $this->totalsService->calculate(
                $cart,
                $address,
                $request->integer('shipping_method_id') ?: null,
            )->toArray()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'shipping_method_id' => 'nullable|exists:shipping_methods,id',
        ]);

        $validated['shipping_country'] = $request->input('country', config('store.default_country', 'SA'));
        $validated['shipping_city'] = $validated['city'];
        $validated['shipping_address_line_1'] = $validated['address'];

        $result = $this->checkoutService->placeOrder($validated, $request->user());

        if (! $result['success']) {
            return response()->json(['message' => $result['message']], 422);
        }

        return response()->json(['order' => $result['order'], 'redirect_url' => $result['redirect_url'] ?? null], 201);
    }
}
