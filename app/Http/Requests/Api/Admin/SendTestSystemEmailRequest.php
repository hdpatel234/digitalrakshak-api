<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\BaseRequest;

class SendTestSystemEmailRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email'
        ];
    }
}
