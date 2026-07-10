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

    public function filters()
    {
        $statuses = [
            ['value' => 'all', 'label' => 'All Statuses'],
            ['value' => 'pending', 'label' => 'Pending'],
            ['value' => 'completed', 'label' => 'Completed'],
            ['value' => 'failed', 'label' => 'Failed'],
            ['value' => 'refunded', 'label' => 'Refunded'],
        ];

        $transactionTypes = [
            ['value' => 'all', 'label' => 'All Transactions'],
            ['value' => 'subscriptions', 'label' => 'Subscriptions'],
            ['value' => 'one_time', 'label' => 'One-time Payments'],
            ['value' => 'refunds', 'label' => 'Refunds'],
        ];

        $platforms = \App\Models\PaymentGateway::select('gateway_code as value', 'gateway_name as label')
            ->where('is_active', true)
            ->get();

        $platforms->prepend(['value' => 'all', 'label' => 'All Platforms']);

        return $this->success('Filters fetched successfully.', [
            'statuses' => $statuses,
            'transaction_types' => $transactionTypes,
            'platforms' => $platforms
        ]);
    }
}
