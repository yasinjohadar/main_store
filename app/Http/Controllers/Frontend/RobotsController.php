<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\SiteSettingsService;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function index(SiteSettingsService $settings): Response
    {
        $allowIndex = config('store.robots_allow_index', true)
            && ! $settings->get(SiteSettingsService::KEY_SITE_MAINTENANCE_MODE, false);
        $sitemap = url('/sitemap.xml');

        $lines = [
            'User-agent: *',
            $allowIndex ? 'Allow: /' : 'Disallow: /',
            '',
            "Sitemap: {$sitemap}",
        ];

        return response(implode("\n", $lines), 200)->header('Content-Type', 'text/plain');
    }
}
