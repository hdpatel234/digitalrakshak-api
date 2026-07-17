<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Services\ApiService\Admin\TransactionService;
use App\Traits\ApiResponse;

class TransactionController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected TransactionService $transactionService
    ) {}

    public function index(Request $request)
    {
        addInfoLog("Admin transaction list request");

        $datatable = $this->transactionService->getTransactions($request->all());

        return $this->success('Transactions list fetched successfully.', $datatable);
    }

    public function filters()
    {
        addInfoLog("Admin transaction filters request");

        $filters = $this->transactionService->getFilters();

        return $this->success('Filters fetched successfully.', $filters);
    }
}
