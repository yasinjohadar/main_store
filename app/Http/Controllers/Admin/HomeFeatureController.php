<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeFeature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeFeatureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $features = HomeFeature::query()->ordered()->get();

        return view('admin.pages.homepage.features.index', compact('features'));
    }

    public function create(): View
    {
        return view('admin.pages.homepage.features.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'icon' => ['required', 'string', 'max:100'],
            'tone' => ['required', 'string', 'in:blue,teal,amber,violet'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active', true);

        HomeFeature::create($data);

        return redirect()->route('admin.homepage.features.index')->with('success', 'تم إضافة الميزة.');
    }

    public function edit(HomeFeature $feature): View
    {
        return view('admin.pages.homepage.features.edit', compact('feature'));
    }

    public function update(Request $request, HomeFeature $feature): RedirectResponse
    {
        $data = $request->validate([
            'icon' => ['required', 'string', 'max:100'],
            'tone' => ['required', 'string', 'in:blue,teal,amber,violet'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $feature->update($data);

        return redirect()->route('admin.homepage.features.index')->with('success', 'تم تحديث الميزة.');
    }

    public function destroy(HomeFeature $feature): RedirectResponse
    {
        $feature->delete();

        return redirect()->route('admin.homepage.features.index')->with('success', 'تم حذف الميزة.');
    }
}
