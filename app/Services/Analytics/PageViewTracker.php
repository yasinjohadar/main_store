<?php

namespace App\Services\Analytics;

use App\Models\AnalyticsEvent;
use App\Models\PageView;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PageViewTracker
{
    public function track(Request $request, ?Model $viewable = null): void
    {
        PageView::create([
            'viewable_type' => $viewable ? $viewable->getMorphClass() : null,
            'viewable_id' => $viewable?->getKey(),
            'url' => $request->fullUrl(),
            'referrer' => $request->headers->get('referer'),
            'utm_source' => $request->query('utm_source'),
            'utm_medium' => $request->query('utm_medium'),
            'utm_campaign' => $request->query('utm_campaign'),
            'session_id' => $request->session()->getId(),
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);
    }

    public function recordEvent(string $eventName, array $payload, Request $request): void
    {
        AnalyticsEvent::create([
            'event_name' => $eventName,
            'payload' => $payload,
            'url' => $request->input('url', $request->fullUrl()),
            'session_id' => $request->session()->getId(),
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);
    }
}
