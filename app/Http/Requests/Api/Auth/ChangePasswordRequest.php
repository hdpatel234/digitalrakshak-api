<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\BaseRequest;

class ChangePasswordRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|different:current_password',
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => __('auth.change_password.validation.current_password_required'),
            'current_password.string' => __('auth.change_password.validation.current_password_string'),
            'new_password.required' => __('auth.change_password.validation.new_password_required'),
            'new_password.string' => __('auth.change_password.validation.new_password_string'),
            'new_password.min' => __('auth.change_password.validation.new_password_min'),
            'new_password.different' => __('auth.change_password.validation.new_password_different'),
        ];
    }
}
