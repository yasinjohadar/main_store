<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InlineUpdateCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $coupon = $this->route('coupon');
        $field = $this->input('field');

        $valueRules = match ($field) {
            'code' => ['required', 'string', 'max:50', Rule::unique('coupons', 'code')->ignore($coupon?->id)],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['percentage', 'fixed_amount', 'buy_x_get_y'])],
            'value' => ['required', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive', 'expired'])],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
            'applicable_to' => ['required', Rule::in(['entire_store', 'specific_products', 'specific_categories'])],
            default => ['required'],
        };

        return [
            'field' => ['required', Rule::in([
                'code', 'name', 'type', 'value', 'usage_limit',
                'status', 'starts_at', 'expires_at', 'applicable_to',
            ])],
            'value' => $valueRules,
        ];
    }

    public function messages(): array
    {
        return [
            'field.required' => 'الحقل مطلوب.',
            'field.in' => 'الحقل غير مدعوم.',
            'value.required' => 'القيمة مطلوبة.',
            'code.unique' => 'كود الكوبون مستخدم مسبقاً.',
            'value.numeric' => 'القيمة يجب أن تكون رقماً.',
            'value.min' => 'القيمة لا يمكن أن تكون سالبة.',
            'usage_limit.integer' => 'حد الاستخدام يجب أن يكون رقماً صحيحاً.',
            'status.in' => 'حالة غير صالحة.',
            'type.in' => 'نوع الخصم غير صالح.',
            'applicable_to.in' => 'نطاق التطبيق غير صالح.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $field = $this->input('field');

        if (in_array($field, ['usage_limit', 'starts_at', 'expires_at'], true) && $this->input('value') === '') {
            $this->merge(['value' => null]);
        }

        if ($field === 'code' && is_string($this->input('value'))) {
            $this->merge(['value' => strtoupper(trim($this->input('value')))]);
        }
    }
}
