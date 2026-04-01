<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetAiDailyUsage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:reset-daily-usage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the daily usage for all AI accounts to 0';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to reset AI accounts daily usage...');
        
        $updated = \App\Models\AiAccount::query()->update([
            'daily_usage' => 0
        ]);

        $this->info("Successfully reset daily usage to 0 for {$updated} AI accounts.");
        \Illuminate\Support\Facades\Log::info("ResetAiDailyUsage command executed: Reset {$updated} accounts.");
        
        return self::SUCCESS;
    }
}
