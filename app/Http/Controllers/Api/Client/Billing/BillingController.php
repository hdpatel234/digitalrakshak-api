<?php

namespace App\Http\Controllers\Api\Client\Billing;

use App\Http\Controllers\Api\Client\BaseController;
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
        addInfoLog("Billing payment gateway list request");

        $response = $this->billingService->getPaymentGateways();

        return $this->success('Client payment gateways fetched successfully.', $response);
    }

    public function paymentMethods(Request $request)
    {
        addInfoLog("Billing payment method list request");

        $response = $this->billingService->getPaymentMethods();

        return $this->success('Client payment methods fetched successfully.', $response);
    }

    public function paymentGatewaysByMethod(Request $request, $payment_method)
    {
        addInfoLog("Billing payment gateway by method request");

        $methodTypeId = (int) $payment_method;

        if ($methodTypeId <= 0) {
            return $this->error('Invalid payment method.', 422);
        }

        $response = $this->billingService->getPaymentGatewaysByMethod($methodTypeId);

        return $this->success('Client payment gateways fetched successfully.', $response);
    }

    public function transactions(Request $request)
    {
        addInfoLog("Transaction list request");

        $datatable = $this->billingService->getTransactions($request->all());

        return $this->success('Transactions list fetched successfully.', $datatable);
    }
}
