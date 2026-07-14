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
        $isDraft = $this->boolean('save_draft');

        return [
            'id' => ['sometimes', 'integer', 'min:1', 'exists:candidate_orders,id'],
            'package_id' => ['required', 'integer', 'min:1', 'exists:packages,id'],
            'candidate_ids' => [Rule::requiredIf(!$isDraft), 'array', 'min:1'],
            'candidate_ids.*' => ['required_with:candidate_ids', 'integer', 'distinct', 'exists:candidates,id'],
            'save_draft' => ['sometimes', 'boolean'],
            'payment_method_id' => [
                Rule::requiredIf(!$isDraft),
                'integer',
                'min:1',
                Rule::exists('payment_method_types', 'id')->where(function ($query) {
                    $query->where('is_active', 1);
                }),
            ],
            'payment_provider_id' => [
                Rule::requiredIf(!$isDraft),
                'integer',
                'min:1',
                Rule::exists('payment_gateway_configs', 'id')->where(function ($query) {
                    $query->where('status', 'active');
                }),
            ],
        ];
    }
}








