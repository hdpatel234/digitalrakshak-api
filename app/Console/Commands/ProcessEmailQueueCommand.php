<?php

namespace App\Console\Commands;

use App\Enums\EmailPriority;
use App\Enums\EmailQueueStatus;
use App\Jobs\ProcessEmailJob;
use App\Models\EmailQueue;
use Illuminate\Console\Command;

class ProcessEmailQueueCommand extends Command
{
    protected $signature = 'emails:process-queue {--limit=50}';
    protected $description = 'Dispatch pending emails by priority for processing';

    public function handle(): int
    {
        $limit = max(1, (int) $this->option('limit'));

        // Use parameterized raw query to prevent SQL injection
        // Bind enum values instead of string concatenation
        $priorityCase = "CASE " . EmailQueue::PRIORITY .
            " WHEN ? THEN 1" .
            " WHEN ? THEN 2" .
            " WHEN ? THEN 3" .
            " WHEN ? THEN 4" .
            " ELSE 5 END";

        $priorityValues = [
            EmailPriority::CRITICAL->value,
            EmailPriority::HIGH->value,
            EmailPriority::NORMAL->value,
            EmailPriority::LOW->value,
        ];

        $pendingEmails = EmailQueue::query()
            ->where(EmailQueue::STATUS, EmailQueueStatus::PENDING->value)
            ->where(function ($query) {
                $query->whereNull(EmailQueue::SCHEDULED_AT)
                    ->orWhere(EmailQueue::SCHEDULED_AT, '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull(EmailQueue::EXPIRES_AT)
                    ->orWhere(EmailQueue::EXPIRES_AT, '>', now());
            })
            ->whereRaw(
                EmailQueue::ATTEMPTS . ' < COALESCE(' . EmailQueue::MAX_ATTEMPTS . ', ?)',
                [3]
            )
            ->orderByRaw($priorityCase, $priorityValues)
            ->orderBy(EmailQueue::ID)
            ->limit($limit)
            ->get();

        foreach ($pendingEmails as $pendingEmail) {
            ProcessEmailJob::dispatch((int) $pendingEmail->id);
        }

        $this->info(sprintf('Dispatched %d email(s) for processing.', $pendingEmails->count()));

        return self::SUCCESS;
    }
}
