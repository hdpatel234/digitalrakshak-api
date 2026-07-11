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
    public function index(Request $request)
    {
        $limit = $request->get('limit', 10);
        $search = $request->get('search', '');
        $status = $request->get('status', 'all');
        $priority = $request->get('priority', 'all');

        $queueQuery = EmailQueue::select(
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

        $logQuery = EmailLog::select(
            'id',
            DB::raw("'log' as source"),
            'to_email as recipient',
            'subject',
            DB::raw("'' as body_text"),
            DB::raw("'' as body_html"),
            'status',
            DB::raw("'Normal' as priority"), // EmailLog might not have priority
            'sent_at as scheduledFor',
            DB::raw("1 as attempts") // EmailLog doesn't store attempts usually
        );

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Apply filters to queue
        if ($search) {
            $queueQuery->where(function ($q) use ($search) {
                $q->where('to_email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }
        if ($status !== 'all') {
            $queueQuery->where('status', $status);
        }
        if ($priority !== 'all') {
            $queueQuery->where('priority', $priority);
        }
        if ($startDate && $endDate) {
            $queueQuery->whereBetween('created_at', [
                Carbon::parse($startDate)->setTimezone(config('app.timezone')),
                Carbon::parse($endDate)->setTimezone(config('app.timezone'))
            ]);
        }

        // Apply filters to logs
        if ($search) {
            $logQuery->where(function ($q) use ($search) {
                $q->where('to_email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }
        if ($status !== 'all') {
            $logQuery->where('status', $status);
        }
        // Logs don't typically have priority, but we ignore the priority filter for logs if it's set to 'all'
        if ($priority !== 'all') {
             $logQuery->whereRaw("1 = 0"); // If filtering by priority and it's not all, exclude logs if they don't have it
        }
        if ($startDate && $endDate) {
            $logQuery->whereBetween('sent_at', [
                Carbon::parse($startDate)->setTimezone(config('app.timezone')),
                Carbon::parse($endDate)->setTimezone(config('app.timezone'))
            ]);
        }

        // Union or execute separately depending on status
        // Since EmailQueue has 'pending', 'processing', 'failed', 'sent' (maybe), 
        // and EmailLog has 'sent', 'failed' etc.
        // It's safer to UNION them for a unified view.
        
        $unifiedQuery = $queueQuery->unionAll($logQuery);
        
        $sql = $unifiedQuery->toSql();
        $query = DB::table(DB::raw("($sql) as unified_emails"))
            ->setBindings($unifiedQuery->getBindings());

        $paginated = $query->orderBy('scheduledFor', 'desc')->paginate($limit);

        // Format dates
        $paginated->getCollection()->transform(function ($item) {
            $item->scheduledFor = Carbon::parse($item->scheduledFor)->format('Y-m-d H:i A');
            // Capitalize status and priority
            $item->status = ucfirst(strtolower($item->status));
            $item->priority = ucfirst(strtolower($item->priority));
            // Add prefix to ID for display
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
        $failedQueue = EmailQueue::where('status', 'failed')->count();
        $failedLogs = EmailLog::where('status', 'failed')->count();
        
        $failed = $failedQueue + $failedLogs;
        
        $sentTodayQueue = EmailQueue::where('status', 'sent')->whereDate('sent_at', Carbon::today())->count();
        $sentTodayLogs = EmailLog::where('status', 'sent')->whereDate('sent_at', Carbon::today())->count();

        $sentToday = $sentTodayQueue + $sentTodayLogs;

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
    public function retry($source, $id)
    {
        if ($source === 'queue') {
            $record = EmailQueue::find($id);
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

        } else if ($source === 'log') {
            $record = EmailLog::find($id);
            if (!$record) {
                return $this->error('Email log not found', 404);
            }

            // Create a new record in queue based on log
            $newRecord = new EmailQueue();
            $newRecord->email_uid = 'email_' . (string) \Illuminate\Support\Str::uuid();
            $newRecord->to_email = $record->to_email;
            $newRecord->subject = $record->subject;
            // Logs don't have body saved natively in this schema unless we have it
            $newRecord->body_html = '';
            $newRecord->body_text = '';
            // Or if metadata has it, we could extract it, but let's leave empty for now
            $newRecord->priority = 'normal'; // default
            $newRecord->status = 'pending';
            $newRecord->attempts = 1;
            $newRecord->error_message = null;
            $newRecord->provider_response = null;
            $newRecord->created_at = Carbon::now();
            $newRecord->updated_at = Carbon::now();
            $newRecord->save();

            // Mark the old log as retried
            $record->status = 'retried';
            $record->save();
        } else {
            return $this->error('Invalid source provided', 400);
        }

        return $this->success('Email queued for retry successfully');
    }
}
