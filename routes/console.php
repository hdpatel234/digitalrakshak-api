<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedules

// Delete expired tokens
Schedule::command('passport:delete-expired')->dailyAt('02:00');

// Process candidate imports
Schedule::command('candidates:process-imports')->everyMinute();

// Process email queue
Schedule::command('emails:process-queue --limit=100')->everyMinute();
