<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\BaseRequest;

class ReplySupportTicketRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'message' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|file',
            'attachment' => 'nullable|array',
            'attachment.*' => 'nullable|file',
        ];
    }
}
