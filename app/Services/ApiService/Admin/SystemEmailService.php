<?php

namespace App\Services\ApiService\Admin;

use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\EmailServer;
use App\Models\EmailQueue;

class SystemEmailService
{
    public function getOverview()
    {
        $stats = [
            'total_sent' => EmailLog::whereIn('status', ['sent', 'delivered'])->count(),
            'total_templates' => EmailTemplate::count(),
            'total_servers' => EmailServer::count(),
            'total_queued' => EmailQueue::where('status', 'pending')->count(),
        ];

        $recent_logs = EmailLog::join('email_queue', 'email_logs.email_queue_id', '=', 'email_queue.id')
            ->select('email_logs.*', 'email_queue.to_email', 'email_queue.subject')
            ->latest('email_logs.created_at')
            ->take(5)
            ->get()
            ->map(function($log) {
            return [
                'id' => $log->id,
                'recipient_name' => '', // Using empty string as there's no name field
                'recipient_email' => $log->to_email,
                'subject' => $log->subject,
                'status' => $log->status,
                'created_at' => $log->created_at,
            ];
        });

        return [
            'stats' => $stats,
            'recent_logs' => $recent_logs,
        ];
    }

    public function getTemplates(array $data)
    {
        $limit = $data['limit'] ?? 10;
        $query = EmailTemplate::query();

        if (isset($data['search']) && $data['search']) {
            $search = $data['search'];
            $query->where(function($q) use ($search) {
                $q->where('template_name', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        if (isset($data['type']) && $data['type'] && $data['type'] !== 'all') {
            $query->where('email_type', $data['type']);
        }

        if (isset($data['status']) && $data['status'] !== 'all' && $data['status'] !== null) {
            $status = in_array($data['status'], ['Active', '1', 1, true, 'true', 'active']) ? 1 : 0;
            $query->where('is_active', $status);
        }

        $templates = $query->paginate($limit);

        $stats = [
            'total' => EmailTemplate::count(),
            'active' => EmailTemplate::where('is_active', 1)->count(),
            'inactive' => EmailTemplate::where('is_active', 0)->count(),
        ];

        return [
            'templates' => $templates,
            'stats' => $stats
        ];
    }

    public function storeTemplate(array $data)
    {
        return EmailTemplate::create($data);
    }

    public function updateTemplate(int $id, array $data)
    {
        $template = EmailTemplate::findOrFail($id);
        $template->update($data);
        return $template;
    }
}
