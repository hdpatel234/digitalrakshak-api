<?php

namespace App\Console\Commands;

use App\Models\CronJob;
use App\Services\Cron\DynamicCronManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class CronRunCommand extends Command
{
    protected $signature = 'cron:run 
                            {--job= : Specific job key to run}
                            {--manual : Indicates manual execution}
                            {--force : Force execution even if not scheduled}';
    
    protected $description = 'Run scheduled cron jobs';

    protected DynamicCronManager $cronManager;

    public function __construct(DynamicCronManager $cronManager)
    {
        parent::__construct();
        $this->cronManager = $cronManager;
    }

    public function handle()
    {
        $jobKey = $this->option('job');
        $isManual = $this->option('manual');
        $force = $this->option('force');

        if ($jobKey) {
            // Run specific job
            $this->runSpecificJob($jobKey, $isManual, $force);
        } else {
            // Run all due jobs
            $this->runAllJobs();
        }

        return 0;
    }

    protected function runSpecificJob($jobKey, $isManual, $force)
    {
        $job = CronJob::where('job_key', $jobKey)->first();

        if (!$job) {
            $this->error("Job not found: {$jobKey}");
            return 1;
        }

        if (!$isManual && !$force && !$this->isJobDue($job)) {
            $this->info("Job not due yet: {$jobKey}");
            return 0;
        }

        $this->info("Running job: {$job->job_name}");
        $this->executeJob($job);
    }

    protected function runAllJobs()
    {
        $jobs = CronJob::where('is_active', 1)
            ->where(function ($query) {
                $query->where('next_run_at', '<=', now())
                      ->orWhereNull('next_run_at');
            })
            ->orderBy('priority', 'desc')
            ->get();

        if ($jobs->isEmpty()) {
            $this->info('No jobs to run');
            return;
        }

        $this->info("Running " . $jobs->count() . " scheduled jobs");

        foreach ($jobs as $job) {
            $this->executeJob($job);
        }
    }

    protected function executeJob(CronJob $job)
    {
        try {
            // Create execution log
            $execution = $job->executions()->create([
                'started_at' => now(),
                'status' => 'running',
                'triggered_by' => $this->option('manual') ? 'manual' : 'system'
            ]);

            // Update job
            $job->update([
                'last_run_at' => now(),
                'last_run_log_id' => $execution->id,
            ]);

            $output = '';

            if ($job->job_class) {
                // Execute the job class
                $jobClass = $job->job_class;
                $method = $job->job_method ?? 'handle';
                $parameters = is_array($job->parameters) ? $job->parameters : json_decode($job->parameters ?? '{}', true);

                if (!class_exists($jobClass)) {
                    throw new \Exception("Job class not found: {$jobClass}");
                }

                $instance = app($jobClass);
                
                // Execute with parameters
                $result = $instance->$method($parameters);

                $execution->update([
                    'processed_count' => $result['processed'] ?? 0,
                    'success_count' => $result['success'] ?? 0,
                    'failed_count' => $result['failed'] ?? 0,
                    'processed_logs' => isset($result['logs']) ? (is_array($result['logs']) ? json_encode($result['logs']) : $result['logs']) : null,
                ]);

            } elseif ($job->command) {
                // Execute native artisan command
                $parameters = [];
                if (is_array($job->parameters)) {
                    $parameters = $job->parameters;
                }
                
                Artisan::call($job->command, $parameters);
                $output = Artisan::output();
            } else {
                throw new \Exception("No job class or artisan command defined for this job.");
            }

            // Update execution
            $execution->update([
                'completed_at' => now(),
                'status' => 'completed',
                'duration_seconds' => Carbon::parse($execution->started_at)->diffInSeconds(now()),
                'output' => $output
            ]);

            $this->cronManager->calculateNextRun($job);

            $job->update([
                'last_run_status' => 'success',
            ]);

            $this->info("✓ Completed: {$job->job_name}");

        } catch (\Exception $e) {
            if (isset($execution)) {
                // Update execution with error
                $execution->update([
                    'completed_at' => now(),
                    'status' => 'failed',
                    'duration_seconds' => Carbon::parse($execution->started_at)->diffInSeconds(now()),
                    'error_message' => $e->getMessage(),
                    'error_stack' => $e->getTraceAsString(),
                ]);
            }

            // Handle retry
            $this->handleRetry($job);

            $this->error("✗ Failed: {$job->job_name} - " . $e->getMessage());
            Log::error("Cron job failed: {$job->job_name}", [
                'job_id' => $job->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    protected function isJobDue(CronJob $job)
    {
        return !$job->next_run_at || $job->next_run_at <= now();
    }

    protected function handleRetry(CronJob $job)
    {
        $retryCount = $job->executions()
            ->where('status', 'failed')
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        if ($retryCount < ($job->max_retries ?? 3)) {
            $job->update([
                'next_run_at' => now()->addMinutes($job->retry_delay_minutes ?? 5),
                'last_run_status' => 'failed',
            ]);
        } else {
            $job->update(['is_active' => 0, 'last_run_status' => 'failed']);
            Log::warning("Cron job disabled: {$job->job_name} (max retries)");
        }
    }
}
