<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\ApiService\Admin\SupportTicketService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    use ApiResponse;

    protected SupportTicketService $service;

    public function __construct(SupportTicketService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        addInfoLog("Admin support ticket list request");

        try {
            $result = $this->service->getTickets($request->all());
            return $this->success('Support tickets fetched successfully.', $result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function show(Request $request, $id)
    {
        addInfoLog("Admin support ticket show request, ID: {$id}");

        try {
            $result = $this->service->getTicket((int) $id);
            return $this->success('Support ticket fetched successfully.', $result);
        } catch (\Exception $e) {
            addErrorLog("Admin support ticket show failed. ID: {$id}, Error: " . $e->getMessage());
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function conversations(Request $request, $id)
    {
        try {
            $result = $this->service->getTicketConversations((int) $id);
            return $this->success('Support ticket conversations fetched successfully.', $result);
        } catch (\Exception $e) {
            addErrorLog("Admin support ticket conversations show failed. ID: {$id}, Error: " . $e->getMessage());
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function reply(Request $request, $id)
    {
        addInfoLog("Admin support ticket reply request");

        $user = $request->user('api') ?? $request->user();
        $message = $request->input('message');

        $attachments = $request->file('attachments') ?? $request->file('attachment') ?? [];

        try {
            $result = $this->service->addTicketReply((int) $id, (string) $message, $user, $attachments);
            return $this->success('Reply added to ticket successfully.', $result);
        } catch (\Exception $e) {
            addErrorLog("Admin support ticket reply failed. Ticket ID: {$id}, Error: " . $e->getMessage());
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
