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

        $table = $this->paymentTransactionService->query()->getModel()->getTable();
        $statusColumn = $this->paymentTransactionService->status();

        $params = $request->all();
        if (isset($params['limit']) && !isset($params['per_page'])) {
            $params['per_page'] = $params['limit'];
        }

        $datatable = $this->paymentTransactionService->datatable($query, $params, [
            'status_column' => $table . '.' . $statusColumn,
            'date_column' => $table . '.created_at',
            'allowed_filters' => [
                'status' => $table . '.' . $statusColumn,
                'client_id' => $table . '.client_id',
            ],
            'default_sort_by' => $table . '.created_at',
            'default_sort_direction' => 'desc',
        ]);

        return $this->success('Transactions list fetched successfully.', $datatable);
    }
}
