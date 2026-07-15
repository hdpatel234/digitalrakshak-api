<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CronJobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $crons = [
            [
                'job_name' => 'Delete expired tokens',
                'job_key' => 'passport_delete_expired',
                'command' => 'passport:delete-expired',
                'schedule_type' => 'daily',
                'time_of_day' => '02:00',
                'cron_expression' => '0 2 * * *',
                'is_active' => true,
            ],
            [
                'job_name' => 'Process candidate imports',
                'job_key' => 'candidates_process_imports',
                'command' => 'candidates:process-imports',
                'schedule_type' => 'interval',
                'interval_minutes' => 1,
                'cron_expression' => '* * * * *',
                'is_active' => true,
            ],
            [
                'job_name' => 'Process order verifications',
                'job_key' => 'orders_process_verifications',
                'command' => 'orders:process-verifications',
                'schedule_type' => 'interval',
                'interval_minutes' => 1,
                'cron_expression' => '* * * * *',
                'is_active' => true,
            ],
            [
                'job_name' => 'Process email queue',
                'job_key' => 'emails_process_queue',
                'command' => 'emails:process-queue',
                'parameters' => ['limit' => '100'],
                'schedule_type' => 'interval',
                'interval_minutes' => 1,
                'cron_expression' => '* * * * *',
                'is_active' => true,
            ],
            [
                'job_name' => 'Sync Country API data',
                'job_key' => 'csc_sync_countries',
                'command' => 'csc:sync',
                'parameters' => ['countries' => true],
                'schedule_type' => 'weekly',
                'time_of_day' => '00:00',
                'day_of_week' => 0,
                'cron_expression' => '0 0 * * 0',
                'is_active' => true,
            ],
            [
                'job_name' => 'Sync State API data',
                'job_key' => 'csc_sync_states',
                'command' => 'csc:sync',
                'parameters' => ['states' => true],
                'schedule_type' => 'weekly',
                'time_of_day' => '00:00',
                'day_of_week' => 0,
                'cron_expression' => '0 0 * * 0',
                'is_active' => true,
            ],
            [
                'job_name' => 'Sync City API data',
                'job_key' => 'csc_sync_cities',
                'command' => 'csc:sync',
                'parameters' => ['cities' => true],
                'schedule_type' => 'daily',
                'time_of_day' => '03:00',
                'cron_expression' => '0 3 * * *',
                'is_active' => true,
            ],
            [
                'job_name' => 'Process candidate services',
                'job_key' => 'app_process_candidate_services',
                'command' => 'app:process-candidate-services',
                'schedule_type' => 'interval',
                'interval_minutes' => 5,
                'cron_expression' => '*/5 * * * *',
                'is_active' => true,
            ]
        ];

        foreach ($crons as $cron) {
            \App\Models\CronJob::updateOrCreate(
                ['job_name' => $cron['job_name']],
                $cron
            );
        }
    }
}
