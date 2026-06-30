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

// Process order verifications
Schedule::command('orders:process-verifications')->everyMinute();

// Process email queue
Schedule::command('emails:process-queue --limit=100')->everyMinute();

// Reset AI accounts daily usage
Schedule::command('ai:reset-daily-usage')->dailyAt('01:00')->timezone(config('app.timezone', 'Asia/Kolkata'));

// Sync CountryStateCity API data
Schedule::command('csc:sync countries')->weekly();
Schedule::command('csc:sync states')->weekly();
Schedule::command('csc:sync cities')->dailyAt('03:00');
