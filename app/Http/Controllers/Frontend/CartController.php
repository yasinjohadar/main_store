<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartPresenter;
use App\Services\CartService;
use App\Services\OrderTotalsService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected CartPresenter $cartPresenter,
        protected OrderTotalsService $orderTotalsService,
    ) {}

    public function index(Request $request)
    {
        $cart = $this->cartService->getCart();
        $viewData = $this->cartPresenter->toViewData($cart);
        $totals = $this->orderTotalsService->calculate($cart);
        $crossSellProducts = Product::crossSellsForCartItems(
            $cart->items->pluck('product_id'),
        );

        return view('frontend.pages.cart.index', array_merge($viewData, [
            'totals' => $totals,
            'crossSellProducts' => $crossSellProducts,
        ]));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'integer|min:1',
        ]);

        $product = Product::active()->findOrFail($request->product_id);
        $variantId = $request->product_variant_id ? (int) $request->product_variant_id : null;
        $quantity = (int) ($request->quantity ?? 1);

        if ($product->variants()->exists()) {
            if (! $variantId) {
                return $this->cartError($request, 'يرجى اختيار جميع خيارات المنتج');
            }
            ProductVariant::where('id', $variantId)->where('product_id', $product->id)->firstOrFail();
        }

        if ($variantId) {
            $variant = ProductVariant::where('id', $variantId)->where('product_id', $product->id)->firstOrFail();
            if ($product->track_quantity && ! $product->allow_backorder && $quantity > $variant->stock_quantity) {
                return $this->cartError($request, 'الكمية المطلوبة غير متوفرة في المخزون');
            }
        } elseif ($product->track_quantity && ! $product->allow_backorder && $quantity > $product->stock_quantity) {
            return $this->cartError($request, 'الكمية المطلوبة غير متوفرة في المخزون');
        }

        $this->cartService->add($product->id, $quantity, $variantId);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(array_merge(
                $this->cartPayload('تمت الإضافة إلى السلة'),
                [
                    'product' => [
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'url' => route('frontend.product.show', $product->slug),
                        'image' => $product->primary_image_url,
                    ],
                ]
            ));
        }

        return back()->with('success', 'تمت إضافة المنتج إلى السلة');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $cart = $this->cartService->getCart();
        $item = $cart->items()->find($id);
        if (! $item) {
            return $this->cartError($request, 'المنتج غير موجود في السلة', 404);
        }

        $product = $item->product;
        $variant = $item->variant;
        $stock = $variant ? $variant->stock_quantity : $product->stock_quantity;

        if ($product->track_quantity && ! $product->allow_backorder && $request->quantity > $stock) {
            return $this->cartError($request, 'الكمية المطلوبة غير متوفرة في المخزون', 422);
        }

        $this->cartService->update((int) $id, (int) $request->quantity);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($this->cartPayload('تم تحديث السلة'));
        }

        return back()->with('success', 'تم تحديث السلة');
    }

    public function destroy(Request $request, $id)
    {
        $this->cartService->remove((int) $id);

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
        $request->validate(['coupon' => 'required|string']);

        $result = $this->cartService->applyCoupon($request->coupon);
        if (! $result['success']) {
            return back()->withErrors(['coupon' => $result['message'] ?? 'كود الخصم غير صالح']);
        }

        return back()->with('success', 'تم تطبيق كود الخصم بنجاح');
    }

    public function removeCoupon(Request $request)
    {
        $this->cartService->removeCoupon();

        return back()->with('success', 'تم إزالة كود الخصم');
    }

    protected function cartPayload(?string $message = null): array
    {
        $cart = $this->cartService->getCart();
        $viewData = $this->cartPresenter->toViewData($cart);
        $count = $viewData['cartCount'];

        $payload = [
            'success' => true,
            'cart_count' => $count,
            'cart_total' => $viewData['cartTotal'],
            'dropdown_html' => $this->renderDropdown($viewData),
            'count_label' => $count . ' ' . ($count === 1 ? 'منتج' : 'منتجات'),
        ];

        if ($message) {
            $payload['message'] = $message;
        }

        return $payload;
    }

    protected function renderDropdown(array $viewData): string
    {
        return view('components.frontend.cart-dropdown-content', $viewData)->render();
    }

    protected function cartError(Request $request, string $message, int $status = 422)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => $message], $status);
        }

        return back()->withErrors(['quantity' => $message]);
    }
}
