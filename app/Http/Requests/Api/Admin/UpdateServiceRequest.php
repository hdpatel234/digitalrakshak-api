<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\BaseRequest;

class UpdateServiceRequest extends BaseRequest
{
    public function rules(): array
    {
        $serviceId = $this->route('service') ? $this->route('service')->id : null;

        return [
            'service_name' => 'sometimes|required|string|max:255',
            'service_code' => 'sometimes|required|string|max:255|unique:services,service_code,' . $serviceId,
            'service_category' => 'sometimes|required|exists:service_categories,id',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'base_price' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|in:active,inactive',
        ];
    }
}
