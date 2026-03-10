<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreShippingMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:flat_rate,free_shipping,by_weight,by_price'],
            'is_active' => ['sometimes', 'boolean'],
            'base_cost' => ['nullable', 'numeric', 'min:0'],
            'min_cart_total' => ['nullable', 'numeric', 'min:0'],
            'order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}

