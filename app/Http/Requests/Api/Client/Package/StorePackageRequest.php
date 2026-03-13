<?php

namespace App\Http\Requests\Api\Client\Package;

use App\Http\Requests\Api\BaseRequest;

class StorePackageRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'package_name' => ['required', 'string', 'max:255'],
            'service_ids' => ['required', 'array', 'min:1'],
            'service_ids.*' => ['required', 'integer', 'distinct', 'exists:services,id'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }
}
