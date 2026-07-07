<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Services\PaymentRefundService;
use App\Traits\ApiResponse;

class RefundController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected PaymentRefundService $paymentRefundService
    ) {}

    public function index(Request $request)
    {
        $query = $this->paymentRefundService->query();
        $datatable = $this->paymentRefundService->datatable($query, $request->all());

        return $this->success('Refunds list fetched successfully.', $datatable);
    }
}
