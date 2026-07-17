<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\BaseRequest;

class RevenueReportRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'transaction_type' => 'nullable|string|in:subscriptions,one_time,all',
            'status' => 'nullable|string|in:completed,failed,pending,all',
            'platform' => 'nullable|string|max:50',
        ];
    }
}
