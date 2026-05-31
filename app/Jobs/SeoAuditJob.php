<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SeoAuditJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $issues = [];

        Product::query()->chunk(100, function ($products) use (&$issues) {
            foreach ($products as $product) {
                if (empty($product->meta_description)) {
                    $issues[] = ['type' => 'product', 'id' => $product->id, 'issue' => 'missing_meta_description'];
                }
                if (empty($product->meta_title)) {
                    $issues[] = ['type' => 'product', 'id' => $product->id, 'issue' => 'missing_meta_title'];
                }
            }
        });

        Cache::put('seo_audit_latest', [
            'ran_at' => now()->toIso8601String(),
            'issues_count' => count($issues),
            'issues' => array_slice($issues, 0, 100),
        ], now()->addDay());

        Log::info('SEO audit completed', ['issues' => count($issues)]);
    }
}
