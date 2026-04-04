<?php

namespace App\Http\Controllers\Api\Client\Support;

use App\Http\Controllers\Api\Client\BaseController;
use App\Services\ApiService\Client\SupportTicketService;
use App\Traits\ApiResponse;
use App\Http\Requests\Api\Client\Support\StoreSupportTicketRequest;
use Illuminate\Http\Request;

class SupportTicketController extends BaseController
{
    use ApiResponse;

    protected SupportTicketService $service;

    public function __construct(SupportTicketService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        addInfoLog("Support ticket list request");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $result = $this->service->getTickets($request->all(), $clientId);
            return $this->success('Support tickets fetched successfully.', $result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function store(StoreSupportTicketRequest $request)
    {
        addInfoLog("Support ticket store request");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $result = $this->service->createTicket($request->validated(), $clientId, $user);

            return $this->success('Support ticket created successfully.', $result, 201);
        } catch (\Exception $e) {
            addErrorLog("Client support ticket store failed. Error: " . $e->getMessage());
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function departments(Request $request)
    {
        addInfoLog("Support ticket departments request");
        $departments = $this->service->getDepartments();
        return $this->success("Departments fetched successfully", $departments);
    }

    public function priorities(Request $request)
    {
        addInfoLog("Support ticket priorities request");
        $priorities = $this->service->getPriorities();
        return $this->success("Priorities fetched successfully", $priorities);
    }
}
