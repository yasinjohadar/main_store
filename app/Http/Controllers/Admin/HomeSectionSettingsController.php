<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateHomeSectionRequest;
use App\Models\HomeSection;
use App\Services\HomePageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeSectionSettingsController extends Controller
{
    public function __construct(
        protected HomePageService $homePage
    ) {
        $this->middleware('auth');
    }

    public function edit(HomeSection $homeSection): View
    {
        $registry = HomePageService::registryEntry($homeSection->key);
        $editable = ($registry['type'] ?? null) === 'simple' || $homeSection->key === 'brands';
        abort_if(! $registry || ! $editable, 404);

        $defaults = $this->homePage->defaultSectionData($homeSection->key);

        return view('admin.pages.homepage.sections.edit', compact('homeSection', 'registry', 'defaults'));
    }

    public function update(UpdateHomeSectionRequest $request, HomeSection $homeSection): RedirectResponse
    {
        $homeSection->update($request->validated());

        return redirect()
            ->route('admin.homepage.index')
            ->with('success', 'تم حفظ إعدادات القسم.');
    }
}
