<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Support\Collection;

class ProductVariantOptionService
{
    /**
     * @return list<array{attribute: ProductAttribute, values: Collection}>
     */
    public function forProduct(Product $product): array
    {
        $product->loadMissing(['variants.attributeValues.attribute', 'attributes']);

        if ($product->variants->isEmpty()) {
            return [];
        }

        $grouped = [];

        foreach ($product->variants as $variant) {
            foreach ($variant->attributeValues as $value) {
                $attribute = $value->attribute;
                if (! $attribute) {
                    continue;
                }

                $attrId = $attribute->id;
                $grouped[$attrId]['attribute'] = $attribute;
                $grouped[$attrId]['values'][$value->id] = $value;
            }
        }

        if ($grouped === []) {
            return [];
        }

        $attributeOrder = $product->attributes->values()->pluck('id')->flip();

        return collect($grouped)
            ->map(fn ($item) => [
                'attribute' => $item['attribute'],
                'values' => collect($item['values'])->sortBy('order')->values(),
            ])
            ->filter(fn ($item) => $item['attribute'] && $item['values']->isNotEmpty())
            ->sortBy(fn ($item) => $attributeOrder[$item['attribute']->id] ?? ($item['attribute']->order ?? 999))
            ->values()
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function variantsPayload(Product $product): array
    {
        $product->loadMissing(['variants.attributeValues']);

        return $product->variants->map(function ($variant) {
            $selection = [];
            foreach ($variant->attributeValues as $value) {
                $selection[(string) $value->product_attribute_id] = $value->id;
            }

            return [
                'id' => $variant->id,
                'price' => (float) $variant->effective_price,
                'compare_at_price' => $variant->compare_at_price ? (float) $variant->compare_at_price : null,
                'sku' => $variant->sku,
                'in_stock' => $variant->in_stock,
                'stock_quantity' => (int) $variant->stock_quantity,
                'is_default' => (bool) $variant->is_default,
                'attribute_value_ids' => $variant->attributeValues->pluck('id')->sort()->values()->all(),
                'selection' => $selection,
            ];
        })->values()->all();
    }

    public function defaultVariantId(Product $product): ?int
    {
        $product->loadMissing('variants');

        $default = $product->variants->firstWhere(fn ($v) => $v->is_default && $v->in_stock)
            ?? $product->variants->firstWhere('in_stock', true)
            ?? $product->variants->firstWhere('is_default', true)
            ?? $product->variants->first();

        return $default?->id;
    }
}
