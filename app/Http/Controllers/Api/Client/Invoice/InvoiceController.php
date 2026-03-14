<?php

namespace App\Http\Controllers\Api\Client\Invoice;

use App\Http\Controllers\Api\Client\BaseController;
use App\Services\InvoiceService;

class InvoiceController extends BaseController
{
    protected InvoiceService $service;
    public function __construct(InvoiceService $service)
    {
        $this->service = $service;
    }
}