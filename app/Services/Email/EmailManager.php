<?php

namespace App\Services\Email;

use App\Enums\EmailPriority;
use App\Enums\EmailQueueStatus;
use App\Enums\EmailServerStatus;
use App\Jobs\ProcessEmailJob;
use App\Models\EmailServer;
use App\Models\EmailRoutingRule;
use App\Models\EmailQueue;
use App\Models\EmailTemplate;
use App\Models\Client;
use App\Models\EmailBounce;
use App\Services\Email\TemplateVariableRenderer;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class EmailManager
{
    protected $routingRules;
    protected $defaultServer;

    public function __construct()
    {
        $this->loadRoutingRules();
    }

    /**
     * Load active routing rules
     */
    protected function loadRoutingRules()
    {
        $this->routingRules = EmailRoutingRule::with(['server', 'failoverServer'])
            ->where('is_active', true)
            ->orderBy('rule_priority', 'desc')
            ->get();
    }

    /**
     * Send an email using appropriate server based on routing rules
     */
    public function send(array $data): EmailQueue
    {
        // Validate required fields
        if (!isset($data['to_email']) || !isset($data['subject']) || (!isset($data['body_html']) && !isset($data['body_text']))) {
            throw new \InvalidArgumentException('Missing required email fields');
        }

        // Generate unique ID
        $emailUid = 'email_' . Str::random(32);

        // Determine email type
        $emailType = $data['email_type'] ?? 'system_notification';

        // Find matching routing rule
        $routingRule = $this->findRoutingRule($emailType, $data);

        // Select server based on rule
        $server = $this->selectServer($routingRule, $data);

        // Check bounce list
        if ($this->isBounced($data['to_email'])) {
            throw new \Exception("Email {$data['to_email']} is in bounce list");
        }

        // Create email queue entry
        $emailQueue = EmailQueue::create([
            'email_uid' => $emailUid,
            'to_email' => $data['to_email'],
            'to_name' => $data['to_name'] ?? null,
            'cc' => $data['cc'] ?? null,
            'bcc' => $data['bcc'] ?? null,
            'reply_to' => $data['reply_to'] ?? null,
            'subject' => $data['subject'],
            'body_html' => $data['body_html'] ?? null,
            'body_text' => $data['body_text'] ?? null,
            'template_id' => $data['template_id'] ?? null,
            'email_type' => $emailType,
            'priority' => $data['priority'] ?? EmailPriority::NORMAL->value,
            'client_id' => $data['client_id'] ?? null,
            'candidate_id' => $data['candidate_id'] ?? null,
            'order_id' => $data['order_id'] ?? null,
            'user_id' => $data['user_id'] ?? null,
            'assigned_server_id' => $server?->id,
            'routing_rule_id' => $routingRule?->id,
            'status' => EmailQueueStatus::PENDING->value,
            'scheduled_at' => $data['scheduled_at'] ?? now(),
            'expires_at' => $data['expires_at'] ?? now()->addDays(7),
            'created_by' => $data['created_by'] ?? auth()->id()
        ]);

        // Handle attachments if any
        if (isset($data['attachments']) && is_array($data['attachments'])) {
            $this->attachFiles($emailQueue, $data['attachments']);
        }

        // Dispatch to queue for processing
        if ($emailQueue->scheduled_at <= now()) {
            dispatch(new ProcessEmailJob((int) $emailQueue->id));
        } else {
            dispatch(new ProcessEmailJob((int) $emailQueue->id))->delay($emailQueue->scheduled_at);
        }

        // Update rule usage stats
        if ($routingRule) {
            $routingRule->increment('times_used');
            $routingRule->update(['last_used_at' => now()]);
        }

        return $emailQueue;
    }

    /**
     * Find matching routing rule for email
     */
    protected function findRoutingRule(string $emailType, array $data): ?EmailRoutingRule
    {
        foreach ($this->routingRules as $rule) {
            if ($this->ruleMatches($rule, $emailType, $data)) {
                return $rule;
            }
        }
        
        return null;
    }

    /**
     * Check if rule matches the email
     */
    protected function ruleMatches(EmailRoutingRule $rule, string $emailType, array $data): bool
    {
        // Check client-specific rule
        if ($rule->client_id && (!isset($data['client_id']) || $rule->client_id != $data['client_id'])) {
            return false;
        }

        // Check email type
        if ($rule->email_type && $rule->email_type !== $emailType) {
            return false;
        }

        // Check match type
        switch ($rule->match_type) {
            case 'email_type':
                return true; // Already checked above
                
            case 'to_domain':
                $domain = explode('@', $data['to_email'])[1] ?? '';
                return $domain === $rule->match_value || 
                       ($rule->match_pattern && preg_match($rule->match_pattern, $domain));
                
            case 'to_email':
                return $data['to_email'] === $rule->match_value ||
                       ($rule->match_pattern && preg_match($rule->match_pattern, $data['to_email']));
                
            case 'from_domain':
                $fromDomain = explode('@', $data['from_email'] ?? config('mail.from.address'))[1] ?? '';
                return $fromDomain === $rule->match_value;
                
            case 'client':
                return isset($data['client_id']) && $data['client_id'] == $rule->match_value;
                
            case 'all':
                return true;
                
            default:
                return false;
        }

        // Check time-based restrictions
        if ($rule->time_start && $rule->time_end) {
            $now = now()->format('H:i:s');
            if ($now < $rule->time_start || $now > $rule->time_end) {
                return false;
            }
        }

        // Check day of week
        if ($rule->days_of_week) {
            $today = strtolower(now()->format('D'));
            if (!in_array($today, $rule->days_of_week)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Select email server based on routing rule
     */
    protected function selectServer(?EmailRoutingRule $rule, array $data): ?EmailServer
    {
        // If no rule, use default server
        if (!$rule) {
            return $this->getDefaultServer();
        }

        switch ($rule->action_type) {
            case 'use_server':
                return $rule->server;
                
            case 'use_group':
                return $this->getServerFromGroup($rule->server_group, $rule);
                
            case 'round_robin':
                return $this->getRoundRobinServer($rule->server_group);
                
            case 'random':
                return $this->getRandomServer($rule->server_group);
                
            case 'failover':
                return $this->getFailoverServer($rule);
                
            default:
                return $this->getDefaultServer();
        }
    }

    /**
     * Get server from group with load balancing
     */
    protected function getServerFromGroup(string $group, EmailRoutingRule $rule): ?EmailServer
    {
        $servers = EmailServer::where('server_group', $group)
            ->where('status', EmailServerStatus::ACTIVE->value)
            ->orderBy('priority')
            ->get();

        if ($servers->isEmpty()) {
            return $rule->failoverServer ?? $this->getDefaultServer();
        }

        // Simple weighted selection
        $totalWeight = $servers->sum('weight');
        $random = mt_rand(1, $totalWeight);
        
        $cumulative = 0;
        foreach ($servers as $server) {
            $cumulative += $server->weight;
            if ($random <= $cumulative) {
                return $server;
            }
        }

        return $servers->first();
    }

    /**
     * Get failover server (try primary, then failover)
     */
    protected function getFailoverServer(EmailRoutingRule $rule): ?EmailServer
    {
        // Try primary server first
        if ($rule->server && $rule->server->status === EmailServerStatus::ACTIVE->value) {
            return $rule->server;
        }

        // Try failover
        if ($rule->failoverServer && $rule->failoverServer->status === EmailServerStatus::ACTIVE->value) {
            return $rule->failoverServer;
        }

        // Try any server in same group
        if ($rule->server_group) {
            return $this->getServerFromGroup($rule->server_group, $rule);
        }

        return $this->getDefaultServer();
    }

    /**
     * Get default server
     */
    protected function getDefaultServer(): ?EmailServer
    {
        if ($this->defaultServer) {
            return $this->defaultServer;
        }

        $this->defaultServer = EmailServer::where('is_default', true)
            ->where('status', EmailServerStatus::ACTIVE->value)
            ->first();

        return $this->defaultServer;
    }

    /**
     * Check if email is bounced
     */
    protected function isBounced(string $email): bool
    {
        $bounce = EmailBounce::where('email', $email)->first();
        
        if (!$bounce) {
            return false;
        }

        // Check if soft bounce and blocked period expired
        if ($bounce->bounce_type === 'soft' && $bounce->blocked_until && $bounce->blocked_until > now()) {
            return true;
        }

        // Hard bounces are permanent
        return $bounce->bounce_type === 'hard';
    }

    /**
     * Attach files to email
     */
    protected function attachFiles(EmailQueue $emailQueue, array $attachments): void
    {
        foreach ($attachments as $attachment) {
            $emailQueue->attachments()->create([
                'document_id' => $attachment['document_id'] ?? null,
                'filename' => $attachment['filename'],
                'file_path' => $attachment['file_path'] ?? null,
                'file_size' => $attachment['file_size'] ?? null,
                'mime_type' => $attachment['mime_type'] ?? null,
                'cid' => $attachment['cid'] ?? null,
                'is_inline' => $attachment['is_inline'] ?? false
            ]);
        }
    }

    /**
     * Send email using template
     */
    public function sendFromTemplate(string $templateCode, string $toEmail, array $data = [], array $options = []): EmailQueue
    {
        $template = EmailTemplate::where('template_code', $templateCode)
            ->where('is_active', true)
            ->firstOrFail();

        // Replace variables in subject and body
        $subject = $this->replaceVariables($template->subject, $data);
        $bodyHtml = $this->replaceVariables($template->body_html, $data);
        $bodyText = $template->body_text ? $this->replaceVariables($template->body_text, $data) : null;

        return $this->send(array_merge([
            'to_email' => $toEmail,
            'subject' => $subject,
            'body_html' => $bodyHtml,
            'body_text' => $bodyText,
            'template_id' => $template->id,
            'email_type' => $template->email_type,
            'priority' => $template->default_priority,
        ], $options));
    }

    /**
     * Replace variables in template
     */
    protected function replaceVariables(string $content, array $data): string
    {
        /** @var TemplateVariableRenderer $renderer */
        $renderer = app(TemplateVariableRenderer::class);

        return (string) $renderer->render($content, $data, [
            'app_name' => config('app.name'),
            'app_url' => rtrim((string) config('app.url'), '/'),
            'current_year' => now()->year,
            'current_date' => now()->format('Y-m-d'),
        ]);
    }
}
