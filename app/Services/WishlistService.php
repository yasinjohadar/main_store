<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistService
{
    public function ids(): array
    {
        if (Auth::check()) {
            return Auth::user()
                ->wishlists()
                ->pluck('product_id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        return array_map('intval', session('wishlist', []));
    }

    public function count(): int
    {
        return count($this->ids());
    }

    public function contains(int $productId): bool
    {
        return in_array($productId, $this->ids(), true);
    }

    public function add(int $productId): void
    {
        Product::active()->findOrFail($productId);

        if (Auth::check()) {
            Wishlist::firstOrCreate([
                'user_id' => Auth::id(),
                'product_id' => $productId,
            ]);

            return;
        }

        $wishlist = session('wishlist', []);

        if (! in_array($productId, $wishlist, true)) {
            $wishlist[] = $productId;
            session(['wishlist' => $wishlist]);
        }
    }

    public function remove(int $productId): void
    {
        if (Auth::check()) {
            Wishlist::query()
                ->where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->delete();

            return;
        }

        $wishlist = array_values(array_filter(
            session('wishlist', []),
            fn ($id) => (int) $id !== $productId
        ));

        session(['wishlist' => $wishlist]);
    }

    /**
     * @return bool True when the product was added, false when removed.
     */
    public function toggle(int $productId): bool
    {
        if ($this->contains($productId)) {
            $this->remove($productId);

            return false;
        }

        $this->add($productId);

        return true;
    }
}
