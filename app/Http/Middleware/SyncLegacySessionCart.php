<?php

namespace App\Http\Middleware;

use App\Models\Product;
use App\Services\CartService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SyncLegacySessionCart
{
    public function __construct(protected CartService $cartService) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->get('legacy_cart_synced')) {
            return $next($request);
        }

        $legacy = $request->session()->get('cart', []);
        if (is_array($legacy) && ! empty($legacy)) {
            foreach ($legacy as $item) {
                if (empty($item['product_id'])) {
                    continue;
                }
                $product = Product::find($item['product_id']);
                if (! $product) {
                    continue;
                }
                $this->cartService->add(
                    (int) $item['product_id'],
                    (int) ($item['quantity'] ?? 1),
                    isset($item['variant_id']) ? (int) $item['variant_id'] : null,
                );
            }
            $request->session()->forget('cart');
        }

        $request->session()->put('legacy_cart_synced', true);

        return $next($request);
    }
}
