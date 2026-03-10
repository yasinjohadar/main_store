<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\ShoppingCart;

class CouponService
{
    /**
     * Validate coupon and calculate discount amount for the given cart.
     * Uses coupon's applicable_to (entire store / specific products / specific categories).
     *
     * @return array{success: bool, message?: string, discount_amount?: float, coupon_code?: string}
     */
    public function calculateDiscount(string $code, ShoppingCart $cart): array
    {
        $code = trim($code);
        if ($code === '') {
            return ['success' => false, 'message' => 'يرجى إدخال كود الكوبون.'];
        }

        $coupon = Coupon::where('code', $code)->first();
        if (!$coupon) {
            return ['success' => false, 'message' => 'كود الكوبون غير صحيح.'];
        }

        if ($coupon->status !== 'active') {
            return ['success' => false, 'message' => 'هذا الكوبون غير نشط.'];
        }

        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            return ['success' => false, 'message' => 'هذا الكوبون لم يبدأ بعد.'];
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return ['success' => false, 'message' => 'هذا الكوبون منتهي الصلاحية.'];
        }

        if ($coupon->usage_limit !== null && $coupon->usage_limit > 0) {
            $used = $coupon->usages()->count();
            if ($used >= $coupon->usage_limit) {
                return ['success' => false, 'message' => 'تم استنفاد عدد استخدامات هذا الكوبون.'];
            }
        }

        $applicableSubtotal = $coupon->getApplicableSubtotal($cart);

        if ($applicableSubtotal <= 0) {
            return [
                'success' => false,
                'message' => 'هذا الكوبون لا ينطبق على أي منتج في سلتك الحالية.',
            ];
        }

        $minAmount = $coupon->minimum_order_amount ? (float) $coupon->minimum_order_amount : 0;
        if ($minAmount > 0 && $applicableSubtotal < $minAmount) {
            return [
                'success' => false,
                'message' => 'الحد الأدنى للطلب لهذا الكوبون هو ' . number_format($minAmount, 2) . ' ر.س.',
            ];
        }

        $discountAmount = $this->computeDiscountAmount($coupon, $applicableSubtotal);

        return [
            'success' => true,
            'discount_amount' => round($discountAmount, 2),
            'coupon_code' => $coupon->code,
        ];
    }

    /**
     * Compute discount amount from coupon type and value.
     */
    protected function computeDiscountAmount(Coupon $coupon, float $applicableSubtotal): float
    {
        $value = (float) $coupon->value;

        if ($coupon->type === 'percentage') {
            return $applicableSubtotal * ($value / 100);
        }

        if ($coupon->type === 'fixed_amount') {
            return min($value, $applicableSubtotal);
        }

        if ($coupon->type === 'buy_x_get_y') {
            return min($value, $applicableSubtotal);
        }

        return 0.0;
    }
}
