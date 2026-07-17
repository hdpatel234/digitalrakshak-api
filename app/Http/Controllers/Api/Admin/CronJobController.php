<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\Api\Admin\UpdateCronJobRequest;
use App\Services\ApiService\Admin\CronJobService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class CronJobController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected CronJobService $cronJobService
    ) {}

    public function index(): JsonResponse
    {
        addInfoLog("Admin cron job list request");

        $crons = $this->cronJobService->getCronJobs();

        return $this->success('Cron jobs retrieved successfully.', $crons);
    }

    public function update(UpdateCronJobRequest $request, string $id): JsonResponse
    {
        addInfoLog("Admin cron job update request");

        try {
            $cronJob = $this->cronJobService->updateCronJob($id, $request->validated());

            return $this->success('Cron job updated successfully', $cronJob);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return $this->error($e->getMessage() ?: 'Failed to update cron job.', $code, ['error' => $e->getMessage()]);
        }
    }

    public function toggle(string $id): JsonResponse
    {
        addInfoLog("Admin cron job toggle status request");

        try {
            $cronJob = $this->cronJobService->toggleCronJob($id);

            return $this->success('Cron job status toggled successfully', $cronJob);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return $this->error($e->getMessage() ?: 'Failed to toggle cron job.', $code, ['error' => $e->getMessage()]);
        }
    }

    public function run(string $id): JsonResponse
    {
        addInfoLog("Admin cron job run request");

        try {
            $result = $this->cronJobService->runCronJob($id);

            return $this->success('Cron job executed successfully', $result);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return $this->error('Cron job execution failed', $code, ['error' => $e->getMessage()]);
        }
    }
}
