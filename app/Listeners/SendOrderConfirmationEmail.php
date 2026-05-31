<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Jobs\SendOrderEmailJob;

class SendOrderConfirmationEmail
{
    public function __construct() {}

    public function handle(OrderPlaced $event): void
    {
        $order = $event->order->load(['items', 'status', 'addresses', 'user']);
        $email = $order->guest_email ?? $order->user?->email;
        if (! $email) {
            return;
        }

        SendOrderEmailJob::dispatch('order_confirmation', $email, [
            'order' => $order,
            'order_number' => $order->order_number,
            'total' => number_format((float) $order->total, 2),
            'customer_name' => $order->addresses->firstWhere('type', 'shipping')?->first_name ?? 'عميل',
        ]);
    }
}
