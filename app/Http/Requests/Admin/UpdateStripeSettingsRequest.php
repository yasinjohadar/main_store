<?php

namespace App\Http\Requests\Admin;

use App\Services\IntegrationSettingsService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStripeSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            IntegrationSettingsService::KEY_STRIPE_KEY => ['nullable', 'string', 'max:500'],
            IntegrationSettingsService::KEY_STRIPE_SECRET => ['nullable', 'string', 'max:500'],
            IntegrationSettingsService::KEY_STRIPE_WEBHOOK_SECRET => ['nullable', 'string', 'max:500'],
        ];
    }
}
