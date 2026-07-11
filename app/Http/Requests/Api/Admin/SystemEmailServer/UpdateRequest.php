<?php

namespace App\Http\Requests\Api\Admin\SystemEmailServer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'server_name' => 'sometimes|string|max:255',
            'server_type_id' => 'sometimes|exists:email_server_types,id',
            'host' => 'sometimes|string|max:255',
            'port' => 'sometimes|integer',
            'encryption' => 'nullable|in:none,ssl,tls,starttls',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,maintenance,failing',
        ];
    }
}
