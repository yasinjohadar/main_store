<?php

namespace App\Services;

use App\DTOs\CartTotalsDTO;
use App\Models\ShoppingCart;
use App\Models\User;

class OrderTotalsService
{
    public function __construct(
        protected CouponService $couponService,
        protected ShippingService $shippingService,
        protected TaxService $taxService,
        protected LoyaltyService $loyaltyService,
    ) {}

    public function calculate(
        ShoppingCart $cart,
        array $shippingAddress = [],
        ?int $shippingMethodId = null,
        ?string $couponCode = null,
        ?User $user = null,
        int $pointsToRedeem = 0,
    ): CartTotalsDTO {
        $cart->loadMissing('items.product', 'items.variant');

        $subtotal = (float) $cart->subtotal;
        $discount = 0.0;
        $appliedCoupon = $couponCode ?? $cart->coupon_code;

        if ($appliedCoupon) {
            $result = $this->couponService->calculateDiscount($appliedCoupon, $cart);
            if ($result['success']) {
                $discount = (float) $result['discount_amount'];
                $appliedCoupon = $result['coupon_code'];
            } else {
                $appliedCoupon = null;
            }
        }

        $shippingResult = $this->shippingService->calculateForCart($cart, $shippingAddress);
        $methods = $shippingResult['methods'] ?? [];
        $selected = $shippingResult['selected_method'];

        if ($shippingMethodId && $methods) {
            foreach ($methods as $method) {
                if ((int) $method['id'] === $shippingMethodId) {
                    $selected = $method;
                    break;
                }
            }
        }

        $shipping = (float) ($selected['cost'] ?? 0);

        $taxResult = $this->taxService->calculateForCart($cart, $shippingAddress);
        $tax = (float) ($taxResult['tax_amount'] ?? 0);

        $pointsDiscount = 0.0;
        if ($user && $pointsToRedeem > 0 && config('store.loyalty_redemption_enabled', true)) {
            $redeem = $this->loyaltyService->redeemPoints($user, $pointsToRedeem, $subtotal);
            if ($redeem['success']) {
                $pointsDiscount = (float) $redeem['discount_amount'];
            }
        }

        $total = max(0, $subtotal + $shipping + $tax - $discount - $pointsDiscount);

        return new CartTotalsDTO(
            subtotal: $subtotal,
            discount: $discount,
            shipping: $shipping,
            tax: $tax,
            pointsDiscount: $pointsDiscount,
            total: $total,
            couponCode: $appliedCoupon,
            shippingMethods: $methods,
            selectedShippingMethod: $selected,
            taxItems: $taxResult['items'] ?? [],
            itemsCount: (int) $cart->items_count,
        );
    }
}
