<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = $this->getCartItems();
        $cartTotal = $this->getCartTotal();

        return view('frontend.pages.cart.index', compact('cartItems', 'cartTotal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1',
        ]);

        $product = Product::active()->findOrFail($request->product_id);
        $quantity = $request->quantity ?? 1;

        if ($product->track_quantity && !$product->allow_backorder && $quantity > $product->stock_quantity) {
            return back()->withErrors(['quantity' => 'الكمية المطلوبة غير متوفرة في المخزون']);
        }

        $cart = $this->getCart();
        $rowId = $this->generateRowId($product->id, $request->product_variant_id ?? null);

        if (isset($cart[$rowId])) {
            $cart[$rowId]['quantity'] += $quantity;
            $cart[$rowId]['price'] = $product->effective_price;
            $cart[$rowId]['image_url'] = $product->primary_image_url;
        } else {
            $cart[$rowId] = [
                'product_id' => $product->id,
                'variant_id' => $request->product_variant_id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->effective_price,
                'quantity' => $quantity,
                'image' => $product->primary_image?->path,
                'image_url' => $product->primary_image_url,
                'stock' => $product->stock_quantity,
            ];
        }

        session(['cart' => $cart]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تمت الإضافة إلى السلة',
                'product' => [
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'url' => route('frontend.product.show', $product->slug),
                    'image' => $product->primary_image_url,
                ],
                'cart_count' => count($cart),
                'cart_total' => $this->getCartTotal(),
                'dropdown_html' => $this->renderDropdown(),
            ]);
        }

        return back()->with('success', 'تمت إضافة المنتج إلى السلة');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->getCart();

        if (!isset($cart[$id])) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => 'المنتج غير موجود في السلة'], 404);
            }

            return back()->withErrors(['cart' => 'المنتج غير موجود في السلة']);
        }

        $product = Product::find($cart[$id]['product_id']);

        if ($product && $product->track_quantity && !$product->allow_backorder && $request->quantity > $product->stock_quantity) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => 'الكمية المطلوبة غير متوفرة في المخزون'], 422);
            }

            return back()->withErrors(['quantity' => 'الكمية المطلوبة غير متوفرة في المخزون']);
        }

        $cart[$id]['quantity'] = $request->quantity;

        if ($cart[$id]['quantity'] <= 0) {
            unset($cart[$id]);
        }

        session(['cart' => $cart]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($this->cartPayload('تم تحديث السلة'));
        }

        return back()->with('success', 'تم تحديث السلة');
    }

    public function destroy(Request $request, $id)
    {
        $cart = $this->getCart();

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session(['cart' => $cart]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($this->cartPayload('تم إزالة المنتج من السلة'));
        }

        return back()->with('success', 'تم إزالة المنتج من السلة');
    }

    public function dropdown()
    {
        return response()->json($this->cartPayload());
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon' => 'required|string',
        ]);

        // TODO: Implement coupon validation logic
        return back()->withErrors(['coupon' => 'كود الخصم غير صالح']);
    }

    public function removeCoupon(Request $request)
    {
        session()->forget(['discount', 'coupon_code']);
        return back()->with('success', 'تم إزالة كود الخصم');
    }

    protected function getCart()
    {
        return session('cart', []);
    }

    protected function getCartItems()
    {
        $cart = $this->getCart();
        $items = [];

        foreach ($cart as $rowId => $item) {
            $items[] = array_merge($item, [
                'row_id' => $rowId,
                'subtotal' => $item['price'] * $item['quantity'],
            ]);
        }

        return $items;
    }

    protected function getCartTotal()
    {
        $cart = $this->getCart();
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $total;
    }

    protected function generateRowId($productId, $variantId = null)
    {
        return md5($productId . '_' . ($variantId ?? 'null'));
    }

    protected function renderDropdown(): string
    {
        return view('components.frontend.cart-dropdown-content', [
            'cartItems' => $this->getCartItems(),
            'cartTotal' => $this->getCartTotal(),
        ])->render();
    }

    protected function cartPayload(?string $message = null): array
    {
        $items = $this->getCartItems();
        $count = count($items);

        $payload = [
            'success' => true,
            'cart_count' => $count,
            'cart_total' => $this->getCartTotal(),
            'dropdown_html' => $this->renderDropdown(),
            'count_label' => $count . ' ' . ($count === 1 ? 'منتج' : 'منتجات'),
        ];

        if ($message) {
            $payload['message'] = $message;
        }

        return $payload;
    }
}
