<?php

namespace App\Http\Requests\Admin;

use App\Services\IntegrationSettingsService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateIntegrationSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return IntegrationSettingsService::validationRules();
    }
}
