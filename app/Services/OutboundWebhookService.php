<?php

namespace App\Services;

use App\Jobs\ProcessOutboundWebhookJob;
use App\Models\Webhook;

class OutboundWebhookService
{
    public function dispatch(string $event, array $payload): void
    {
        Webhook::active()
            ->get()
            ->filter(fn (Webhook $webhook) => $webhook->listensTo($event))
            ->each(fn (Webhook $webhook) => ProcessOutboundWebhookJob::dispatch(
                $webhook->id,
                $event,
                $payload,
            ));
    }
}
