<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\BaseController;
use App\Services\ApiService\Client\BillingService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class BillingController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected BillingService $billingService
    ) {}

    public function paymentGateways(Request $request)
    {
        addInfoLog("Admin billing payment gateway list request");

        $response = $this->billingService->getPaymentGateways();

        return $this->success('Admin payment gateways fetched successfully.', $response);
    }
}
