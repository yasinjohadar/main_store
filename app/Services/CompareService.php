<?php

namespace App\Services;

use App\Models\Product;

class CompareService
{
    public const MAX_ITEMS = 4;

    public function ids(): array
    {
        return array_slice(array_map('intval', session('compare', [])), 0, self::MAX_ITEMS);
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

        $compare = $this->ids();

        if (in_array($productId, $compare, true)) {
            return;
        }

        if (count($compare) >= self::MAX_ITEMS) {
            array_shift($compare);
        }

        $compare[] = $productId;
        session(['compare' => $compare]);
    }

    public function remove(int $productId): void
    {
        $compare = array_values(array_filter(
            $this->ids(),
            fn ($id) => (int) $id !== $productId
        ));

        session(['compare' => $compare]);
    }

    public function clear(): void
    {
        session()->forget('compare');
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

    public function products()
    {
        $ids = $this->ids();

        if (empty($ids)) {
            return collect();
        }

        return Product::query()
            ->whereIn('id', $ids)
            ->where('status', 'active')
            ->where('is_visible', true)
            ->get()
            ->sortBy(fn ($product) => array_search($product->id, $ids, true))
            ->values();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function barPayload(): array
    {
        return $this->products()->map(function (Product $product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'url' => route('frontend.product.show', $product->slug),
                'image' => $product->primary_image_url,
                'delete_url' => route('frontend.compare.destroy', $product),
            ];
        })->all();
    }
}
