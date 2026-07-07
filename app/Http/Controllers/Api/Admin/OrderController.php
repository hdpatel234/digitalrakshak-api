<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\BaseController;
use App\Services\CandidateOrderService;
use App\Services\PaymentMethodTypeService;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    public function __construct(
        protected CandidateOrderService $candidateOrderService,
        protected PaymentMethodTypeService $paymentMethodTypeService
    ) {}

    public function index(Request $request)
    {
        $orderTable = $this->candidateOrderService->query()->getModel()->getTable();
        $statusColumn = $this->candidateOrderService->status();
        $orderNumberColumn = $this->candidateOrderService->orderNumber();
        $clientOrderNumberColumn = $this->candidateOrderService->clientOrderNumber();
        $invoiceNumberColumn = $this->candidateOrderService->invoiceNumber();
        $paymentStatusColumn = $this->candidateOrderService->paymentStatus();
        $paymentMethodColumn = $this->candidateOrderService->paymentMethod();
        $orderDateColumn = $this->candidateOrderService->orderDate();

        $qualifiedStatusColumn = $orderTable . '.' . $statusColumn;

        $query = $this->candidateOrderService->query()->with('client');

        $params = $request->all();

        if (isset($params['payment_method_id']) && !isset($params['filters']['payment_method_id'])) {
            $params['filters']['payment_method_id'] = $params['payment_method_id'];
        }
        if (isset($params['limit']) && !isset($params['per_page'])) {
            $params['per_page'] = $params['limit'];
        }
        if (isset($params['search']) && !empty($params['search'])) {
             // For simplicity, search functionality is built-in datatable for specific searchable array
        }

        $result = $this->candidateOrderService->datatable(
            query: $query,
            params: $params,
            config: [
                'searchable' => [
                    $orderTable . '.' . $orderNumberColumn,
                    $orderTable . '.' . $clientOrderNumberColumn,
                    $orderTable . '.' . $invoiceNumberColumn,
                ],
                'status_column' => $qualifiedStatusColumn,
                'date_column' => $orderTable . '.' . $this->candidateOrderService->createdAt(),
                'allowed_filters' => [
                    'status' => $qualifiedStatusColumn,
                    'payment_status' => $orderTable . '.' . $paymentStatusColumn,
                    'order_date' => $orderTable . '.' . $orderDateColumn,
                    'payment_method_id' => function ($builder, $value) use ($orderTable, $paymentMethodColumn) {
                        $raw = is_array($value) ? $value : explode(',', (string) $value);
                        $ids = collect($raw)
                            ->map(static fn($id) => (int) $id)
                            ->filter(static fn($id) => $id > 0)
                            ->unique()
                            ->values()
                            ->all();

                        if ($ids === []) {
                            return;
                        }

                        $builder->whereIn($orderTable . '.' . $paymentMethodColumn, $ids);
                    },
                ],
                'allowed_sorts' => [
                    $orderTable . '.' . $this->candidateOrderService->id(),
                    $orderTable . '.' . $orderNumberColumn,
                    $orderTable . '.' . $clientOrderNumberColumn,
                    $orderTable . '.' . $paymentStatusColumn,
                    $orderTable . '.' . $this->candidateOrderService->totalAmount(),
                    $orderTable . '.' . $orderDateColumn,
                    $orderTable . '.' . $this->candidateOrderService->createdAt(),
                ],
                'default_sort_by' => $orderTable . '.' . $this->candidateOrderService->createdAt(),
                'default_sort_direction' => 'desc',
                'default_per_page' => 10,
                'max_per_page' => 100,
            ]
        );

        $statusList = array_map(
            static fn(OrderStatus $status): array => [
                'key' => $status->value,
                'name' => ucwords(str_replace('_', ' ', $status->value)),
            ],
            OrderStatus::cases()
        );

        $paymentMethodRows = $this->paymentMethodTypeService->query()
            ->where($this->paymentMethodTypeService->isActive(), 1)
            ->select([
                $this->paymentMethodTypeService->id(),
                $this->paymentMethodTypeService->methodName(),
                $this->paymentMethodTypeService->methodCode(),
                $this->paymentMethodTypeService->category(),
                $this->paymentMethodTypeService->icon(),
                $this->paymentMethodTypeService->description(),
                $this->paymentMethodTypeService->displayOrder(),
            ])
            ->orderBy($this->paymentMethodTypeService->displayOrder(), 'asc')
            ->get();

        $paymentMethods = $paymentMethodRows
            ->map(function ($method) {
                return [
                    'id' => (int) $method->{$this->paymentMethodTypeService->id()},
                    'method_name' => $method->{$this->paymentMethodTypeService->methodName()},
                    'method_code' => $method->{$this->paymentMethodTypeService->methodCode()},
                    'category' => $method->{$this->paymentMethodTypeService->category()},
                    'icon' => $method->{$this->paymentMethodTypeService->icon()},
                    'description' => $method->{$this->paymentMethodTypeService->description()},
                    'display_order' => (int) ($method->{$this->paymentMethodTypeService->displayOrder()} ?? 0),
                ];
            })
            ->values()
            ->all();

        $paymentMethodNameById = $paymentMethodRows
            ->mapWithKeys(function ($method) {
                return [
                    (int) $method->{$this->paymentMethodTypeService->id()} =>
                    $method->{$this->paymentMethodTypeService->methodName()},
                ];
            });

        if (is_array($result) && isset($result['list']) && is_array($result['list'])) {
            $orderList = collect($result['list'])
                ->map(static fn($item) => is_array($item) ? $item : $item->toArray());

            $result['list'] = $orderList
                ->map(function (array $row) use ($paymentMethodNameById, $paymentMethodColumn) {
                    $methodId = (int) ($row[$paymentMethodColumn] ?? 0);
                    $row['payment_method_name'] = $paymentMethodNameById->get($methodId);
                    
                    if (isset($row['client'])) {
                        $row['client_name'] = $row['client']['company_name'] ?? $row['client']['name'] ?? '';
                    }

                    return $row;
                })
                ->values()
                ->all();
        }

        if (is_array($result)) {
            $result['status_list'] = $statusList;
            $result['payment_methods'] = $paymentMethods;
        } else {
            $result = [
                'list' => $result,
                'status_list' => $statusList,
                'payment_methods' => $paymentMethods,
            ];
        }

        return $this->success('Orders fetched successfully.', $result);
    }
}
