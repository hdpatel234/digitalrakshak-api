<?php

namespace App\Http\Controllers\Api\Admin;

use App\Services\ApiService\Admin\OrderService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected OrderService $orderService
    ) {}

    public function index(Request $request): JsonResponse
    {
        addInfoLog("Admin order list request");

        $result = $this->orderService->getOrders($request->all());

        return $this->success('Orders fetched successfully.', $result);
    }

    public function filters(): JsonResponse
    {
        addInfoLog("Admin order filters request");

        $filters = $this->orderService->getFilters();

        return $this->success('Filters fetched successfully.', $filters);
    }
}
