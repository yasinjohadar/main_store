<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeBrand;
use App\Services\HomePageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HomeBrandController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $brands = HomeBrand::query()->ordered()->get();

        return view('admin.pages.homepage.brands.index', compact('brands'));
    }

    public function create(): View
    {
        return view('admin.pages.homepage.brands.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, true);
        $data['logo'] = $request->file('logo')->store(HomePageService::UPLOAD_PATH . '/brands', 'public');

        HomeBrand::create($data);

        return redirect()->route('admin.homepage.brands.index')->with('success', 'تم إضافة الشعار.');
    }

    public function edit(HomeBrand $brand): View
    {
        return view('admin.pages.homepage.brands.edit', compact('brand'));
    }

    public function update(Request $request, HomeBrand $brand): RedirectResponse
    {
        $data = $this->validated($request, false);
        unset($data['remove_logo']);

        if ($request->boolean('remove_logo')) {
            $this->deleteImage($brand->logo);
            $data['logo'] = null;
        }

        if ($request->hasFile('logo')) {
            $this->deleteImage($brand->logo);
            $data['logo'] = $request->file('logo')->store(HomePageService::UPLOAD_PATH . '/brands', 'public');
        }

        $brand->update($data);

        return redirect()->route('admin.homepage.brands.index')->with('success', 'تم تحديث الشعار.');
    }

    public function destroy(HomeBrand $brand): RedirectResponse
    {
        $this->deleteImage($brand->logo);
        $brand->delete();

        return redirect()->route('admin.homepage.brands.index')->with('success', 'تم حذف الشعار.');
    }

    protected function validated(Request $request, bool $logoRequired): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => [$logoRequired ? 'required' : 'nullable', 'image', 'max:2048'],
            'url' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'remove_logo' => ['nullable', 'boolean'],
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
