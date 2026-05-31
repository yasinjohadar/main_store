<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomePromoBanner;
use App\Services\HomePageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HomePromoBannerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $banners = HomePromoBanner::query()->ordered()->get();

        return view('admin.pages.homepage.promo-banners.index', compact('banners'));
    }

    public function create(): View
    {
        return view('admin.pages.homepage.promo-banners.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, true);
        $data['image'] = $request->file('image')->store(HomePageService::UPLOAD_PATH . '/promos', 'public');

        HomePromoBanner::create($data);

        return redirect()->route('admin.homepage.promo-banners.index')->with('success', 'تم إضافة البانر.');
    }

    public function edit(HomePromoBanner $promoBanner): View
    {
        return view('admin.pages.homepage.promo-banners.edit', ['banner' => $promoBanner]);
    }

    public function update(Request $request, HomePromoBanner $promoBanner): RedirectResponse
    {
        $data = $this->validated($request, false);
        unset($data['remove_image']);

        if ($request->boolean('remove_image')) {
            $this->deleteImage($promoBanner->image);
            $data['image'] = null;
        }

        if ($request->hasFile('image')) {
            $this->deleteImage($promoBanner->image);
            $data['image'] = $request->file('image')->store(HomePageService::UPLOAD_PATH . '/promos', 'public');
        }

        $promoBanner->update($data);

        return redirect()->route('admin.homepage.promo-banners.index')->with('success', 'تم تحديث البانر.');
    }

    public function destroy(HomePromoBanner $promoBanner): RedirectResponse
    {
        $this->deleteImage($promoBanner->image);
        $promoBanner->delete();

        return redirect()->route('admin.homepage.promo-banners.index')->with('success', 'تم حذف البانر.');
    }

    protected function validated(Request $request, bool $imageRequired): array
    {
        $data = $request->validate([
            'image' => [$imageRequired ? 'required' : 'nullable', 'image', 'max:4096'],
            'background_color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'tone' => ['required', 'in:light,dark'],
            'badge' => ['nullable', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'price_label' => ['nullable', 'string', 'max:100'],
            'price_text' => ['nullable', 'string', 'max:100'],
            'button_url' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'remove_image' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }

    protected function deleteImage(?string $path): void
    {
        if (empty($path) || str_starts_with($path, 'frontend/')) {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
