<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\BaseRequest;

class UpdateCronJobRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'schedule' => 'required|string',
            'is_active' => 'boolean',
        ];
    }
}
