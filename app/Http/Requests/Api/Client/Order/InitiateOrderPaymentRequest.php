<?php

namespace App\Http\Requests\Api\Client\Order;

use App\Http\Requests\Api\BaseRequest;

class InitiateOrderPaymentRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_provider_name' => ['required', 'string'],
            'total_amount' => ['nullable', 'numeric', 'min:1'],
            'total_amount_in_paise' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
