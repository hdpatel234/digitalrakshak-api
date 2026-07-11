<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailLog;
use App\Traits\ApiResponse;
use Carbon\Carbon;

class SystemEmailLogController extends Controller
{
    use ApiResponse;

    /**
     * Fetch paginated email logs based on filters.
     */
    public function index(Request $request)
    {
        $limit = $request->get('limit', 10);
        $search = $request->get('search', '');
        $status = $request->get('status', 'all');

        $query = EmailLog::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('to_email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('email_uid', 'like', "%{$search}%");
            });
        }
        
        if ($status !== 'all' && !empty($status)) {
            $query->where('status', $status);
        }

        $paginated = $query->orderBy('sent_at', 'desc')->paginate($limit);

        // Format for frontend
        $paginated->getCollection()->transform(function ($item) {
            $item->recipient = $item->to_email;
            $item->sentAt = $item->sent_at ? Carbon::parse($item->sent_at)->format('Y-m-d H:i A') : null;
            $item->status = ucfirst(strtolower($item->status));
            $item->template = $item->metadata['template_name'] ?? 'N/A';
            return $item;
        });

        return $this->success('Email logs fetched successfully', $paginated);
    }

    /**
     * Fetch KPI stats for email logs.
     */
    public function stats()
    {
        $total = EmailLog::count();
        $delivered = EmailLog::whereIn('status', ['delivered', 'opened', 'sent'])->count();
        $bounced = EmailLog::where('status', 'bounced')->count();
        $failed = EmailLog::where('status', 'failed')->count();

        return $this->success('Log stats fetched successfully', [
            'total' => $total,
            'delivered' => $delivered,
            'bounced' => $bounced,
            'failed' => $failed,
        ]);
    }

    /**
     * Fetch unique email log statuses.
     */
    public function statuses()
    {
        $statuses = EmailLog::select('status')
            ->distinct()
            ->whereNotNull('status')
            ->pluck('status')
            ->map(function ($status) {
                return ucfirst(strtolower($status));
            })
            ->unique()
            ->values();

        return $this->success('Log statuses fetched successfully', $statuses);
    }

    /**
     * Show details of a specific email log.
     */
    public function show($id)
    {
        $log = EmailLog::find($id);

        if (!$log) {
            return $this->error('Email log not found', 404);
        }

        $log->recipient = $log->to_email;
        $log->sentAt = $log->sent_at ? Carbon::parse($log->sent_at)->format('Y-m-d H:i A') : null;
        $log->status = ucfirst(strtolower($log->status));
        $log->template = $log->metadata['template_name'] ?? 'N/A';

        return $this->success('Email log details fetched successfully', $log);
    }
}
