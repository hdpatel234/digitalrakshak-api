<?php

namespace App\Services\ApiService\Admin;

use App\Models\EmailQueue;
use App\Models\EmailLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\EmailQueueService;
use Illuminate\Support\Str;

class SystemEmailQueueService
{
    public function __construct(
        protected EmailQueueService $emailQueueService
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

        $query = EmailQueue::select(
            'id',
            DB::raw("'queue' as source"),
            'to_email as recipient',
            'subject',
            'body_text',
            'body_html',
            'status',
            'priority',
            'created_at as scheduledFor',
            'attempts'
        );

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('to_email', 'like', '%' . $search . '%')
                  ->orWhere('subject', 'like', '%' . $search . '%');
            });
        }
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        if ($priority !== 'all') {
            $query->where('priority', $priority);
        }
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
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
        $pending = EmailQueue::where('status', 'pending')->count();
        $processing = EmailQueue::where('status', 'processing')->count();
        $failed = EmailQueue::where('status', 'failed')->count();
        $sentToday = EmailQueue::where('status', 'sent')->whereDate('sent_at', Carbon::today())->count();

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
        $record = EmailQueue::find($id);
        if (!$record && $source === 'log') {
            // Backward compatibility if they pass a log id, find the queue id
            $log = EmailLog::find($id);
            if ($log) {
                $record = EmailQueue::find($log->email_queue_id);
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
