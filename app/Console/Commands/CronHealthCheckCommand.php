<?php

namespace App\Console\Commands;

use App\Models\CronJob;
use App\Models\CronJobExecution;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CronHealthCheckCommand extends Command
{
    protected $signature = 'cron:health-check';
    protected $description = 'Check health of cron jobs';

    public function handle()
    {
        $healthy = true;
        
        // Check for jobs that haven't run recently
        $threshold = now()->subHours(2);
        
        $stuckJobs = CronJob::where('is_active', 1)
            ->where(function ($query) use ($threshold) {
                $query->whereNull('last_run_at')
                    ->orWhere('last_run_at', '<', $threshold);
            })
            ->get();

        if ($stuckJobs->isNotEmpty()) {
            $this->warn("Stuck jobs found:");
            foreach ($stuckJobs as $job) {
                $this->line("  - {$job->job_name} (last run: {$job->last_run_at})");
            }
            
            // Send alert
            $this->sendAlert($stuckJobs);
            $healthy = false;
        }

        // Check for failed executions
        $failedExecutions = CronJobExecution::where('status', 'failed')
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
