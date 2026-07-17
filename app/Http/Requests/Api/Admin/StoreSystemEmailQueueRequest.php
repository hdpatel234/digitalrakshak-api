<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\BaseRequest;

class StoreSystemEmailQueueRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'to_email' => 'required|email',
            'template_id' => 'required|exists:email_templates,id',
            'variables' => 'nullable|array',
            'subject' => 'nullable|string|max:255',
            'body_html' => 'nullable|string',
        ];
    }
}
