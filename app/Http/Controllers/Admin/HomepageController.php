<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeSection;
use App\Services\HomePageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomepageController extends Controller
{
    public function __construct(
        protected HomePageService $homePage
    ) {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $sections = $this->homePage->allSections();
        $registry = HomePageService::registry();

        return view('admin.pages.homepage.index', [
            'sections' => $sections,
            'registry' => $registry,
            'homePage' => $this->homePage,
        ]);
    }

    public function toggleVisible(HomeSection $homeSection): RedirectResponse
    {
        $homeSection->update(['is_visible' => ! $homeSection->is_visible]);

        return back()->with('success', 'تم تحديث حالة القسم.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $request->validate([
            'sort_order' => ['required', 'array'],
            'sort_order.*' => ['integer', 'min:0'],
        ]);

        foreach ($request->input('sort_order') as $key => $order) {
            HomeSection::query()->where('key', $key)->update(['sort_order' => (int) $order]);
        }

        return back()->with('success', 'تم حفظ ترتيب الأقسام.');
    }
}
