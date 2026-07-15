<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Dynamic jobs from database are now handled by App\Providers\CronServiceProvider

// Static jobs that must always run
Schedule::command('cron:run')->everyMinute();
Schedule::command('queue:work --stop-when-empty')->everyFiveMinutes();

// Health check
Schedule::command('cron:health-check')->hourly();
