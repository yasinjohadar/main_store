<?php

namespace App\Http\Requests\Admin;

use App\Support\SiteTheme;
use Illuminate\Foundation\Http\FormRequest;

class StoreHomeSlideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'badge' => ['nullable', 'string', 'max:100'],
            'background_image' => ['required', 'image', 'max:4096'],
            'product_image' => ['nullable', 'image', 'max:4096'],
            'background_color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'accent_color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'button_text' => ['required', 'string', 'max:100'],
            'button_url' => ['required', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'accent_color' => $this->normalizeHex($this->input('accent_color')) ?: SiteTheme::primary(),
            'background_color' => $this->normalizeHex($this->input('background_color')) ?: '#e8edf3',
        ]);
    }

    protected function normalizeHex(?string $color): ?string
    {
        if (! is_string($color) || trim($color) === '') {
            return null;
        }

        return SiteTheme::normalizeHex($color);
    }
}
