<?php

namespace App\Services\ApiService\Admin;

use App\Services\PaymentTransactionService;
use App\Enums\PaymentStatus;
use App\Enums\TransactionType;
use App\Models\PaymentGateway;

use App\Repositories\PaymentGatewayRepository;

class TransactionService
{
    public function __construct(
        protected PaymentTransactionService $paymentTransactionService,
        protected PaymentGatewayRepository $paymentGatewayRepo
    ) {}

    public function getTransactions(array $params)
    {
        $query = $this->paymentTransactionService->query()
            ->with(['client', 'order', 'invoice', 'gatewayConfig', 'methodType']);

        $table = $this->paymentTransactionService->query()->getModel()->getTable();
        $statusColumn = $this->paymentTransactionService->status();

        if (isset($params['limit']) && !isset($params['per_page'])) {
            $params['per_page'] = $params['limit'];
        }

        return $this->paymentTransactionService->datatable($query, $params, [
            'status_column' => $table . '.' . $statusColumn,
            'date_column' => $table . '.created_at',
            'allowed_filters' => [
                'status' => $table . '.' . $statusColumn,
                'client_id' => $table . '.client_id',
            ],
            'default_sort_by' => $table . '.created_at',
            'default_sort_direction' => 'desc',
        ]);
    }

    public function getFilters()
    {
        $statuses = [
            ['value' => 'all', 'label' => 'All Statuses'],
            ...array_map(fn($status) => ['value' => $status->value, 'label' => $status->label()], PaymentStatus::cases())
        ];

        $transactionTypes = [
            ['value' => 'all', 'label' => 'All Transactions'],
            ...array_map(fn($type) => ['value' => $type->value, 'label' => $type->label()], TransactionType::cases())
        ];

        $platforms = $this->paymentGatewayRepo->query()->select($this->paymentGatewayRepo->gatewayCode() . ' as value', $this->paymentGatewayRepo->gatewayName() . ' as label')->get()->toArray();
        array_unshift($platforms, ['value' => 'all', 'label' => 'All Platforms']);

        return [
            'statuses' => $statuses,
            'transaction_types' => $transactionTypes,
            'platforms' => $platforms
        ];
    }
}
