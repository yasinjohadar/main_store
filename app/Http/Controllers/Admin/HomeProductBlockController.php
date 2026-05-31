<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HomeProductBlock;
use App\Services\HomePageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HomeProductBlockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit(string $blockKey): View
    {
        $block = HomeProductBlock::query()->where('block_key', $blockKey)->firstOrFail();
        $categories = Category::active()->ordered()->get();
        $labels = [
            HomeProductBlock::KEY_PRODUCT_1 => 'قسم منتجات 1',
            HomeProductBlock::KEY_PRODUCT_2 => 'قسم منتجات 2',
            HomeProductBlock::KEY_PRODUCT_3 => 'قسم منتجات 3',
            HomeProductBlock::KEY_FASHION_BANNER => 'بانر أزياء',
        ];

        return view('admin.pages.homepage.product-blocks.edit', [
            'block' => $block,
            'categories' => $categories,
            'label' => $labels[$blockKey] ?? $blockKey,
            'isFashion' => $blockKey === HomeProductBlock::KEY_FASHION_BANNER,
        ]);
    }

    public function update(Request $request, string $blockKey): RedirectResponse
    {
        $block = HomeProductBlock::query()->where('block_key', $blockKey)->firstOrFail();
        $isFashion = $blockKey === HomeProductBlock::KEY_FASHION_BANNER;

        $data = $request->validate([
            'section_title' => [$isFashion ? 'nullable' : 'required', 'string', 'max:255'],
            'banner_image' => ['nullable', 'image', 'max:4096'],
            'banner_bg_color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'banner_subtitle' => ['nullable', 'string', 'max:255'],
            'banner_title' => ['nullable', 'string', 'max:1000'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'button_url' => ['nullable', 'string', 'max:500'],
            'wrapper_class' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'remove_banner_image' => ['nullable', 'boolean'],
            'discount_percent' => ['nullable', 'string', 'max:10'],
            'promo_code' => ['nullable', 'string', 'max:50'],
            'promo_text' => ['nullable', 'string', 'max:500'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        unset($data['remove_banner_image'], $data['discount_percent'], $data['promo_code'], $data['promo_text']);

        if ($isFashion) {
            $settings = $block->settings ?? [];
            $settings['discount_percent'] = $request->input('discount_percent', '25');
            $settings['promo_code'] = $request->input('promo_code', 'SAVE25');
            $settings['promo_text'] = $request->input('promo_text');
            $data['settings'] = $settings;
        }

        if ($request->boolean('remove_banner_image')) {
            $this->deleteImage($block->banner_image);
            $data['banner_image'] = null;
        }

        if ($request->hasFile('banner_image')) {
            $this->deleteImage($block->banner_image);
            $data['banner_image'] = $request->file('banner_image')->store(HomePageService::UPLOAD_PATH . '/blocks', 'public');
        }

        $block->update($data);

        return redirect()->route('admin.homepage.index')->with('success', 'تم حفظ إعدادات القسم.');
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
