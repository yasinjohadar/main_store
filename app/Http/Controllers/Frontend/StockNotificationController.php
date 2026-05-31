<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockNotificationController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'email' => 'nullable|email|max:255',
        ]);

        $email = $validated['email'] ?? Auth::user()?->email;
        if (! $email) {
            return back()->withErrors(['email' => 'يرجى إدخال بريد إلكتروني.']);
        }

        StockNotification::firstOrCreate(
            [
                'product_id' => $product->id,
                'email' => $email,
            ],
            [
                'user_id' => Auth::id(),
                'notified' => false,
            ],
        );

        return back()->with('success', 'سنُبلغك عند توفر المنتج.');
    }
}
