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

    public function paymentMethods(Request $request)
    {
        addInfoLog("Admin billing payment methods list request");

        $methods = \App\Models\PaymentMethodType::where('is_active', 1)
            ->orderBy('display_order', 'asc')
            ->get(['method_code as value', 'method_name as label']);

        return $this->success('Admin payment methods fetched successfully.', $methods);
    }
}
