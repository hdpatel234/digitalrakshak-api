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

    public function __construct(protected SupportTicketService $supportTicketService) {}

    public function index(Request $request)
    {
        addInfoLog("Client support ticket list request");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $result = $this->supportTicketService->getTickets($request->all(), $clientId);
            return $this->success('Support tickets fetched successfully.', $result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function show(Request $request, $id)
    {
        addInfoLog("Client support ticket show request, ID: {$id}");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $result = $this->supportTicketService->getTicket((int) $id, $clientId);
            return $this->success('Support ticket fetched successfully.', $result);
        } catch (\Exception $e) {
            addErrorLog("Client support ticket show failed. ID: {$id}, Error: " . $e->getMessage());
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function conversations(Request $request, $id)
    {
        addInfoLog("Client support ticket conversations request, ID: {$id}");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $result = $this->supportTicketService->getTicketConversations((int) $id, $clientId);
            return $this->success('Support ticket conversations fetched successfully.', $result);
        } catch (\Exception $e) {
            addErrorLog("Client support ticket conversations show failed. ID: {$id}, Error: " . $e->getMessage());
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function store(StoreSupportTicketRequest $request)
    {
        addInfoLog("Client support ticket create request");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $result = $this->supportTicketService->createTicket($request->validated(), $clientId, $user);

            return $this->success('Support ticket created successfully.', $result, 201);
        } catch (\Exception $e) {
            addErrorLog("Client support ticket store failed. Error: " . $e->getMessage());
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function reply(Request $request)
    {
        addInfoLog("Client support ticket reply request");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $ticketId = $request->input('ticket_id');
        $message = $request->input('message');

        $attachments = $request->file('attachments') ?? $request->file('attachment') ?? [];

        try {
            $result = $this->supportTicketService->addTicketReply((int) $ticketId, (string) $message, $clientId, $user, $attachments);
            return $this->success('Reply added to ticket successfully.', $result);
        } catch (\Exception $e) {
            addErrorLog("Client support ticket reply failed. Ticket ID: {$ticketId}, Error: " . $e->getMessage());
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function departments(Request $request)
    {
        addInfoLog("Client support ticket departments request");
        $departments = $this->supportTicketService->getDepartments();
        return $this->success("Departments fetched successfully", $departments);
    }

    public function priorities(Request $request)
    {
        addInfoLog("Client support ticket priorities request");
        $priorities = $this->supportTicketService->getPriorities();
        return $this->success("Priorities fetched successfully", $priorities);
    }
}
