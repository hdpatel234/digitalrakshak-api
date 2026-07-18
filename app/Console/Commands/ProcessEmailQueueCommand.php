<?php

namespace App\Console\Commands;

use App\Enums\EmailPriority;
use App\Enums\EmailQueueStatus;
use App\Jobs\ProcessEmailJob;
use App\Services\EmailQueueService;
use Illuminate\Console\Command;

class ProcessEmailQueueCommand extends Command
{
    protected $signature = 'emails:process-queue {--limit=50}';
    protected $description = 'Dispatch pending emails by priority for processing';

    public function __construct(protected EmailQueueService $emailQueueService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $limit = max(1, (int) $this->option('limit')) ?? 100;

        // Use parameterized raw query to prevent SQL injection
        // Bind enum values instead of string concatenation
        $priorityCase = "CASE " . $this->emailQueueService->priority() .
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

        $pendingEmails = $this->emailQueueService->query()
            ->where($this->emailQueueService->status(), EmailQueueStatus::PENDING->value)
            ->where(function ($query) {
                $query->whereNull($this->emailQueueService->scheduledAt())
                    ->orWhere($this->emailQueueService->scheduledAt(), '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull($this->emailQueueService->expiresAt())
                    ->orWhere($this->emailQueueService->expiresAt(), '>', now());
            })
            ->whereRaw(
                $this->emailQueueService->attempts() . ' < COALESCE(' . $this->emailQueueService->maxAttempts() . ', ?)',
                [3]
            )
            ->orderByRaw($priorityCase, $priorityValues)
            ->orderBy($this->emailQueueService->id())
            ->limit($limit)
            ->get();

        foreach ($pendingEmails as $pendingEmail) {
            ProcessEmailJob::dispatch((int) $pendingEmail->{$this->emailQueueService->id()});
        }

        $this->info(sprintf('Dispatched %d email(s) for processing.', $pendingEmails->count()));

        return self::SUCCESS;
    }
}
