<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CartPresenter;
use App\Services\CartService;
use App\Services\OrderTotalsService;
use Illuminate\Http\Request;

class CartApiController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected CartPresenter $presenter,
        protected OrderTotalsService $totalsService,
    ) {}

    public function show()
    {
        $cart = $this->cartService->getCart();

        return response()->json([
            'cart' => $this->presenter->toViewData($cart),
            'totals' => $this->totalsService->calculate($cart)->toArray(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'integer|min:1',
        ]);

        $product = \App\Models\Product::active()->findOrFail($request->product_id);
        $variantId = $request->product_variant_id ? (int) $request->product_variant_id : null;

        if ($product->variants()->exists()) {
            if (! $variantId) {
                return response()->json(['success' => false, 'message' => 'يرجى اختيار جميع خيارات المنتج'], 422);
            }
            \App\Models\ProductVariant::where('id', $variantId)->where('product_id', $product->id)->firstOrFail();
        }

        $this->cartService->add(
            (int) $request->product_id,
            (int) ($request->quantity ?? 1),
            $request->product_variant_id ? (int) $request->product_variant_id : null,
        );

        return $this->show();
    }

    public function update(Request $request, int $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $this->cartService->update($id, (int) $request->quantity);

        return $this->show();
    }

    public function destroy(int $id)
    {
        $this->cartService->remove($id);

        return $this->show();
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['coupon' => 'required|string']);
        $result = $this->cartService->applyCoupon($request->coupon);

        return response()->json(array_merge($result, ['cart' => $this->show()->getData(true)]));
    }
}
