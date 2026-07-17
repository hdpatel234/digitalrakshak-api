<?php

namespace App\Http\Controllers\Api\Admin;

use App\Services\ApiService\Admin\InvoiceService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InvoiceController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected InvoiceService $invoiceService
    ) {}

    public function index(Request $request): JsonResponse
    {
        addInfoLog("Admin invoice list request");

        $result = $this->invoiceService->getInvoices($request->all());

        return $this->success('Invoices fetched successfully.', $result);
    }
}
