<?php

namespace App\Http\Controllers\Api\Client\Order;

use App\Http\Controllers\Api\Client\BaseController;
use App\Http\Requests\Api\Client\Order\CompleteOrderPaymentRequest;
use App\Http\Requests\Api\Client\Order\InitiateOrderPaymentRequest;
use App\Http\Requests\Api\Client\Order\StoreOrderRequest;
use App\Services\ApiService\Client\OrderService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected OrderService $orderService
    ) {}

    public function index(Request $request)
    {
        addInfoLog("Order list request");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $result = $this->orderService->getOrders($request->all(), $clientId);

        return $this->success('Orders fetched successfully.', $result);
    }

    public function store(StoreOrderRequest $request)
    {
        addInfoLog("Order store request");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $result = $this->orderService->createOrder($request->validated(), $clientId, $user);

            return $this->success('Order created/updated successfully.', $result, 201);
        } catch (\Exception $e) {
            Log::error('Order creation/update failed', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function show(Request $request, int $order)
    {
        addInfoLog("Order show request");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $result = $this->orderService->getOrder((int) $order, $clientId);

            if (!empty($result['candidates'])) {
                $candidateIds = array_filter(array_map(function ($item) {
                    return $item['candidate_id'] ?? null;
                }, $result['candidates']));

                if (!empty($candidateIds)) {
                    $candidatesMap = \App\Models\Candidate::whereIn('id', $candidateIds)
                        ->get()
                        ->keyBy('id');

                    foreach ($result['candidates'] as &$candidateItem) {
                        $candidateId = $candidateItem['candidate_id'] ?? null;
                        $candidateDetails = $candidateId && isset($candidatesMap[$candidateId])
                            ? $candidatesMap[$candidateId]->toArray()
                            : null;

                        $candidateItem['candaite_details'] = $candidateDetails;
                        $candidateItem['candidate_details'] = $candidateDetails;
                    }
                } else {
                    foreach ($result['candidates'] as &$candidateItem) {
                        $candidateItem['candaite_details'] = null;
                        $candidateItem['candidate_details'] = null;
                    }
                }
            }

            return $this->success('Order fetched successfully.', $result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function initiatePayment(InitiateOrderPaymentRequest $request, $order)
    {
        addInfoLog("Initiate payment for order $order request");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);
        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $response = $this->orderService->initiateOrderPayment(
                (int) $order,
                $request->validated(),
                $clientId,
                $user,
                $request->ip(),
                $request->userAgent()
            );

            return $this->success('Payment initiated successfully.', $response);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function completePayment(CompleteOrderPaymentRequest $request, int $order)
    {
        addInfoLog("Complete payment for order $order request");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);
        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $result = $this->orderService->completeOrderPayment(
                (int) $order,
                $request->validated(),
                $clientId,
                $user
            );

            return $this->success('Payment completed successfully.', $result);
        } catch (\Exception $e) {
            Log::error('Payment completion failed', [
                'error' => $e->getMessage(),
                'order' => $order,
            ]);

            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
