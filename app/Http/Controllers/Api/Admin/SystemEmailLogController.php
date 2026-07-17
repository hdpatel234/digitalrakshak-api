<?php

namespace App\Http\Controllers\Api\Admin;

use App\Services\ApiService\Admin\SystemEmailLogService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class SystemEmailLogController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected SystemEmailLogService $systemEmailLogService
    ) {}

    /**
     * Fetch paginated email logs based on filters.
     */
    public function index(Request $request)
    {
        addInfoLog("Admin system email log list request");

        $paginated = $this->systemEmailLogService->getLogs($request->all());

        return $this->success('Email logs fetched successfully', $paginated);
    }

    /**
     * Fetch KPI stats for email logs.
     */
    public function stats()
    {
        addInfoLog("Admin system email log stats request");

        $stats = $this->systemEmailLogService->getStats();

        return $this->success('Log stats fetched successfully', $stats);
    }

    /**
     * Fetch unique email log statuses.
     */
    public function statuses()
    {
        addInfoLog("Admin system email log statuses request");

        $statuses = $this->systemEmailLogService->getStatuses();

        return $this->success('Log statuses fetched successfully', $statuses);
    }

    /**
     * Show details of a specific email log.
     */
    public function show($id)
    {
        addInfoLog("Admin system email log show request, ID: {$id}");

        try {
            $log = $this->systemEmailLogService->showLog($id);
            return $this->success('Email log details fetched successfully', $log);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return $this->error($e->getMessage(), $code);
        }
    }
}
