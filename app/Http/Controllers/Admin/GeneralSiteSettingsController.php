<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SiteSettingsService;
use App\Services\ActivityLogger;
use App\Http\Requests\Admin\UpdateSiteSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GeneralSiteSettingsController extends Controller
{
    public function __construct(
        protected SiteSettingsService $siteSettings
    ) {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $settings = $this->siteSettings->all();
        $schema = SiteSettingsService::schema();
        $sectionLabels = SiteSettingsService::sectionLabels();

        return view('admin.pages.site-settings.index', compact('settings', 'schema', 'sectionLabels'));
    }

    public function update(UpdateSiteSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->boolean('remove_site_logo')) {
            $this->siteSettings->deleteAsset(SiteSettingsService::KEY_SITE_LOGO);
        }

        if ($request->boolean('remove_site_favicon')) {
            $this->siteSettings->deleteAsset(SiteSettingsService::KEY_SITE_FAVICON);
        }

        if ($request->boolean('remove_newsletter_popup_image')) {
            $this->siteSettings->deleteAsset(SiteSettingsService::KEY_NEWSLETTER_POPUP_IMAGE);
            $this->siteSettings->setMany([
                SiteSettingsService::KEY_NEWSLETTER_POPUP_IMAGE => SiteSettingsService::DEFAULT_NEWSLETTER_POPUP_IMAGE,
            ]);
        }

        $logoFile = $request->file('site_logo_file');
        $faviconFile = $request->file('site_favicon_file');
        $newsletterImageFile = $request->file('newsletter_popup_image_file');

        if ($logoFile) {
            $this->siteSettings->storeUpload(SiteSettingsService::KEY_SITE_LOGO, $logoFile);
        }

        if ($faviconFile) {
            $this->siteSettings->storeUpload(SiteSettingsService::KEY_SITE_FAVICON, $faviconFile);
        }

        if ($newsletterImageFile) {
            $this->siteSettings->storeUpload(SiteSettingsService::KEY_NEWSLETTER_POPUP_IMAGE, $newsletterImageFile);
        }

        unset(
            $validated['site_logo_file'],
            $validated['site_favicon_file'],
            $validated['newsletter_popup_image_file'],
            $validated['remove_site_logo'],
            $validated['remove_site_favicon'],
            $validated['remove_newsletter_popup_image'],
        );

        $this->siteSettings->setMany($validated);

        app(ActivityLogger::class)->siteSettingsUpdated(array_keys($validated));

        return redirect()
            ->route('admin.site-settings.index')
            ->with('success', 'تم حفظ إعدادات الموقع بنجاح.');
    }
}
