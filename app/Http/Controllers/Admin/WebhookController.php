<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Webhook;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebhookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $webhooks = Webhook::withCount('deliveries')->latest()->paginate(20);

        return view('admin.pages.webhooks.index', compact('webhooks'));
    }

    public function create()
    {
        return view('admin.pages.webhooks.form', [
            'webhook' => new Webhook,
            'events' => $this->availableEvents(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateWebhook($request);
        Webhook::create($validated);

        return redirect()->route('admin.webhooks.index')->with('success', 'تم إنشاء Webhook');
    }

    public function edit(Webhook $webhook)
    {
        return view('admin.pages.webhooks.form', [
            'webhook' => $webhook,
            'events' => $this->availableEvents(),
        ]);
    }

    public function update(Request $request, Webhook $webhook)
    {
        $validated = $this->validateWebhook($request);
        $webhook->update($validated);

        return redirect()->route('admin.webhooks.index')->with('success', 'تم تحديث Webhook');
    }

    public function destroy(Webhook $webhook)
    {
        $webhook->delete();

        return redirect()->route('admin.webhooks.index')->with('success', 'تم الحذف');
    }

    protected function validateWebhook(Request $request): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'events' => 'required|array|min:1',
            'events.*' => 'string|in:order.created,order.updated,product.updated',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        return $validated;
    }

    protected function availableEvents(): array
    {
        return [
            'order.created' => 'طلب جديد',
            'order.updated' => 'تحديث طلب',
            'product.updated' => 'تحديث منتج',
        ];
    }
}
