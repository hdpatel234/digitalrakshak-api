<?php

namespace App\Http\Requests\Api\Client;

use App\Http\Requests\Api\BaseRequest;

class VerifyEmploymentRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'status' => 'required|in:verified,rejected,needs_changes',
            'remarks' => 'nullable|string'
        ];
    }
}
