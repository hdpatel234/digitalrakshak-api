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
            'email.required' => 'auth.login.validation.email_required',
            'email.email' => 'auth.login.validation.email_email',
            'password.required' => 'auth.login.validation.password_required',
            'password.string' => 'auth.login.validation.password_string',
            'password.min' => 'auth.login.validation.password_min',
        ];
    }
}