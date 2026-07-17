<?php

namespace App\Services\ApiService\Admin;

use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\EmailServer;
use App\Models\EmailQueue;

use App\Repositories\EmailLogRepository;
use App\Repositories\EmailTemplateRepository;
use App\Repositories\EmailServerRepository;
use App\Repositories\EmailQueueRepository;

class SystemEmailService
{
    public function __construct(
        protected EmailLogRepository $logRepo,
        protected EmailTemplateRepository $templateRepo,
        protected EmailServerRepository $serverRepo,
        protected EmailQueueRepository $queueRepo
    ) {}
    public function getOverview()
    {
        $stats = [
            'total_sent' => $this->logRepo->query()->whereIn($this->logRepo->status(), ['sent', 'delivered'])->count(),
            'total_templates' => $this->templateRepo->count(),
            'total_servers' => $this->serverRepo->count(),
            'total_queued' => $this->queueRepo->query()->where($this->queueRepo->status(), 'pending')->count(),
        ];

        $recent_logs = $this->logRepo->query()->join('email_queue', 'email_logs.email_queue_id', '=', 'email_queue.id')
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
        $query = $this->templateRepo->query();

        if (isset($data['search']) && $data['search']) {
            $search = $data['search'];
            $query->where(function($q) use ($search) {
                $q->where($this->templateRepo->templateName(), 'like', "%{$search}%")
                  ->orWhere($this->templateRepo->subject(), 'like', "%{$search}%");
            });
        }

        if (isset($data['type']) && $data['type'] && $data['type'] !== 'all') {
            $query->where($this->templateRepo->emailType(), $data['type']);
        }

        if (isset($data['status']) && $data['status'] !== 'all' && $data['status'] !== null) {
            $status = in_array($data['status'], ['Active', '1', 1, true, 'true', 'active']) ? 1 : 0;
            $query->where($this->templateRepo->isActive(), $status);
        }

        $templates = $query->paginate($limit);

        $stats = [
            'total' => $this->templateRepo->count(),
            'active' => $this->templateRepo->query()->where($this->templateRepo->isActive(), 1)->count(),
            'inactive' => $this->templateRepo->query()->where($this->templateRepo->isActive(), 0)->count(),
        ];

        return [
            'templates' => $templates,
            'stats' => $stats
        ];
    }

    public function storeTemplate(array $data)
    {
        return $this->templateRepo->create($data);
    }

    public function updateTemplate(int $id, array $data)
    {
        return $this->templateRepo->update($id, $data);
    }
}
