<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\BaseRequest;

class UpdateSystemEmailTemplateRequest extends BaseRequest
{
    public function rules(): array
    {
        $id = $this->route('id') ?? ($this->route('template') ? $this->route('template')->id : null);

        return [
            'template_name' => 'sometimes|required|string|max:255',
            'template_code' => 'sometimes|required|string|unique:email_templates,template_code,' . $id,
            'email_type' => 'sometimes|required|string|max:50',
            'subject' => 'sometimes|required|string|max:255',
            'body_html' => 'sometimes|required|string',
            'body_text' => 'nullable|string',
            'server_id' => 'required|exists:email_servers,id',
            'default_priority' => 'nullable|string|in:low,normal,high,critical',
            'variables' => 'nullable|array',
            'is_active' => 'boolean'
        ];
    }
}
