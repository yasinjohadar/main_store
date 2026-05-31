<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\StockNotification;
use App\Services\EmailSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyBackInStockJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $productId,
    ) {}

    public function handle(EmailSender $emailSender): void
    {
        $product = Product::find($this->productId);
        if (! $product || ! $product->in_stock) {
            return;
        }

        $notifications = StockNotification::query()
            ->where('product_id', $product->id)
            ->where('notified', false)
            ->get();

        foreach ($notifications as $notification) {
            $email = $notification->email ?? $notification->user?->email;
            if (! $email) {
                continue;
            }

            $emailSender->send('back_in_stock', $email, [
                'product' => $product,
                'product_name' => $product->name,
                'product_url' => route('frontend.product.show', $product->slug),
            ]);

            $notification->update(['notified' => true]);
        }
    }
}
