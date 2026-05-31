<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Analytics\PageViewTracker;
use Illuminate\Http\Request;

class AnalyticsBeaconController extends Controller
{
    public function __construct(protected PageViewTracker $tracker) {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_name' => 'required|string|max:64',
            'payload' => 'nullable|array',
            'url' => 'nullable|string|max:500',
        ]);

        $this->tracker->recordEvent(
            $validated['event_name'],
            $validated['payload'] ?? [],
            $request
        );

        return response()->json(['ok' => true]);
    }
}
