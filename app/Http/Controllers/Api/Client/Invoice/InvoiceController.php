<?php

namespace App\Http\Controllers\Api\Client\Invoice;

use App\Http\Controllers\Api\Client\BaseController;
use App\Models\BillingConfig;
use App\Models\Invoice;
use App\Services\Billing\BillingManager;
use App\Services\ClientService;
use App\Services\InvoiceService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class InvoiceController extends BaseController
{
    use ApiResponse;
    public function __construct(
        protected InvoiceService $service,
        protected ClientService $clientService,
        protected BillingManager $billingManager
    ) {}

    public function index(Request $request)
    {
        addInfoLog("Invoice list request");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $table = $this->service->query()->getModel()->getTable();
        $clientIdColumn = $this->service->clientId();
        $statusColumn = $this->service->status();
        $paymentStatusColumn = $this->service->paymentStatus();
        $invoiceNumberColumn = $this->service->invoiceNumber();
        $externalInvoiceNumberColumn = $this->service->externalInvoiceNumber();
        $invoiceDateColumn = $this->service->invoiceDate();
        $dueDateColumn = $this->service->dueDate();

        $qualifiedClientIdColumn = $table . '.' . $clientIdColumn;
        $qualifiedStatusColumn = $table . '.' . $statusColumn;

        $query = $this->service->query()
            ->where($qualifiedClientIdColumn, $clientId);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($startDate) {
            $query->whereDate($table . '.' . $invoiceDateColumn, '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate($table . '.' . $invoiceDateColumn, '<=', $endDate);
        }

        $params = $request->all();
        if (isset($params['limit']) && !isset($params['per_page'])) {
            $params['per_page'] = $params['limit'];
        }

        $result = $this->service->datatable(
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
                    $table . '.' . $this->service->totalAmount(),
                    $table . '.' . $this->service->amountDue(),
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

        return $this->success('Invoices fetched successfully.', $result);
    }

    public function show(Request $request, $invoiceId)
    {
        addInfoLog("Invoice show request");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $invoice = $this->service->query()->find($invoiceId);

        if (!$invoice) {
            return $this->error('Invoice not found', 404);
        }

        if ($invoice->client_id !== $clientId) {
            return $this->error('You do not have permission to view this invoice.', 403);
        }

        return $this->success('Invoice fetched successfully.', $invoice);
    }

    public function downloadPdf(Request $request, $invoiceId)
    {
        $invoice = $this->service->query()->find($invoiceId);

        if (!$invoice) {
            return $this->error('Invoice not found', 404);
        }

        if (!$invoice->external_invoice_id) {
            return $this->error('Invoice has not been generated on the billing provider yet.', 400);
        }

        /** @var \App\Models\Client|null $client */
        $client = $this->clientService->query()->find($invoice->client_id);

        if (!$client) {
            return $this->error('Client not found', 404);
        }

        try {
            $pdfContent = $this->billingManager->downloadInvoice($client, $invoice->external_invoice_id);

            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="invoice_' . ($invoice->invoice_number ?? $invoice->id) . '.pdf"',
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to download invoice pdf: " . $e->getMessage());

            return $this->error('Failed to download invoice from billing provider.', 400);
        }
    }
}
