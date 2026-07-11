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
            'to_email as recipient',
            'subject',
            'status',
            'priority',
            'created_at as scheduledFor',
            'attempts'
        );

        $logQuery = EmailLog::select(
            'id',
            'to_email as recipient',
            'subject',
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
            // Add prefix to ID to mimic Q-1001
            $item->id = 'Q-' . $item->id;
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
}
