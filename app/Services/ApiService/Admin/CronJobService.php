<?php

namespace App\Services\ApiService\Admin;

use App\Repositories\CronJobRepository;
use Illuminate\Support\Facades\Artisan;

class CronJobService
{
    public function __construct(
        protected CronJobRepository $repo,
    ) {}

    public function getCronJobs()
    {
        return $this->repo->getAllOrderedDesc();
    }

    public function updateCronJob(string $id, array $data)
    {
        return $this->repo->update($id, $data);
    }

    public function toggleCronJob(string $id)
    {
        $cronJob = $this->repo->find($id);
        $cronJob->{$this->repo->isActive()} = !$cronJob->{$this->repo->isActive()};
        $cronJob->save();
        return $cronJob;
    }

    public function runCronJob(string $id)
    {
        $cronJob = $this->repo->find($id);

        try {
            Artisan::call($cronJob->{$this->repo->command()});

            $this->repo->update($id, [
                $this->repo->lastRunAt() => now(),
                $this->repo->status() => 'completed',
            ]);

            return [
                'cron_job' => $this->repo->find($id),
                'output' => Artisan::output()
            ];
        } catch (\Exception $e) {
            $this->repo->update($id, [
                $this->repo->lastRunAt() => now(),
                $this->repo->status() => 'failed',
                $this->repo->errorMessage() => $e->getMessage()
            ]);

            throw $e;
        }
    }
}
