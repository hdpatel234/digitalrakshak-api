<?php

namespace App\Console\Commands;

use App\Enums\BaseStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\UserSessionService;

class DeleteExpiredTokens extends Command
{
    protected $signature = 'passport:delete-expired';
    protected $description = 'Delete expired and revoked Passport tokens';

    public function __construct(protected UserSessionService $userSessionService)
    {
        parent::__construct();
    }

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

            $this->userSessionService->query()
                ->whereIn($this->userSessionService->accessTokenId(), $tokenIds)
                ->update([
                    $this->userSessionService->status() => BaseStatus::INACTIVE,
                    $this->userSessionService->updatedAt() => now(),
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
