<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateStripeSettingsRequest;
use App\Services\ActivityLogger;
use App\Services\DynamicConfigLoader;
use App\Services\IntegrationSettingsService;
use App\Services\Payments\StripeCredentialResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StripeSettingsController extends Controller
{
    public function __construct(
        protected IntegrationSettingsService $integrationSettings,
        protected StripeCredentialResolver $credentials,
    ) {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $keys = [
            IntegrationSettingsService::KEY_STRIPE_KEY,
            IntegrationSettingsService::KEY_STRIPE_SECRET,
            IntegrationSettingsService::KEY_STRIPE_WEBHOOK_SECRET,
        ];

        $settings = [];
        foreach ($keys as $key) {
            $settings[$key] = $this->integrationSettings->get($key, '');
        }

        $storedSecrets = collect([
            IntegrationSettingsService::KEY_STRIPE_SECRET,
            IntegrationSettingsService::KEY_STRIPE_WEBHOOK_SECRET,
        ])->filter(fn ($key) => $this->integrationSettings->hasStored($key));

        $status = [
            'sdk_installed' => class_exists(\Stripe\Stripe::class),
            'publishable_configured' => (bool) $this->credentials->publishableKey(),
            'secret_configured' => (bool) $this->credentials->secretKey(),
            'webhook_configured' => (bool) $this->credentials->webhookSecret(),
            'test_mode' => $this->credentials->isTestMode(),
        ];

        $webhookUrl = url('/api/webhooks/stripe');
        $requiredEvents = [
            'checkout.session.completed',
            'checkout.session.expired',
            'payment_intent.payment_failed',
            'charge.refunded',
        ];

        return view('admin.pages.stripe-settings.index', compact(
            'settings',
            'storedSecrets',
            'status',
            'webhookUrl',
            'requiredEvents',
        ));
    }

    public function update(UpdateStripeSettingsRequest $request): RedirectResponse
    {
        $this->integrationSettings->setMany($request->validated());
        app(DynamicConfigLoader::class)->load();
        app(ActivityLogger::class)->siteSettingsUpdated(array_keys($request->validated()));

        return redirect()
            ->route('admin.stripe-settings.index')
            ->with('success', 'تم حفظ إعدادات Stripe بنجاح.');
    }

    public function testConnection(): JsonResponse
    {
        if (! class_exists(\Stripe\Stripe::class)) {
            return response()->json([
                'success' => false,
                'message' => 'حزمة stripe/stripe-php غير مثبتة. نفّذ: composer require stripe/stripe-php',
            ], 422);
        }

        $secret = $this->credentials->secretKey();
        if (! $secret) {
            return response()->json([
                'success' => false,
                'message' => 'المفتاح السري غير مُعد. أضفه في الحقول أدناه أو في .env',
            ], 422);
        }

        try {
            \Stripe\Stripe::setApiKey($secret);
            $balance = \Stripe\Balance::retrieve();

            return response()->json([
                'success' => true,
                'message' => 'الاتصال ناجح. الوضع: ' . ($this->credentials->isTestMode($secret) ? 'تجريبي (test)' : 'مباشر (live)'),
                'currency' => $balance->available[0]->currency ?? null,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
