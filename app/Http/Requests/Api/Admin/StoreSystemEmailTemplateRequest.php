<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\BaseRequest;

class StoreSystemEmailTemplateRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'template_name' => 'required|string|max:255',
            'template_code' => 'required|string|unique:email_templates,template_code',
            'email_type' => 'required|string|max:50',
            'subject' => 'required|string|max:255',
            'body_html' => 'required|string',
            'body_text' => 'nullable|string',
            'server_id' => 'required|exists:email_servers,id',
            'default_priority' => 'nullable|string|in:low,normal,high,critical',
            'variables' => 'nullable|array',
            'is_active' => 'boolean'
        ];
    }
}
