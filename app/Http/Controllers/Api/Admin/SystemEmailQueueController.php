<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\Api\Admin\StoreSystemEmailQueueRequest;
use App\Services\ApiService\Admin\SystemEmailQueueService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class SystemEmailQueueController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected SystemEmailQueueService $systemEmailQueueService
    ) {}

    /**
     * Store a new email in the queue.
     */
    public function store(StoreSystemEmailQueueRequest $request)
    {
        addInfoLog("Admin system email queue store request");

        $queue = $this->systemEmailQueueService->queueEmail($request->validated());

        return $this->success('Email queued successfully', $queue);
    }

    /**
     * Fetch unified list of email queue and logs based on filters.
     */
    public function index(Request $request)
    {
        addInfoLog("Admin system email queue list request");

        $paginated = $this->systemEmailQueueService->getQueue($request->all());

        return $this->success('Email queue fetched successfully', $paginated);
    }

    /**
     * Fetch KPI stats.
     */
    public function stats()
    {
        addInfoLog("Admin system email queue stats request");

        $stats = $this->systemEmailQueueService->getStats();

        return $this->success('Queue stats fetched successfully', $stats);
    }

    /**
     * Retry a failed email by recreating it in the queue.
     */
    public function retry(string $source, int $id)
    {
        addInfoLog("Admin system email queue retry request, Source: {$source}, ID: {$id}");

        try {
            $this->systemEmailQueueService->retryEmail($source, $id);
            return $this->success('Email queued for retry successfully');
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return $this->error($e->getMessage(), $code);
        }
    }
}
