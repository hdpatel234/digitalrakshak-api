<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\BaseRequest;

class ToggleServiceProviderStatusRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'status' => 'required|in:active,inactive,maintenance,deprecated'
        ];
    }
}
