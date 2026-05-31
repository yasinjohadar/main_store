<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Events\OrderStatusChanged;
use App\Services\OutboundWebhookService;

class DispatchOrderWebhooks
{
    public function __construct(
        protected OutboundWebhookService $webhooks,
    ) {}

    public function handleOrderPlaced(OrderPlaced $event): void
    {
        $order = $event->order;
        $this->webhooks->dispatch('order.created', $this->orderPayload($order));
    }

    public function handleOrderStatusChanged(OrderStatusChanged $event): void
    {
        $order = $event->order;
        $this->webhooks->dispatch('order.updated', array_merge(
            $this->orderPayload($order),
            ['previous_status' => $event->previousStatusSlug],
        ));
    }

    protected function orderPayload($order): array
    {
        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status?->slug,
            'total' => (float) $order->total,
            'currency' => $order->currency,
            'user_id' => $order->user_id,
            'guest_email' => $order->guest_email,
            'created_at' => $order->created_at?->toIso8601String(),
        ];
    }
}
