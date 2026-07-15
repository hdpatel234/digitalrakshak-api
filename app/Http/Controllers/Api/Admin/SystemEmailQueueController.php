<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailQueue;
use App\Models\EmailLog;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SystemEmailQueueController extends Controller
{
    use ApiResponse;

    /**
     * Fetch unified list of email queue and logs based on filters.
     */
    public function store(Request $request, \App\Services\EmailQueueService $emailQueueService)
    {
        $validated = $request->validate([
            'to_email' => 'required|email',
            'template_id' => 'required|exists:email_templates,id',
            'variables' => 'nullable|array',
            'subject' => 'nullable|string|max:255',
            'body_html' => 'nullable|string',
        ]);

        $validated['email_uid'] = 'email_' . (string) \Illuminate\Support\Str::uuid();
        $validated['status'] = 'pending';
        $validated['attempts'] = 0;

        $queue = $emailQueueService->create($validated);

        return $this->success('Email queued successfully', $queue);
    }

    /**
     * Fetch unified list of email queue and logs based on filters.
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $search = $request->input('search', '');
        $status = $request->input('status', 'all');
        $priority = $request->input('priority', 'all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

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

        return $this->success('Email queue fetched successfully', $paginated);
    }

    /**
     * Fetch KPI stats.
     */
    public function stats()
    {
        $pending = EmailQueue::where('status', 'pending')->count();
        $processing = EmailQueue::where('status', 'processing')->count();
        $failed = EmailQueue::where('status', 'failed')->count();
        $sentToday = EmailQueue::where('status', 'sent')->whereDate('sent_at', Carbon::today())->count();

        return $this->success('Queue stats fetched successfully', [
            'pending' => $pending,
            'processing' => $processing,
            'failed' => $failed,
            'sentToday' => $sentToday,
        ]);
    }

    /**
     * Retry a failed email by recreating it in the queue.
     */
    public function retry(string $source, int $id)
    {
        if ($source === 'queue' || $source === 'log') {
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
                return $this->error('Email record not found in queue', 404);
            }

            // Create a new record in queue
            $newRecord = $record->replicate();
            $newRecord->email_uid = 'email_' . (string) \Illuminate\Support\Str::uuid();
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
        } else {
            return $this->error('Invalid source provided', 400);
        }

        return $this->success('Email queued for retry successfully');
    }
}
