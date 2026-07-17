<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\BaseRequest;

class StoreSupportTicketRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'client_id' => 'required|integer',
            'title' => 'required|string',
            'message' => 'required|string',
            'department' => 'required|integer',
            'priority' => 'required|integer',
        ];
    }
}
