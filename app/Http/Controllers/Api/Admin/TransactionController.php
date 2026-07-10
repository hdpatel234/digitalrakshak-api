<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Services\PaymentTransactionService;
use App\Traits\ApiResponse;
use App\Enums\PaymentStatus;
use App\Enums\TransactionType;

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

    public function filters()
    {
        $statuses = [
            ['value' => 'all', 'label' => 'All Statuses'],
            ...array_map(fn($status) => ['value' => $status->value, 'label' => $status->label()], PaymentStatus::cases())
        ];

        $transactionTypes = [
            ['value' => 'all', 'label' => 'All Transactions'],
            ...array_map(fn($type) => ['value' => $type->value, 'label' => $type->label()], TransactionType::cases())
        ];

        $platforms = \App\Models\PaymentGateway::select('gateway_code as value', 'gateway_name as label')->get()->toArray();

        array_unshift($platforms, ['value' => 'all', 'label' => 'All Platforms']);

        return $this->success('Filters fetched successfully.', [
            'statuses' => $statuses,
            'transaction_types' => $transactionTypes,
            'platforms' => $platforms
        ]);
    }
}
