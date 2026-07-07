<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Services\PaymentTransactionService;
use App\Traits\ApiResponse;

class TransactionController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected PaymentTransactionService $paymentTransactionService
    ) {}

    public function index(Request $request)
    {
        $query = $this->paymentTransactionService->query()
            ->with(['client', 'order', 'invoice', 'gatewayConfig', 'methodType']);

        $datatable = $this->paymentTransactionService->datatable($query, $request->all());

        return $this->success('Transactions list fetched successfully.', $datatable);
    }
}
