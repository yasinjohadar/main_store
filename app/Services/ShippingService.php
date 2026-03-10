<?php

namespace App\Services;

use App\Models\OrderAddress;
use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use App\Models\ShoppingCart;

class ShippingService
{
    /**
     * احسب تكلفة الشحن المتاحة وحدد أفضل طريقة شحن بناءً على عنوان الشحن ومحتوى السلة.
     */
    public function calculateForCart(ShoppingCart $cart, array $shippingAddress): array
    {
        $zone = $this->matchZone($shippingAddress);
        if (!$zone) {
            return [
                'methods' => [],
                'selected_method' => null,
                'shipping_amount' => 0,
            ];
        }

        $cartWeight = $cart->items->sum(function ($item) {
            $product = $item->product;
            return (float) ($product->weight ?? 0) * $item->quantity;
        });
        $cartSubtotal = $cart->subtotal;

        $methods = [];
        foreach ($zone->methods()->where('is_active', true)->get() as $method) {
            $cost = $this->calculateMethodCost($method, $cartWeight, $cartSubtotal);
            if ($cost === null) {
                continue;
            }
            $methods[] = [
                'id' => $method->id,
                'name' => $method->name,
                'type' => $method->type,
                'cost' => $cost,
            ];
        }

        usort($methods, function ($a, $b) {
            return $a['cost'] <=> $b['cost'];
        });

        $selected = $methods[0] ?? null;

        return [
            'methods' => $methods,
            'selected_method' => $selected,
            'shipping_amount' => $selected['cost'] ?? 0,
        ];
    }

    /**
     * احسب تكلفة طريقة شحن واحدة بناءً على قواعدها.
     */
    protected function calculateMethodCost(ShippingMethod $method, float $cartWeight, float $cartSubtotal): ?float
    {
        // تحقق من الحد الأدنى للمجموع إن وُجد
        if ($method->min_cart_total !== null && $cartSubtotal < (float) $method->min_cart_total) {
            return null;
        }

        // طرق أساسية
        if ($method->type === ShippingMethod::TYPE_FREE_SHIPPING) {
            return 0.0;
        }

        if (in_array($method->type, [ShippingMethod::TYPE_FLAT_RATE, ShippingMethod::TYPE_BY_WEIGHT, ShippingMethod::TYPE_BY_PRICE], true)) {
            $cost = (float) $method->base_cost;

            foreach ($method->rules as $rule) {
                $value = $rule->condition_type === 'weight' ? $cartWeight : $cartSubtotal;
                if ($value < (float) $rule->min_value) {
                    continue;
                }
                if ($rule->max_value !== null && $value > (float) $rule->max_value) {
                    continue;
                }

                $ruleCost = (float) $rule->cost;
                if ($rule->per_unit !== null && $rule->per_unit > 0) {
                    $units = max(0, $value - (float) $rule->min_value);
                    $ruleCost += ceil($units) * (float) $rule->per_unit;
                }

                $cost += $ruleCost;
            }

            return max(0.0, $cost);
        }

        return null;
    }

    /**
     * العثور على المنطقة المناسبة لعنوان الشحن.
     */
    protected function matchZone(array $shippingAddress): ?ShippingZone
    {
        $country = $shippingAddress['country'] ?? null;
        $state = $shippingAddress['state'] ?? null;
        $city = $shippingAddress['city'] ?? null;
        $postal = $shippingAddress['postal_code'] ?? null;

        if (!$country) {
            return null;
        }

        $zones = ShippingZone::with('locations')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        foreach ($zones as $zone) {
            foreach ($zone->locations as $location) {
                if ($location->country_code && strtoupper($location->country_code) !== strtoupper($country)) {
                    continue;
                }
                if ($location->state && $state && strcasecmp($location->state, $state) !== 0) {
                    continue;
                }
                if ($location->city && $city && strcasecmp($location->city, $city) !== 0) {
                    continue;
                }
                if ($location->postal_code_pattern && $postal) {
                    $pattern = '#^' . str_replace(['*', '%'], '.*', preg_quote($location->postal_code_pattern, '#')) . '$#i';
                    if (!preg_match($pattern, $postal)) {
                        continue;
                    }
                }

                return $zone;
            }
        }

        return null;
    }
}
