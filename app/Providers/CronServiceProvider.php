<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CronServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(\Illuminate\Console\Scheduling\Schedule::class);
            $cronManager = $this->app->make(\App\Services\Cron\DynamicCronManager::class);
            
            // Register dynamic cron jobs from database
            try {
                $cronManager->registerDynamicJobs($schedule);
            } catch (\Exception $e) {
                // Ignore errors during migration or initial setup
            }
        });
    }

    public function register(): void
    {
        $this->app->singleton(\App\Services\Cron\DynamicCronManager::class, function ($app) {
            return new \App\Services\Cron\DynamicCronManager($app);
        });
    }
}
