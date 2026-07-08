<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:clients,email,' . ($this->route('client')->id ?? $this->route('client')), 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'phone_code' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:active,inactive,suspended'],
            'address' => ['nullable', 'string'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'state_id' => ['nullable', 'integer', 'exists:states,id'],
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'pincode' => ['nullable', 'string', 'max:20'],
            'gst_number' => ['nullable', 'string', 'max:50'],
            'pan_number' => ['nullable', 'string', 'max:50'],
            'currency' => ['nullable', 'string', 'max:10'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
            'payment_terms' => ['nullable', 'string', 'max:255'],
        ];
    }
}
