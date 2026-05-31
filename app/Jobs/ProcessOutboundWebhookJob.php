<?php

namespace App\Jobs;

use App\Models\Webhook;
use App\Models\WebhookDelivery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ProcessOutboundWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $webhookId,
        public string $event,
        public array $payload,
    ) {}

    public function handle(): void
    {
        $webhook = Webhook::active()->find($this->webhookId);
        if (! $webhook || ! $webhook->listensTo($this->event)) {
            return;
        }

        $delivery = WebhookDelivery::create([
            'webhook_id' => $webhook->id,
            'event' => $this->event,
            'payload' => $this->payload,
            'attempts' => $this->attempts(),
        ]);

        $body = json_encode($this->payload);
        $signature = hash_hmac('sha256', $body, $webhook->secret);

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'X-Webhook-Event' => $this->event,
                    'X-Webhook-Signature' => $signature,
                    'Content-Type' => 'application/json',
                ])
                ->withBody($body, 'application/json')
                ->post($webhook->url);

            $delivery->update([
                'response_status' => $response->status(),
                'response_body' => Str::limit($response->body(), 2000),
                'delivered_at' => $response->successful() ? now() : null,
            ]);

            if (! $response->successful()) {
                $this->fail(new \RuntimeException('Webhook delivery failed: HTTP ' . $response->status()));
            }
        } catch (\Throwable $e) {
            $delivery->update([
                'response_body' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
