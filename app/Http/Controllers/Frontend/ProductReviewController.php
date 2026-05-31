<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    public function store(Request $request, string $slug)
    {
        $product = Product::where('slug', $slug)->where('status', 'active')->firstOrFail();

        if (! $product->allow_reviews) {
            return back()->withErrors(['review' => 'المراجعات غير مفعّلة لهذا المنتج']);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'content' => 'required|string|max:5000',
        ]);

        $requireApproval = $product->reviews_require_approval;
        if ($requireApproval === null) {
            $requireApproval = (bool) site_setting('reviews_require_approval', true);
        }

        Review::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'rating' => $validated['rating'],
            'title' => $validated['title'],
            'comment' => $validated['content'],
            'status' => $requireApproval ? 'pending' : 'approved',
        ]);

        $message = $requireApproval
            ? 'شكراً! سيتم مراجعة تقييمك قبل نشره.'
            : 'شكراً! تم نشر تقييمك.';

        return back()->with('success', $message);
    }
}
