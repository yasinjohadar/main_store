<?php

namespace App\Services;

use App\Models\ShoppingCart;
use Illuminate\Support\Collection;

class CartPresenter
{
    public function items(ShoppingCart $cart): Collection
    {
        $cart->loadMissing('items.product.images', 'items.variant');

        return $cart->items->map(function ($item) {
            $product = $item->product;
            $variant = $item->variant;
            $name = $product->name;
            if ($variant) {
                $name .= ' — ' . $variant->display_name;
            }

            return [
                'row_id' => (string) $item->id,
                'cart_item_id' => $item->id,
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'name' => $name,
                'slug' => $product->slug,
                'price' => (float) $item->unit_price,
                'quantity' => $item->quantity,
                'subtotal' => (float) $item->line_total,
                'image' => $product->images->firstWhere('is_primary', true)?->path ?? $product->images->first()?->path,
                'image_url' => $product->primary_image_url,
                'stock' => $variant ? $variant->stock_quantity : $product->stock_quantity,
            ];
        });
    }

    public function toViewData(ShoppingCart $cart): array
    {
        $items = $this->items($cart);

        return [
            'cart' => $cart,
            'cartItems' => $items->all(),
            'cartTotal' => (float) $cart->subtotal,
            'cartCount' => (int) $cart->items_count,
            'couponCode' => $cart->coupon_code,
            'discountAmount' => (float) $cart->discount_amount,
        ];
    }
}
