<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\StockNotification;
use App\Services\EmailSender;
use App\Services\SiteSettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LowStockAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(EmailSender $emailSender, SiteSettingsService $settings): void
    {
        $threshold = (int) config('store.low_stock_threshold', 5);
        $adminEmail = $settings->get(
            SiteSettingsService::KEY_SITE_CONTACT_EMAIL,
            config('mail.from.address')
        );

        if (! $adminEmail || $threshold <= 0) {
            return;
        }

        $lowStock = Product::query()
            ->where('track_quantity', true)
            ->where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<=', $threshold)
            ->where('status', 'active')
            ->orderBy('stock_quantity')
            ->limit(50)
            ->get(['id', 'name', 'sku', 'stock_quantity']);

        if ($lowStock->isEmpty()) {
            return;
        }

        $lines = $lowStock->map(fn ($p) => "{$p->name} (SKU: {$p->sku}) — {$p->stock_quantity} متبقي")->implode('<br>');

        $emailSender->send('low_stock_alert', $adminEmail, [
            'products_count' => $lowStock->count(),
            'products_list' => $lines,
        ]);
    }
}
