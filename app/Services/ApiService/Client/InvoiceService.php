<?php

namespace App\Services\ApiService\Client;

use App\Repositories\ClientRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\InvoiceItemRepository;
use App\Services\BaseService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class InvoiceService extends BaseService
{
    public function __construct(
        protected InvoiceRepository $invoiceRepo,
        protected ClientRepository $clientRepo,
        protected InvoiceItemRepository $invoiceItemRepo
    ) {}

    public function getInvoices(array $params, int $clientId): array
    {
        $table = $this->invoiceRepo->query()->getModel()->getTable();
        $clientIdColumn = $this->invoiceRepo->clientId();
        $statusColumn = $this->invoiceRepo->status();
        $paymentStatusColumn = $this->invoiceRepo->paymentStatus();
        $invoiceNumberColumn = $this->invoiceRepo->invoiceNumber();
        $externalInvoiceNumberColumn = $this->invoiceRepo->externalInvoiceNumber();
        $invoiceDateColumn = $this->invoiceRepo->invoiceDate();
        $dueDateColumn = $this->invoiceRepo->dueDate();

        $qualifiedClientIdColumn = $table . '.' . $clientIdColumn;
        $qualifiedStatusColumn = $table . '.' . $statusColumn;

        $query = $this->invoiceRepo->query()
            ->with('order')
            ->where($qualifiedClientIdColumn, $clientId);

        $startDate = $params['start_date'] ?? null;
        $endDate = $params['end_date'] ?? null;

        if ($startDate) {
            $query->whereDate($table . '.' . $invoiceDateColumn, '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate($table . '.' . $invoiceDateColumn, '<=', $endDate);
        }

        if (isset($params['limit']) && !isset($params['per_page'])) {
            $params['per_page'] = $params['limit'];
        }

        $result = $this->invoiceRepo->datatable(
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
                ],
                'allowed_sorts' => [
                    $table . '.id',
                    $table . '.' . $invoiceNumberColumn,
                    $table . '.' . $this->invoiceRepo->totalAmount(),
                    $table . '.' . $this->invoiceRepo->amountDue(),
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

        if (is_array($result)) {
            $result['status_list'] = $statusList;
        } else {
            $result = [
                'items' => $result,
                'status_list' => $statusList,
            ];
        }

        return $result;
    }

    public function getInvoice(int $invoiceId, int $clientId)
    {
        $invoice = $this->invoiceRepo->query()->find($invoiceId);

        if (!$invoice) {
            throw new \Exception('Invoice not found', 404);
        }

        if ($invoice->client_id !== $clientId) {
            throw new \Exception('You do not have permission to view this invoice.', 403);
        }

        return $invoice;
    }

    public function downloadInvoicePdf(int $invoiceId, int $clientId): array
    {
        $invoice = $this->invoiceRepo->query()->find($invoiceId);

        if (!$invoice) {
            throw new \Exception('Invoice not found', 404);
        }

        if ($invoice->client_id !== $clientId) {
            throw new \Exception('You do not have permission to download this invoice.', 403);
        }

        $client = $this->clientRepo->query()->find($invoice->client_id);

        if (!$client) {
            throw new \Exception('Client not found', 404);
        }

        if (!$invoice->external_invoice_id) {
            try {
                $items = $this->invoiceItemRepo->query()->where($this->invoiceItemRepo->invoiceId(), $invoice->id)->get();
                $pdf = Pdf::loadView('invoices.client_pdf', [
                    'invoice' => $invoice,
                    'client' => $client,
                    'items' => $items
                ]);

                return [
                    'content' => $pdf->output(),
                    'filename' => 'invoice_' . ($invoice->invoice_number ?? $invoice->id) . '.pdf',
                ];
            } catch (\Throwable $e) {
                Log::error("Failed to generate local invoice pdf: " . $e->getMessage() . "\n" . $e->getTraceAsString());
                throw new \Exception('Failed to generate local invoice.', 500);
            }
        }

        throw new \Exception('Failed to download external invoice because BillingManager is not implemented.', 501);
    }
}
