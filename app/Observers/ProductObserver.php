<?php

namespace App\Observers;

use App\Jobs\NotifyBackInStockJob;
use App\Models\Product;
use App\Services\OutboundWebhookService;

class ProductObserver
{
    public function __construct(
        protected OutboundWebhookService $webhooks,
    ) {}

    public function updated(Product $product): void
    {
        if ($product->wasChanged(['stock_quantity', 'status', 'price', 'name'])) {
            $this->webhooks->dispatch('product.updated', [
                'id' => $product->id,
                'slug' => $product->slug,
                'name' => $product->name,
                'price' => (float) $product->price,
                'stock_quantity' => $product->stock_quantity,
                'status' => $product->status,
            ]);
        }

        if ($product->wasChanged('stock_quantity') && $product->stock_quantity > 0 && $product->track_quantity) {
            NotifyBackInStockJob::dispatch($product->id);
        }
    }
}
