<?php

namespace App\Http\Controllers\Api\Client\Invoice;

use App\Http\Controllers\Api\Client\BaseController;
use App\Services\ApiService\Client\InvoiceService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class InvoiceController extends BaseController
{
    use ApiResponse;

    public function __construct(protected InvoiceService $invoiceService) {}

    public function index(Request $request)
    {
        addInfoLog("Client invoice list request");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $result = $this->invoiceService->getInvoices($request->all(), $clientId);

        return $this->success('Invoices fetched successfully.', $result);
    }

    public function show(Request $request, $invoiceId)
    {
        addInfoLog("Client invoice show request, ID: {$invoiceId}");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $invoice = $this->invoiceService->getInvoice((int) $invoiceId, $clientId);

            return $this->success('Invoice fetched successfully.', $invoice);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function downloadPdf(Request $request, $invoiceId)
    {
        addInfoLog("Client invoice download pdf request, ID: {$invoiceId}");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $result = $this->invoiceService->downloadInvoicePdf((int) $invoiceId, $clientId);

            return response($result['content'], 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $result['filename'] . '"',
            ]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
