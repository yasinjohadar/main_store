<?php

namespace App\Services\Seo;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IndexNowService
{
    public function ping(string $url): void
    {
        $key = config('services.indexnow.key');
        if (! $key || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return;
        }

        try {
            Http::timeout(5)->post('https://api.indexnow.org/indexnow', [
                'host' => parse_url(config('app.url'), PHP_URL_HOST),
                'key' => $key,
                'urlList' => [$url],
            ]);
        } catch (\Throwable $e) {
            Log::debug('IndexNow ping failed', ['url' => $url, 'error' => $e->getMessage()]);
        }
    }
}
