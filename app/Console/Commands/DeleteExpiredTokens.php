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

        $expiredTokenIds = DB::table('oauth_access_tokens')
            ->where('expires_at', '<', $now)
            ->pluck('id')
            ->toArray();

        $revokedTokenIds = DB::table('oauth_access_tokens')
            ->where('revoked', 1)
            ->pluck('id')
            ->toArray();

        $tokenIds = array_unique(array_merge($expiredTokenIds, $revokedTokenIds));

        if (!empty($tokenIds)) {

            DB::table('user_sessions')
                ->whereIn('access_token_id', $tokenIds)
                ->update([
                    'is_active' => false,
                    'updated_at' => now(),
                ]);

            DB::table('oauth_access_tokens')
                ->whereIn('id', $tokenIds)
                ->delete();
        }

        DB::table('oauth_refresh_tokens')
            ->where('expires_at', '<', $now)
            ->delete();

        $this->info('Expired and revoked tokens deleted successfully.');
    }
}