<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\BaseRequest;

class StoreServiceRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'service_name' => 'required|string|max:255',
            'service_code' => 'required|string|max:255|unique:services,service_code',
            'service_category' => 'required|exists:service_categories,id',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ];
    }
}
