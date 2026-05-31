<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductTagController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $tags = ProductTag::withCount('products')->orderBy('name')->paginate(30);

        return view('admin.pages.product-tags.index', compact('tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:product_tags,name',
        ]);

        $slug = Str::slug($validated['name']);
        $original = $slug;
        $counter = 1;
        while (ProductTag::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $counter++;
        }

        ProductTag::create([
            'name' => $validated['name'],
            'slug' => $slug,
        ]);

        return back()->with('success', 'تم إضافة الوسم');
    }

    public function destroy(ProductTag $productTag)
    {
        $productTag->delete();

        return back()->with('success', 'تم حذف الوسم');
    }
}
