<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\BaseRequest;

class StoreServiceProviderRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'provider_name' => 'required|string|max:255',
            'provider_code' => 'required|string|max:100|unique:service_providers',
            'provider_type' => 'nullable|in:api,webhook,manual',
            'logo' => 'nullable|string',
            'description' => 'nullable|string',
            'website' => 'nullable|string|max:500',
            'documentation_url' => 'nullable|string|max:500',
            'status' => 'nullable|in:active,inactive,maintenance,deprecated',
            'is_default' => 'nullable|boolean',
            'priority' => 'nullable|integer',
        ];
    }
}
