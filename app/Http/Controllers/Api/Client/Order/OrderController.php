<?php

namespace App\Http\Controllers\Api\Client\Order;

use App\Http\Controllers\Api\Client\BaseController;
use App\Services\CandidateOrderService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends BaseController
{
    use ApiResponse;
    protected CandidateOrderService $service;

    public function __construct(CandidateOrderService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        //
    }
    public function store(Request $request)
    {
        Log::info("Order create request", $request->all());
    }
    public function show(CandidateOrderService $service)
    {
        //
    }
    public function update(Request $request, $id)
    {
        //
    }
    public function destroy(Request $request, $id)
    {
        //
    }
}