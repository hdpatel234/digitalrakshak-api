<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\BaseRequest;

class UpdatePackageRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'package_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id',
            'status' => 'nullable|in:active,inactive',
        ];
    }
}
