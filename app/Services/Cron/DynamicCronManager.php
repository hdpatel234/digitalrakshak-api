<?php

namespace App\Services\Cron;

use App\Models\CronJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DynamicCronManager
{
    protected $app;
    protected $runningJobs = [];

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Register all dynamic cron jobs from database
     */
    public function registerDynamicJobs(Schedule $schedule)
    {
        // Get all active cron jobs that are due
        $jobs = CronJob::where('is_active', 1)
            ->where(function ($query) {
                $query->where('next_run_at', '<=', Carbon::now())
                      ->orWhereNull('next_run_at');
            })
            ->get();

        foreach ($jobs as $job) {
            $this->registerJob($schedule, $job);
        }
    }

    /**
     * Register a single cron job
     */
    protected function registerJob(Schedule $schedule, CronJob $job)
    {
        // Build the command
        $command = $this->buildCommand($job);
        
        // Create the scheduled event
        $event = $schedule->command($command);
        
        // Apply schedule frequency
        $this->applySchedule($event, $job);
        
        // Apply constraints
        $this->applyConstraints($event, $job);
        
        // Add callback to update job status
        $event->before(function () use ($job) {
            $this->jobStarted($job);
        });
        
        $event->after(function () use ($job) {
            $this->jobCompleted($job);
        });
        
        $event->onFailure(function () use ($job) {
            $this->jobFailed($job);
        });

        // Store the event for monitoring
        $this->runningJobs[$job->id] = $event;
    }

    /**
     * Build the artisan command
     */
    protected function buildCommand(CronJob $job)
    {
        $command = "cron:run --job={$job->job_key}";
        
        // Add parameters if any
        if ($parameters = $job->parameters) {
            foreach ($parameters as $key => $value) {
                $command .= " --{$key}=" . escapeshellarg($value);
            }
        }
        
        return $command;
    }

    /**
     * Apply schedule frequency
     */
    protected function applySchedule($event, CronJob $job)
    {
        switch ($job->schedule_type) {
            case 'cron':
                if ($job->cron_expression) {
                    $event->cron($job->cron_expression);
                }
                break;
                
            case 'interval':
                if ($job->interval_minutes) {
                    $event->everyMinute();
                    if ($job->interval_minutes > 1) {
                        $event->when(function () use ($job) {
                            return Carbon::now()->minute % $job->interval_minutes == 0;
                        });
                    }
                }
                break;
                
            case 'hourly':
                $event->hourly();
                break;
                
            case 'daily':
                $time = $job->time_of_day ?? '00:00';
                $event->dailyAt($time);
                break;
                
            case 'weekly':
                $dayOfWeek = $job->day_of_week ?? '0';
                $time = $job->time_of_day ?? '00:00';
                $event->weeklyOn($dayOfWeek, $time);
                break;
                
            case 'monthly':
                $dayOfMonth = $job->day_of_month ?? 1;
                $time = $job->time_of_day ?? '00:00';
                $event->monthlyOn($dayOfMonth, $time);
                break;
        }
    }

    /**
     * Apply additional constraints
     */
    protected function applyConstraints($event, CronJob $job)
    {
        // Without overlapping (if not allowed concurrent)
        if (!$job->concurrent_instances) {
            $event->withoutOverlapping();
        }

        // Maximum execution time
        if ($job->max_execution_time) {
            // timeout only exists on command events, which we are using
        }

        // Run on one server (for multi-server environments)
        $event->onOneServer();
    }

    /**
     * Handle job started
     */
    public function jobStarted(CronJob $job)
    {
        // Create execution log
        $execution = $job->executions()->create([
            'started_at' => Carbon::now(),
            'status' => 'running',
            'triggered_by' => 'system',
        ]);

        // Update job last run
        $job->update([
            'last_run_at' => Carbon::now(),
            'last_run_log_id' => $execution->id,
        ]);

        Log::info("Cron job started: {$job->job_name}", [
            'job_id' => $job->id,
            'job_key' => $job->job_key,
            'execution_id' => $execution->id,
        ]);
    }

    /**
     * Handle job completed
     */
    public function jobCompleted(CronJob $job)
    {
        $execution = $job->executions()->latest()->first();
        
        if ($execution) {
            $execution->update([
                'completed_at' => Carbon::now(),
                'status' => 'completed',
                'duration_seconds' => Carbon::parse($execution->started_at)->diffInSeconds(Carbon::now()),
            ]);
        }

        // Calculate next run
        $this->calculateNextRun($job);

        Log::info("Cron job completed: {$job->job_name}", [
            'job_id' => $job->id,
            'job_key' => $job->job_key,
            'duration' => $execution?->duration_seconds ?? 0,
        ]);
    }

    /**
     * Handle job failed
     */
    public function jobFailed(CronJob $job)
    {
        $execution = $job->executions()->latest()->first();
        
        if ($execution) {
            $execution->update([
                'completed_at' => Carbon::now(),
                'status' => 'failed',
                'duration_seconds' => Carbon::parse($execution->started_at)->diffInSeconds(Carbon::now()),
            ]);
        }

        // Schedule retry if needed
        $this->scheduleRetry($job);

        Log::error("Cron job failed: {$job->job_name}", [
            'job_id' => $job->id,
            'job_key' => $job->job_key,
            'execution_id' => $execution?->id,
        ]);
    }

    /**
     * Calculate next run time
     */
    public function calculateNextRun(CronJob $job)
    {
        $nextRun = null;
        
        switch ($job->schedule_type) {
            case 'cron':
                $nextRun = $this->getNextCronRun($job->cron_expression);
                break;
                
            case 'interval':
                $nextRun = Carbon::now()->addMinutes($job->interval_minutes ?? 5);
                break;
                
            case 'hourly':
                $nextRun = Carbon::now()->addHour()->startOfHour();
                break;
                
            case 'daily':
                $time = $job->time_of_day ?? '00:00';
                $nextRun = Carbon::now()->tomorrow()->setTimeFromTimeString($time);
                break;
                
            case 'weekly':
                $dayOfWeek = $job->day_of_week ?? 0;
                $time = $job->time_of_day ?? '00:00';
                $nextRun = Carbon::now()->next($this->getDayName($dayOfWeek))->setTimeFromTimeString($time);
                break;
                
            case 'monthly':
                $dayOfMonth = $job->day_of_month ?? 1;
                $time = $job->time_of_day ?? '00:00';
                $nextRun = Carbon::now()->addMonth()->day($dayOfMonth)->setTimeFromTimeString($time);
                break;
        }

        if ($nextRun) {
            $job->update(['next_run_at' => $nextRun]);
        }
    }

    /**
     * Schedule retry for failed job
     */
    protected function scheduleRetry(CronJob $job)
    {
        $retryCount = $job->executions()
            ->where('status', 'failed')
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->count();

        if ($retryCount < ($job->max_retries ?? 3)) {
            $retryDelay = $job->retry_delay_minutes ?? 5;
            $job->update([
                'next_run_at' => Carbon::now()->addMinutes($retryDelay),
            ]);
        } else {
            // Mark job as inactive if max retries exceeded
            $job->update(['is_active' => 0]);
            
            Log::warning("Cron job disabled due to max retries: {$job->job_name}", [
                'job_id' => $job->id,
                'retry_count' => $retryCount,
            ]);
        }
    }

    /**
     * Get day name from day number (0 = Sunday, 1 = Monday, etc.)
     */
    protected function getDayName($dayNumber)
    {
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        return $days[$dayNumber] ?? 'Monday';
    }

    /**
     * Get next run time for cron expression
     */
    protected function getNextCronRun($cronExpression)
    {
        try {
            $cron = new \Cron\CronExpression($cronExpression);
            return Carbon::instance($cron->getNextRunDate());
        } catch (\Exception $e) {
            Log::error("Invalid cron expression: {$cronExpression}", ['error' => $e->getMessage()]);
            return Carbon::now()->addDay();
        }
    }
}
