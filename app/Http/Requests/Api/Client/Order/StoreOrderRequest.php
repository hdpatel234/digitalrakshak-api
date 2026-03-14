<?php

namespace App\Http\Requests\Api\Client\Order;

use App\Http\Requests\Api\BaseRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'package_id' => ['required', 'integer', 'min:1', 'exists:packages,id'],
            'candidate_ids' => ['required', 'array', 'min:1'],
            'candidate_ids.*' => ['required', 'integer', 'distinct', 'exists:candidates,id'],
            'save_draft' => ['sometimes', 'boolean'],
            'payment_method_id' => [
                'required',
                'integer',
                'min:1',
                Rule::exists('payment_method_types', 'id')->where(function ($query) {
                    $query->where('is_active', 1);
                }),
            ],
            'payment_provider_id' => [
                'required',
                'integer',
                'min:1',
                Rule::exists('payment_gateway_configs', 'id')->where(function ($query) {
                    $query->where('is_active', 1);
                }),
            ],
        ];
    }
}
