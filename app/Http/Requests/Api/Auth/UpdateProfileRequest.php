<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('remove_logo')) {
            return;
        }

        $value = $this->input('remove_logo');
        if (!is_string($value)) {
            return;
        }

        $normalized = strtolower(trim($value));
        if ($normalized === 'true' || $normalized === '1') {
            $this->merge(['remove_logo' => true]);
            return;
        }

        if ($normalized === 'false' || $normalized === '0') {
            $this->merge(['remove_logo' => false]);
        }
    }

    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone_code' => 'nullable|string|max:10',
            'phone' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'phone')->ignore($userId),
            ],
            'avatar' => 'nullable|file|image|max:5120',
            'remove_logo' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.string' => __('auth.update_profile.validation.first_name_string'),
            'first_name.max' => __('auth.update_profile.validation.first_name_max'),
            'last_name.string' => __('auth.update_profile.validation.last_name_string'),
            'last_name.max' => __('auth.update_profile.validation.last_name_max'),
            'email.email' => __('auth.update_profile.validation.email_email'),
            'email.unique' => __('auth.update_profile.validation.email_unique'),
            'phone_code.string' => __('auth.update_profile.validation.phone_code_string'),
            'phone_code.max' => __('auth.update_profile.validation.phone_code_max'),
            'phone.string' => __('auth.update_profile.validation.phone_string'),
            'phone.max' => __('auth.update_profile.validation.phone_max'),
            'phone.unique' => __('auth.update_profile.validation.phone_unique'),
            'avatar.file' => __('auth.update_profile.validation.avatar_file'),
            'avatar.image' => __('auth.update_profile.validation.avatar_image'),
            'avatar.max' => __('auth.update_profile.validation.avatar_max'),
            'remove_logo.boolean' => __('auth.update_profile.validation.remove_logo_boolean'),
        ];
    }
}
