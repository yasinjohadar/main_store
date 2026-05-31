<?php

namespace App\DTOs;

class CartTotalsDTO
{
    public function __construct(
        public float $subtotal = 0,
        public float $discount = 0,
        public float $shipping = 0,
        public float $tax = 0,
        public float $pointsDiscount = 0,
        public float $total = 0,
        public ?string $couponCode = null,
        public array $shippingMethods = [],
        public ?array $selectedShippingMethod = null,
        public array $taxItems = [],
        public int $itemsCount = 0,
    ) {}

    public function toArray(): array
    {
        return [
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'shipping' => $this->shipping,
            'tax' => $this->tax,
            'points_discount' => $this->pointsDiscount,
            'total' => $this->total,
            'coupon_code' => $this->couponCode,
            'shipping_methods' => $this->shippingMethods,
            'selected_shipping_method' => $this->selectedShippingMethod,
            'tax_items' => $this->taxItems,
            'items_count' => $this->itemsCount,
        ];
    }
}
