<?php

namespace App\Http\Requests\Admin;

use App\Services\StoreSettingsService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return StoreSettingsService::validationRules();
    }
}
