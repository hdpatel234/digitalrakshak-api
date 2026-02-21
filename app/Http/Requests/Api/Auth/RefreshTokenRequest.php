<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\BaseRequest;

class RefreshTokenRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'refresh_token' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'refresh_token.required' => __('auth/refresh_token.validation.refresh_token_required'),
            'refresh_token.string' => __('auth/refresh_token.validation.refresh_token_string'),
        ];
    }
}
