<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\BaseRequest;

class UpdateSystemAdminUserStatusRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'status' => 'required|string|in:active,inactive,suspended',
        ];
    }
}
