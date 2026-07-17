<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\BaseRequest;

class SetClientPricingRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'services' => 'required|array',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.is_enabled' => 'required|boolean',
            'services.*.custom_price' => 'nullable|numeric|min:0',
        ];
    }
}
