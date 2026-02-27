<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteExpiredTokens extends Command
{
    protected $signature = 'passport:delete-expired';
    protected $description = 'Delete expired and revoked Passport tokens';

    public function handle()
    {
        $now = now();

        DB::table('oauth_access_tokens')->where('expires_at', '<', $now)->delete();

        DB::table('oauth_refresh_tokens')->where('expires_at', '<', $now)->delete();

        DB::table('oauth_access_tokens')->where('revoked', 1)->delete();

        $this->info('Expired and revoked tokens deleted successfully.');
    }
}