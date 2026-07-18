<?php

namespace App\Console\Commands;

use App\Services\CronJobService;
use App\Services\CronJobExecutionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CronHealthCheckCommand extends Command
{
    protected $signature = 'cron:health-check';
    protected $description = 'Check health of cron jobs';

    public function __construct(
        protected CronJobService $cronJobService,
        protected CronJobExecutionService $cronJobExecutionService
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $healthy = true;
        
        // Check for jobs that haven't run recently
        $threshold = now()->subHours(2);
        
        $stuckJobs = $this->cronJobService->query()->where($this->cronJobService->isActive(), 1)
            ->where(function ($query) use ($threshold) {
                $query->whereNull($this->cronJobService->lastRunAt())
                    ->orWhere($this->cronJobService->lastRunAt(), '<', $threshold);
            })
            ->get();

        if ($stuckJobs->isNotEmpty()) {
            $this->warn("Stuck jobs found:");
            foreach ($stuckJobs as $job) {
                $this->line("  - {$job->{$this->cronJobService->jobName()}} (last run: {$job->{$this->cronJobService->lastRunAt()}})");
            }
            
            // Send alert
            $this->sendAlert($stuckJobs);
            $healthy = false;
        }

        // Check for failed executions
        $failedExecutions = $this->cronJobExecutionService->query()->where($this->cronJobExecutionService->status(), 'failed')
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        if ($failedExecutions > 5) {
            $this->warn("High number of failed executions: {$failedExecutions}");
            $healthy = false;
        }

        if ($healthy) {
            $this->info("Cron jobs are healthy.");
        }

        return $healthy ? 0 : 1;
    }

    protected function sendAlert($jobs)
    {
        // Implement alerting (email, Slack, etc.)
        Log::warning("Cron Health Check Failed: Found " . $jobs->count() . " stuck jobs.");
    }
}
