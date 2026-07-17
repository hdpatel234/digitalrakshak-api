<?php

namespace App\Services\ApiService\Admin;

use App\Models\EmailLog;
use Carbon\Carbon;

class SystemEmailLogService
{
    public function getLogs(array $data)
    {
        $limit = $data['limit'] ?? 10;
        $search = $data['search'] ?? '';
        $status = $data['status'] ?? 'all';

        $query = EmailLog::query()
            ->join('email_queue', 'email_logs.email_queue_id', '=', 'email_queue.id')
            ->select('email_logs.*', 'email_queue.to_email', 'email_queue.subject', 'email_queue.email_uid', 'email_queue.sent_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('email_queue.to_email', 'like', "%{$search}%")
                  ->orWhere('email_queue.subject', 'like', "%{$search}%")
                  ->orWhere('email_queue.email_uid', 'like', "%{$search}%");
            });
        }
        
        if ($status !== 'all' && !empty($status)) {
            $query->where('email_logs.status', $status);
        }

        $paginated = $query->orderBy('email_logs.created_at', 'desc')->paginate($limit);

        // Format for frontend
        $paginated->getCollection()->transform(function ($item) {
            $item->recipient = $item->to_email;
            $item->sentAt = $item->sent_at ? Carbon::parse($item->sent_at)->format('Y-m-d H:i A') : null;
            $item->status = ucfirst(strtolower($item->status));
            $item->template = $item->metadata['template_name'] ?? 'N/A';
            $item->body_text = $item->metadata['body_text'] ?? '';
            $item->body_html = $item->metadata['body_html'] ?? '';
            return $item;
        });

        return $paginated;
    }

    public function getStats()
    {
        $total = EmailLog::count();
        $delivered = EmailLog::whereIn('status', ['delivered', 'opened', 'sent'])->count();
        $bounced = EmailLog::where('status', 'bounced')->count();
        $failed = EmailLog::where('status', 'failed')->count();

        return [
            'total' => $total,
            'delivered' => $delivered,
            'bounced' => $bounced,
            'failed' => $failed,
        ];
    }

    public function getStatuses()
    {
        return EmailLog::select('status')
            ->distinct()
            ->whereNotNull('status')
            ->pluck('status')
            ->map(function ($status) {
                return ucfirst(strtolower($status));
            })
            ->unique()
            ->values();
    }

    public function showLog($id)
    {
        $log = EmailLog::join('email_queue', 'email_logs.email_queue_id', '=', 'email_queue.id')
            ->select('email_logs.*', 'email_queue.to_email', 'email_queue.subject', 'email_queue.email_uid', 'email_queue.sent_at')
            ->find($id);

        if (!$log) {
            throw new \Exception('Email log not found', 404);
        }

        $log->recipient = $log->to_email;
        $log->sentAt = $log->sent_at ? Carbon::parse($log->sent_at)->format('Y-m-d H:i A') : null;
        $log->status = ucfirst(strtolower($log->status));
        $log->template = $log->metadata['template_name'] ?? 'N/A';

        return $log;
    }
}
