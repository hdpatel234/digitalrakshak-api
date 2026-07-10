<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\BaseController;
use App\Services\InvoiceService as CoreInvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends BaseController
{
    public function __construct(
        protected CoreInvoiceService $invoiceService
    ) {}

    public function index(Request $request)
    {
        $table = $this->invoiceService->query()->getModel()->getTable();
        $statusColumn = $this->invoiceService->status();
        $paymentStatusColumn = $this->invoiceService->paymentStatus();
        $invoiceNumberColumn = $this->invoiceService->invoiceNumber();
        $externalInvoiceNumberColumn = $this->invoiceService->externalInvoiceNumber();
        $invoiceDateColumn = $this->invoiceService->invoiceDate();
        $dueDateColumn = $this->invoiceService->dueDate();

        $qualifiedStatusColumn = $table . '.' . $statusColumn;

        // Eager load client and order to include their details
        $query = $this->invoiceService->query()
            ->with(['client', 'order']);

        $params = $request->all();

        $startDate = $params['start_date'] ?? $params['date_from'] ?? null;
        $endDate = $params['end_date'] ?? $params['date_to'] ?? null;

        if ($startDate) {
            $query->whereDate($table . '.' . $invoiceDateColumn, '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate($table . '.' . $invoiceDateColumn, '<=', $endDate);
        }

        if (isset($params['limit']) && !isset($params['per_page'])) {
            $params['per_page'] = $params['limit'];
        }

        $result = $this->invoiceService->datatable(
            query: $query,
            params: $params,
            config: [
                'searchable' => [
                    $table . '.' . $invoiceNumberColumn,
                    $table . '.' . $externalInvoiceNumberColumn,
                ],
                'status_column' => $qualifiedStatusColumn,
                'date_column' => $table . '.created_at',
                'allowed_filters' => [
                    'status' => $qualifiedStatusColumn,
                    'payment_status' => $table . '.' . $paymentStatusColumn,
                    'invoice_date' => $table . '.' . $invoiceDateColumn,
                    'due_date' => $table . '.' . $dueDateColumn,
                    'client_id' => $table . '.client_id',
                ],
                'allowed_sorts' => [
                    $table . '.id',
                    $table . '.' . $invoiceNumberColumn,
                    $table . '.' . $this->invoiceService->totalAmount(),
                    $table . '.' . $this->invoiceService->amountDue(),
                    $table . '.' . $statusColumn,
                    $table . '.' . $paymentStatusColumn,
                    $table . '.' . $invoiceDateColumn,
                    $table . '.' . $dueDateColumn,
                    $table . '.created_at',
                ],
                'default_sort_by' => $table . '.created_at',
                'default_sort_direction' => 'desc',
                'default_per_page' => 10,
                'max_per_page' => 100,
            ]
        );

        $statusList = [
            ['key' => 'draft', 'name' => 'Draft'],
            ['key' => 'sent', 'name' => 'Sent'],
            ['key' => 'viewed', 'name' => 'Viewed'],
            ['key' => 'paid', 'name' => 'Paid'],
            ['key' => 'overdue', 'name' => 'Overdue'],
            ['key' => 'cancelled', 'name' => 'Cancelled'],
        ];

        // Format result to extract client name for datatable if necessary
        if (is_array($result) && isset($result['list']) && is_array($result['list'])) {
            $invoiceList = collect($result['list'])
                ->map(static fn($item) => is_array($item) ? $item : $item->toArray());

            $result['list'] = $invoiceList
                ->map(function (array $row) {
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
        } else {
            $result = [
                'list' => $result,
                'status_list' => $statusList,
            ];
        }

        return $this->success('Invoices fetched successfully.', $result);
    }
}
