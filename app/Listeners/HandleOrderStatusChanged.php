<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Jobs\SendOrderEmailJob;
use App\Models\Coupon;
use App\Models\Order;
use App\Services\LoyaltyService;

class HandleOrderStatusChanged
{
    public function __construct(
        protected LoyaltyService $loyaltyService,
    ) {}

    public function handle(OrderStatusChanged $event): void
    {
        $order = $event->order->load(['status', 'user']);
        $this->loyaltyService->awardPointsForOrder($order);
        $this->awardReferralBonus($order);

        $email = $order->guest_email ?? $order->user?->email;
        if (! $email || ! $order->status) {
            return;
        }

        SendOrderEmailJob::dispatch('order_status_changed', $email, [
            'order' => $order,
            'order_number' => $order->order_number,
            'status' => $order->status->name,
            'status_slug' => $order->status->slug,
            'tracking_number' => $order->tracking_number,
            'tracking_carrier' => $order->tracking_carrier,
        ]);

        if ($order->status->slug === 'completed' && $order->user) {
            SendOrderEmailJob::dispatch('review_request', $email, [
                'order' => $order,
                'order_number' => $order->order_number,
                'customer_name' => $order->user->name,
                'order_url' => route('frontend.account.orders.show', $order),
            ]);
        }
    }

    /**
     * Reward the referrer with loyalty points the first time the referred
     * customer's own order reaches the "completed" status.
     */
    protected function awardReferralBonus(Order $order): void
    {
        if (! config('store.referral_program_enabled', true)) {
            return;
        }

        if (! $order->coupon_code || $order->status?->slug !== 'completed' || ! $order->user_id) {
            return;
        }

        $coupon = Coupon::where('code', $order->coupon_code)->whereNotNull('referrer_user_id')->first();
        if (! $coupon || $coupon->referrer_user_id === $order->user_id) {
            return;
        }

        $isFirstCompletedOrder = Order::where('user_id', $order->user_id)
            ->whereHas('status', fn ($q) => $q->where('slug', 'completed'))
            ->count() === 1;

        if (! $isFirstCompletedOrder) {
            return;
        }

        $referrer = $coupon->referrer;
        if (! $referrer) {
            return;
        }

        $this->loyaltyService->adjustPoints(
            $referrer,
            (int) config('store.referral_bonus_points', 100),
            'مكافأة إحالة عميل جديد عبر الطلب #' . $order->order_number,
        );
    }
}
