<?php

namespace App\Http\Requests\Api\Admin\SystemEmailServer;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'server_name' => 'required|string|max:255',
            'server_type_id' => 'required|exists:email_server_types,id',
            'default_from_name' => 'nullable|string|max:255',
            'default_from_email' => 'nullable|email|max:255',
            'dynamic_values' => 'nullable|array',
            'status' => 'nullable|in:active,inactive,maintenance,failing',
        ];
    }
}
