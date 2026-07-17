<?php

namespace App\Services\ApiService\Admin;

use App\Models\CronJob;
use Illuminate\Support\Facades\Artisan;

class CronJobService
{
    public function getCronJobs()
    {
        return CronJob::orderBy('id', 'desc')->get();
    }

    public function updateCronJob(string $id, array $data)
    {
        $cronJob = CronJob::findOrFail($id);
        $cronJob->update($data);
        return $cronJob;
    }

    public function toggleCronJob(string $id)
    {
        $cronJob = CronJob::findOrFail($id);
        $cronJob->is_active = !$cronJob->is_active;
        $cronJob->save();
        return $cronJob;
    }

    public function runCronJob(string $id)
    {
        $cronJob = CronJob::findOrFail($id);

        try {
            Artisan::call($cronJob->command);

            $cronJob->update([
                'last_run_at' => now(),
                'status' => 'completed',
            ]);

            return [
                'cron_job' => $cronJob,
                'output' => Artisan::output()
            ];
        } catch (\Exception $e) {
            $cronJob->update([
                'last_run_at' => now(),
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}
