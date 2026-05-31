<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InlineUpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $field = $this->input('field');

        $valueRules = match ($field) {
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['active', 'draft', 'archived'])],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            default => ['required'],
        };

        return [
            'field' => ['required', Rule::in(['name', 'price', 'status', 'category_id'])],
            'value' => $valueRules,
        ];
    }

    public function messages(): array
    {
        return [
            'field.required' => 'الحقل مطلوب.',
            'field.in' => 'الحقل غير مدعوم.',
            'value.required' => 'القيمة مطلوبة.',
            'name.max' => 'الاسم طويل جداً.',
            'price.numeric' => 'السعر يجب أن يكون رقماً.',
            'price.min' => 'السعر لا يمكن أن يكون سالباً.',
            'status.in' => 'حالة غير صالحة.',
            'category_id.exists' => 'التصنيف غير موجود.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('field') === 'category_id' && $this->input('value') === '') {
            $this->merge(['value' => null]);
        }
    }
}
