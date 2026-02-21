<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\BaseRequest;

class LoginRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('auth/login.validation.email_required'),
            'email.email' => __('auth/login.validation.email_email'),
            'password.required' => __('auth/login.validation.password_required'),
            'password.string' => __('auth/login.validation.password_string'),
            'password.min' => __('auth/login.validation.password_min'),
        ];
    }
}
