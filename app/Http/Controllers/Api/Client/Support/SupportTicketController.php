<?php

namespace App\Http\Controllers\Api\Client\Support;

use App\Http\Controllers\Api\Client\BaseController;
use App\Services\ApiService\Client\SupportTicketService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class SupportTicketController extends BaseController
{
    use ApiResponse;

    protected SupportTicketService $service;

    public function __construct(SupportTicketService $service)
    {
        $this->service = $service;
    }

    public function store(Request $request)
    {
        addInfoLog("Support ticket store request");
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
