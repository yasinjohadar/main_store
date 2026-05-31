<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShoppingCart;
use App\Models\ShoppingCartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function __construct(
        protected CouponService $couponService
    ) {}
    public function getCart(): ShoppingCart
    {
        if (Auth::check()) {
            $cart = ShoppingCart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['user_id' => Auth::id()]
            );
        } else {
            $sessionId = Session::getId();
            $cart = ShoppingCart::firstOrCreate(
                ['session_id' => $sessionId],
                ['session_id' => $sessionId]
            );
        }
        return $cart->load('items.product', 'items.variant');
    }

    public function add(int $productId, int $quantity = 1, ?int $variantId = null): ShoppingCart
    {
        $cart = $this->getCart();
        $product = Product::active()->findOrFail($productId);

        if ($variantId !== null) {
            $variant = ProductVariant::where('id', $variantId)->where('product_id', $productId)->firstOrFail();
        }

        $item = $cart->items()->where('product_id', $productId)->where('product_variant_id', $variantId)->first();
        if ($item) {
            $item->increment('quantity', $quantity);
        } else {
            $cart->items()->create([
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'quantity' => $quantity,
            ]);
        }
        return $this->getCart();
    }

    public function update(int $cartItemId, int $quantity): void
    {
        $cart = $this->getCart();
        $item = $cart->items()->findOrFail($cartItemId);
        if ($quantity <= 0) {
            $item->delete();
            return;
        }
        $item->update(['quantity' => $quantity]);
    }

    public function remove(int $cartItemId): void
    {
        $cart = $this->getCart();
        $cart->items()->where('id', $cartItemId)->delete();
    }

    public function clear(): void
    {
        $cart = $this->getCart();
        $cart->items()->delete();
        $cart->update(['coupon_code' => null, 'discount_amount' => 0]);
    }

    public function mergeGuestCartIntoUser(int $userId, string $guestSessionId): void
    {
        $guestCart = ShoppingCart::query()
            ->where('session_id', $guestSessionId)
            ->whereNull('user_id')
            ->with('items')
            ->first();

        if (! $guestCart || $guestCart->items->isEmpty()) {
            return;
        }

        $userCart = ShoppingCart::firstOrCreate(
            ['user_id' => $userId],
            ['user_id' => $userId]
        );

        foreach ($guestCart->items as $item) {
            $existing = $userCart->items()
                ->where('product_id', $item->product_id)
                ->where('product_variant_id', $item->product_variant_id)
                ->first();

            if ($existing) {
                $existing->increment('quantity', $item->quantity);
            } else {
                $userCart->items()->create([
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                ]);
            }
        }

        if (! $userCart->coupon_code && $guestCart->coupon_code) {
            $userCart->update([
                'coupon_code' => $guestCart->coupon_code,
                'discount_amount' => $guestCart->discount_amount,
            ]);
        }

        $guestCart->items()->delete();
        $guestCart->delete();
    }

    /**
     * Apply a coupon to the cart. Returns ['success' => true] or ['success' => false, 'message' => '...'].
     */
    public function applyCoupon(string $code): array
    {
        $cart = $this->getCart();
        $result = $this->couponService->calculateDiscount($code, $cart);
        if (!$result['success']) {
            return $result;
        }
        $cart->update([
            'coupon_code' => $result['coupon_code'],
            'discount_amount' => $result['discount_amount'],
        ]);
        return $result;
    }

    /**
     * Remove applied coupon from the cart.
     */
    public function removeCoupon(): void
    {
        $cart = $this->getCart();
        $cart->update(['coupon_code' => null, 'discount_amount' => 0]);
    }
}
