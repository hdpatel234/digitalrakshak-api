<?php

namespace App\Http\Requests\Api\Client\Order;

use App\Http\Requests\Api\BaseRequest;

class CompleteOrderPaymentRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider' => ['required', 'string'],
            'transaction_uuid' => ['required', 'string'],
            'payment_id' => ['required', 'string'],
            'order_id' => ['required', 'string'],
            'signature' => ['nullable', 'string'],
            'gateway_data' => ['nullable', 'array'],
        ];
    }
}
