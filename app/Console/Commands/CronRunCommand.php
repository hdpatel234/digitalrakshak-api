<?php

namespace App\Console\Commands;

use App\Enums\BaseStatus;
use App\Services\CronJobService;
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

    public function __construct(
        protected DynamicCronManager $cronManager,
        protected CronJobService $cronJobService
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $jobKey = $this->option('job');
        $isManual = $this->option('manual');
        $force = $this->option('force');

        if ($jobKey) {
            $this->runSpecificJob($jobKey, $isManual, $force);
        } else {
            $this->runAllJobs();
        }

        return 0;
    }

    protected function runSpecificJob($jobKey, $isManual, $force)
    {
        $job = $this->cronJobService->query()->where($this->cronJobService->jobKey(), $jobKey)->first();

        if (!$job) {
            $this->error("Job not found: {$jobKey}");
            return 1;
        }

        if (!$isManual && !$force && !$this->isJobDue($job)) {
            $this->info("Job not due yet: {$jobKey}");
            return 0;
        }

        $this->info("Running job: {$job->{$this->cronJobService->jobName()}}");
        $this->executeJob($job);
    }

    protected function runAllJobs()
    {
        $jobs = $this->cronJobService->query()->where($this->cronJobService->status(), BaseStatus::ACTIVE)
            ->where(function ($query) {
                $query->where($this->cronJobService->nextRunAt(), '<=', now())
                    ->orWhereNull($this->cronJobService->nextRunAt());
            })
            ->orderBy($this->cronJobService->priority(), 'desc')
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

    protected function executeJob($job)
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
                $this->cronJobService->lastRunAt() => now(),
                $this->cronJobService->lastRunLogId() => $execution->id,
            ]);

            $output = '';

            if ($job->{$this->cronJobService->jobClass()}) {
                // Execute the job class
                $jobClass = $job->{$this->cronJobService->jobClass()};
                $method = $job->{$this->cronJobService->jobMethod()} ?? 'handle';
                $parameters = is_array($job->{$this->cronJobService->parameters()}) ? $job->{$this->cronJobService->parameters()} : json_decode($job->{$this->cronJobService->parameters()} ?? '{}', true);

                if (!class_exists($jobClass)) {
                    throw new \Exception("Job class not found: {$jobClass}");
                }

                $instance = app($jobClass);

                // Execute with parameters
                $result = $instance->$method($parameters);

                $execution->update([
                    'processed_count' => $result[BaseStatus::PROCESSED] ?? 0,
                    'success_count' => $result[BaseStatus::SUCCESS] ?? 0,
                    'failed_count' => $result[BaseStatus::FAILED] ?? 0,
                    'processed_logs' => isset($result['logs']) ? (is_array($result['logs']) ? json_encode($result['logs']) : $result['logs']) : null,
                ]);
            } elseif ($job->{$this->cronJobService->command()}) {
                // Execute native artisan command
                $parameters = [];
                if (is_array($job->{$this->cronJobService->parameters()})) {
                    $parameters = $job->{$this->cronJobService->parameters()};
                }

                Artisan::call($job->{$this->cronJobService->command()}, $parameters);
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
                $this->cronJobService->lastRunStatus() => 'success',
            ]);

            $this->info("✓ Completed: {$job->{$this->cronJobService->jobName()}}");
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

            $this->error("✗ Failed: {$job->{$this->cronJobService->jobName()}} - " . $e->getMessage());
            Log::error("Cron job failed: {$job->{$this->cronJobService->jobName()}}", [
                'job_id' => $job->{$this->cronJobService->id()},
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    protected function isJobDue($job)
    {
        return !$job->{$this->cronJobService->nextRunAt()} || $job->{$this->cronJobService->nextRunAt()} <= now();
    }

    protected function handleRetry($job)
    {
        $retryCount = $job->executions()
            ->where('status', 'failed')
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        if ($retryCount < ($job->{$this->cronJobService->maxRetries()} ?? 3)) {
            $job->update([
                $this->cronJobService->nextRunAt() => now()->addMinutes($job->{$this->cronJobService->retryDelayMinutes()} ?? 5),
                $this->cronJobService->lastRunStatus() => BaseStatus::FAILED,
            ]);
        } else {
            $job->update([$this->cronJobService->status() => BaseStatus::INACTIVE, $this->cronJobService->lastRunStatus() => BaseStatus::FAILED]);
            Log::warning("Cron job disabled: {$job->{$this->cronJobService->jobName()}} (max retries)");
        }
    }
}
