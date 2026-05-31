<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateStoreSettingsRequest;
use App\Models\OrderStatus;
use App\Services\ActivityLogger;
use App\Services\DynamicConfigLoader;
use App\Services\StoreSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StoreSettingsController extends Controller
{
    public function __construct(
        protected StoreSettingsService $storeSettings,
    ) {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $settings = $this->storeSettings->all();
        $schema = StoreSettingsService::schema();
        $sectionLabels = StoreSettingsService::sectionLabels();
        $orderStatuses = OrderStatus::orderBy('order')->get();

        return view('admin.pages.store-settings.index', compact(
            'settings',
            'schema',
            'sectionLabels',
            'orderStatuses',
        ));
    }

    public function update(UpdateStoreSettingsRequest $request): RedirectResponse
    {
        $this->storeSettings->setMany($request->validated());
        app(DynamicConfigLoader::class)->load();
        app(ActivityLogger::class)->siteSettingsUpdated(array_keys($request->validated()));

        return redirect()
            ->route('admin.store-settings.index')
            ->with('success', 'تم حفظ إعدادات المتجر بنجاح.');
    }
}
