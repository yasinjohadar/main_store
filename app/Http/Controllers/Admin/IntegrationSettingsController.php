<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateIntegrationSettingsRequest;
use App\Services\ActivityLogger;
use App\Services\DynamicConfigLoader;
use App\Services\IntegrationSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class IntegrationSettingsController extends Controller
{
    public function __construct(
        protected IntegrationSettingsService $integrationSettings,
    ) {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $settings = $this->integrationSettings->all();
        $schema = IntegrationSettingsService::schema();
        $sectionLabels = IntegrationSettingsService::sectionLabels();
        $secretKeys = IntegrationSettingsService::SECRET_KEYS;
        $storedSecrets = collect($secretKeys)->filter(fn ($key) => $this->integrationSettings->hasStored($key));

        $webhookUrls = [
            'stripe' => url('/api/webhooks/stripe'),
            'paypal' => url('/api/webhooks/paypal'),
        ];

        return view('admin.pages.integration-settings.index', compact(
            'settings',
            'schema',
            'sectionLabels',
            'secretKeys',
            'storedSecrets',
            'webhookUrls',
        ));
    }

    public function update(UpdateIntegrationSettingsRequest $request): RedirectResponse
    {
        $this->integrationSettings->setMany($request->validated());
        app(DynamicConfigLoader::class)->load();
        app(ActivityLogger::class)->siteSettingsUpdated(array_keys($request->validated()));

        return redirect()
            ->route('admin.integration-settings.index')
            ->with('success', 'تم حفظ إعدادات التكامل بنجاح.');
    }
}
