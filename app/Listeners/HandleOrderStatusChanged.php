<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Jobs\SendOrderEmailJob;
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

        $email = $order->guest_email ?? $order->user?->email;
        if (! $email || ! $order->status) {
            return;
        }

        SendOrderEmailJob::dispatch('order_status_changed', $email, [
            'order' => $order,
            'order_number' => $order->order_number,
            'status' => $order->status->name,
            'status_slug' => $order->status->slug,
        ]);
    }
}
