<?php

namespace App\Services\ApiService\Admin;

use App\Models\EmailQueue;
use App\Models\EmailLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Repositories\EmailQueueRepository;
use App\Repositories\EmailLogRepository;
use Illuminate\Support\Str;

class SystemEmailQueueService
{
    public function __construct(
        protected EmailQueueService $emailQueueService,
        protected EmailQueueRepository $queueRepo,
        protected EmailLogRepository $logRepo
    ) {}

    public function queueEmail(array $data)
    {
        $data['email_uid'] = 'email_' . (string) Str::uuid();
        $data['status'] = 'pending';
        $data['attempts'] = 0;

        return $this->emailQueueService->create($data);
    }

    public function getQueue(array $data)
    {
        $limit = $data['limit'] ?? 10;
        $search = $data['search'] ?? '';
        $status = $data['status'] ?? 'all';
        $priority = $data['priority'] ?? 'all';
        $startDate = $data['start_date'] ?? null;
        $endDate = $data['end_date'] ?? null;

        $query = $this->queueRepo->query()->select(
            $this->queueRepo->id(),
            DB::raw("'queue' as source"),
            $this->queueRepo->toEmail() . ' as recipient',
            $this->queueRepo->subject(),
            $this->queueRepo->bodyText(),
            $this->queueRepo->bodyHtml(),
            $this->queueRepo->status(),
            $this->queueRepo->priority(),
            $this->queueRepo->createdAt() . ' as scheduledFor',
            $this->queueRepo->attempts()
        );

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where($this->queueRepo->toEmail(), 'like', '%' . $search . '%')
                  ->orWhere($this->queueRepo->subject(), 'like', '%' . $search . '%');
            });
        }
        if ($status !== 'all') {
            $query->where($this->queueRepo->status(), $status);
        }
        if ($priority !== 'all') {
            $query->where($this->queueRepo->priority(), $priority);
        }
        if ($startDate && $endDate) {
            $query->whereBetween($this->queueRepo->createdAt(), [
                Carbon::parse($startDate)->setTimezone(config('app.timezone')),
                Carbon::parse($endDate)->setTimezone(config('app.timezone'))
            ]);
        }

        $paginated = $query->orderBy('scheduledFor', 'desc')->paginate($limit);

        // Format dates
        $paginated->getCollection()->transform(function ($item) {
            $item->scheduledFor = Carbon::parse($item->scheduledFor)->format('Y-m-d H:i A');
            $item->status = ucfirst(strtolower($item->status));
            $item->priority = ucfirst(strtolower($item->priority));
            $item->display_id = 'Q-' . $item->id;
            return $item;
        });

        return $paginated;
    }

    public function getStats()
    {
        $pending = $this->queueRepo->query()->where($this->queueRepo->status(), 'pending')->count();
        $processing = $this->queueRepo->query()->where($this->queueRepo->status(), 'processing')->count();
        $failed = $this->queueRepo->query()->where($this->queueRepo->status(), 'failed')->count();
        $sentToday = $this->queueRepo->query()->where($this->queueRepo->status(), 'sent')->whereDate($this->queueRepo->sentAt(), Carbon::today())->count();

        return [
            'pending' => $pending,
            'processing' => $processing,
            'failed' => $failed,
            'sentToday' => $sentToday,
        ];
    }

    public function retryEmail(string $source, int $id)
    {
        if ($source !== 'queue' && $source !== 'log') {
            throw new \Exception('Invalid source provided', 400);
        }

        // Since queue contains everything now, source doesn't strictly matter
        $record = $this->queueRepo->find($id);
        if (!$record && $source === 'log') {
            // Backward compatibility if they pass a log id, find the queue id
            $log = $this->logRepo->find($id);
            if ($log) {
                $record = $this->queueRepo->find($log->{$this->logRepo->emailQueueId()});
            }
        }
        if (!$record) {
            throw new \Exception('Email record not found in queue', 404);
        }

        // Create a new record in queue
        $newRecord = $record->replicate();
        $newRecord->email_uid = 'email_' . (string) Str::uuid();
        $newRecord->status = 'pending';
        $newRecord->attempts = $record->attempts + 1;
        $newRecord->error_message = null;
        $newRecord->provider_response = null;
        $newRecord->last_attempt_at = null;
        $newRecord->sent_at = null;
        $newRecord->created_at = Carbon::now();
        $newRecord->updated_at = Carbon::now();
        $newRecord->save();

        // Mark the old one as retried
        $record->status = 'retried';
        $record->save();

        return true;
    }
}
