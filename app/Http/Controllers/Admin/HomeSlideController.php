<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHomeSlideRequest;
use App\Http\Requests\Admin\UpdateHomeSlideRequest;
use App\Models\HomeSlide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HomeSlideController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $slides = HomeSlide::query()->ordered()->get();

        return view('admin.pages.home-slides.index', compact('slides'));
    }

    public function create(): View
    {
        return view('admin.pages.home-slides.create');
    }

    public function store(StoreHomeSlideRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['background_image'], $data['product_image']);

        $data['background_image'] = $request->file('background_image')->store('home-slides', 'public');

        if ($request->hasFile('product_image')) {
            $data['product_image'] = $request->file('product_image')->store('home-slides', 'public');
        }

        HomeSlide::create($data);

        return redirect()
            ->route('admin.home-slides.index')
            ->with('success', 'تم إضافة الشريحة بنجاح.');
    }

    public function edit(HomeSlide $homeSlide): View
    {
        return view('admin.pages.home-slides.edit', ['slide' => $homeSlide]);
    }

    public function update(UpdateHomeSlideRequest $request, HomeSlide $homeSlide): RedirectResponse
    {
        $data = $request->validated();
        unset(
            $data['background_image'],
            $data['product_image'],
            $data['remove_background_image'],
            $data['remove_product_image'],
        );

        if ($request->boolean('remove_background_image')) {
            $this->deleteStoredImage($homeSlide->background_image);
            $data['background_image'] = null;
        }

        if ($request->boolean('remove_product_image')) {
            $this->deleteStoredImage($homeSlide->product_image);
            $data['product_image'] = null;
        }

        if ($request->hasFile('background_image')) {
            $this->deleteStoredImage($homeSlide->background_image);
            $data['background_image'] = $request->file('background_image')->store('home-slides', 'public');
        }

        if ($request->hasFile('product_image')) {
            $this->deleteStoredImage($homeSlide->product_image);
            $data['product_image'] = $request->file('product_image')->store('home-slides', 'public');
        }

        $homeSlide->update($data);

        return redirect()
            ->route('admin.home-slides.index')
            ->with('success', 'تم تحديث الشريحة بنجاح.');
    }

    public function destroy(HomeSlide $homeSlide): RedirectResponse
    {
        $this->deleteStoredImage($homeSlide->background_image);
        $this->deleteStoredImage($homeSlide->product_image);
        $homeSlide->delete();

        return redirect()
            ->route('admin.home-slides.index')
            ->with('success', 'تم حذف الشريحة بنجاح.');
    }

    public function toggleActive(HomeSlide $homeSlide): RedirectResponse
    {
        $homeSlide->update(['is_active' => ! $homeSlide->is_active]);

        return back()->with('success', 'تم تحديث حالة الشريحة.');
    }

    protected function deleteStoredImage(?string $path): void
    {
        if (empty($path) || str_starts_with($path, 'frontend/')) {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
