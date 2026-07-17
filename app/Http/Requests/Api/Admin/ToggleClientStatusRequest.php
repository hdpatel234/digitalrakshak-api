<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\BaseRequest;

class ToggleClientStatusRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'status' => 'required|in:active,inactive,suspended'
        ];
    }
}
