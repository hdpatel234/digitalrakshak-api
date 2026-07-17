<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\BaseRequest;

class StoreServiceFieldRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'service_id' => 'required|exists:services,id',
            'field_name' => 'required|string|max:100',
            'section' => 'nullable|string|max:100',
            'field_label' => 'required|string|max:100',
            'field_type' => 'required|string|max:50',
            'is_required' => 'boolean',
            'validation_regex' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer',
            'status' => 'nullable|string|max:50',
        ];
    }
}
