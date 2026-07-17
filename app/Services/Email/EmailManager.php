<?php

namespace App\Services\Email;

use App\Models\EmailQueue;
use App\Models\EmailServer;
use Illuminate\Support\Str;
use App\Enums\EmailPriority;
use App\Jobs\ProcessEmailJob;
use App\Enums\EmailQueueStatus;
use App\Enums\EmailServerStatus;
use App\Models\EmailRoutingRule;
use App\Services\EmailQueueService;
use Illuminate\Support\Facades\Auth;
use App\Services\EmailRoutingRuleService;
use App\Services\Email\TemplateVariableRenderer;
use App\Services\EmailAttachmentService;
use App\Services\EmailServerService;
use App\Services\EmailTemplateService;

class EmailManager
{
    /**
     * @var \Illuminate\Database\Eloquent\Collection<int, EmailRoutingRule>
     */
    protected $routingRules;

    /**
     * @var EmailServer|null
     */
    protected $defaultServer;

    public function __construct(
        protected EmailQueueService $emailQueueService,
        protected EmailServerService $emailServerService,
        protected EmailTemplateService $emailTemplateService,
        protected EmailAttachmentService $emailAttachmentService,
        protected EmailRoutingRuleService $emailRoutingRuleService
    ) {
        $this->loadRoutingRules();
    }

    protected function loadRoutingRules()
    {
        $this->routingRules = $this->emailRoutingRuleService->query()->with(['server', 'failoverServer'])
            ->whereColumn($this->emailRoutingRuleService->isActive(), true)
            ->orderBy($this->emailRoutingRuleService->rulePriority(), 'desc')
            ->get();
    }

    public function send(array $data): EmailQueue
    {
        if (!isset($data['to_email']) || !isset($data['subject']) || (!isset($data['body_html']) && !isset($data['body_text']))) {
            throw new \InvalidArgumentException('Missing required email fields');
        }

        $emailUid = 'email_' . Str::random(32);

        $emailType = $data['email_type'] ?? 'system_notification';

        $routingRule = $this->findRoutingRule($emailType, $data);

        $server = $this->selectServer($routingRule, $data);

        if ($this->isBounced($data['to_email'])) {
            throw new \Exception("Email {$data['to_email']} is in bounce list");
        }

        $emailQueue = $this->emailQueueService->create([
            $this->emailQueueService->emailUid() => $emailUid,
            $this->emailQueueService->toEmail() => $data['to_email'],
            $this->emailQueueService->toName() => $data['to_name'] ?? null,
            $this->emailQueueService->cc() => $data['cc'] ?? null,
            $this->emailQueueService->bcc() => $data['bcc'] ?? null,
            $this->emailQueueService->replyTo() => $data['reply_to'] ?? null,
            $this->emailQueueService->subject() => $data['subject'],
            $this->emailQueueService->bodyHtml() => $data['body_html'] ?? null,
            $this->emailQueueService->bodyText() => $data['body_text'] ?? null,
            $this->emailQueueService->templateId() => $data['template_id'] ?? null,
            $this->emailQueueService->emailType() => $emailType,
            $this->emailQueueService->priority() => $data['priority'] ?? EmailPriority::NORMAL->value,
            $this->emailQueueService->clientId() => $data['client_id'] ?? null,
            $this->emailQueueService->candidateId() => $data['candidate_id'] ?? null,
            $this->emailQueueService->orderId() => $data['order_id'] ?? null,
            $this->emailQueueService->userId() => $data['user_id'] ?? null,
            $this->emailQueueService->assignedServerId() => $server?->id,
            $this->emailQueueService->routingRuleId() => $routingRule?->id,
            $this->emailQueueService->status() => EmailQueueStatus::PENDING->value,
            $this->emailQueueService->scheduledAt() => $data['scheduled_at'] ?? now(),
            $this->emailQueueService->expiresAt() => $data['expires_at'] ?? now()->addDays(7),
            $this->emailQueueService->createdBy() => $data['created_by'] ?? Auth::id()
        ]);

        if (isset($data['attachments']) && is_array($data['attachments'])) {
            $this->attachFiles($emailQueue, $data['attachments']);
        }

        if ($emailQueue->scheduled_at <= now()) {
            dispatch(new ProcessEmailJob((int) $emailQueue->id));
        } else {
            dispatch(new ProcessEmailJob((int) $emailQueue->id))->delay($emailQueue->scheduled_at);
        }

        if ($routingRule) {
            $routingRule->increment('times_used');
            $routingRule->update(['last_used_at' => now()]);
        }

        return $emailQueue;
    }

    protected function findRoutingRule(string $emailType, array $data): ?EmailRoutingRule
    {
        foreach ($this->routingRules as $rule) {
            if ($this->ruleMatches($rule, $emailType, $data)) {
                return $rule;
            }
        }

        return null;
    }

    protected function ruleMatches(EmailRoutingRule $rule, string $emailType, array $data): bool
    {
        if ($rule->client_id && (!isset($data['client_id']) || $rule->client_id != $data['client_id'])) {
            return false;
        }

        if ($rule->email_type && $rule->email_type !== $emailType) {
            return false;
        }

        $isMatch = false;
        switch ($rule->match_type) {
            case 'email_type':
                $isMatch = true;
                break;

            case 'to_domain':
                $domain = explode('@', $data['to_email'])[1] ?? '';
                $isMatch = $domain === $rule->match_value ||
                    ($rule->match_pattern && preg_match($rule->match_pattern, $domain));
                break;

            case 'to_email':
                $isMatch = $data['to_email'] === $rule->match_value ||
                    ($rule->match_pattern && preg_match($rule->match_pattern, $data['to_email']));
                break;

            case 'from_domain':
                $fromDomain = explode('@', $data['from_email'] ?? config('mail.from.address'))[1] ?? '';
                $isMatch = $fromDomain === $rule->match_value;
                break;

            case 'client':
                $isMatch = isset($data['client_id']) && $data['client_id'] == $rule->match_value;
                break;

            case 'all':
                $isMatch = true;
                break;

            default:
                return false;
        }

        if (!$isMatch) {
            return false;
        }

        if ($rule->time_start && $rule->time_end) {
            $now = now()->format('H:i:s');
            if ($now < $rule->time_start || $now > $rule->time_end) {
                return false;
            }
        }

        if ($rule->days_of_week) {
            $today = strtolower(now()->format('D'));
            if (!in_array($today, $rule->days_of_week)) {
                return false;
            }
        }

        return true;
    }

    protected function selectServer(?EmailRoutingRule $rule, array $data): ?EmailServer
    {
        if (!$rule) {
            return $this->getDefaultServer();
        }

        switch ($rule->action_type) {
            case 'use_server':
                return $rule->server;

            case 'use_group':
                return $this->getServerFromGroup($rule->server_group, $rule);

                // case 'round_robin':
                //     return $this->getRoundRobinServer($rule->server_group);

                // case 'random':
                //     return $this->getRandomServer($rule->server_group);

            case 'failover':
                return $this->getFailoverServer($rule);

            default:
                return $this->getDefaultServer();
        }
    }

    protected function getServerFromGroup(string $group, EmailRoutingRule $rule): ?EmailServer
    {
        $servers = $this->emailServerService->query()->where($this->emailServerService->serverGroup(), $group)
            ->where($this->emailServerService->status(), EmailServerStatus::ACTIVE->value)
            ->orderBy($this->emailServerService->priority())
            ->get();

        if ($servers->isEmpty()) {
            return $rule->failoverServer ?? $this->getDefaultServer();
        }

        $totalWeight = $servers->sum($this->emailServerService->weight());
        $random = mt_rand(1, $totalWeight);

        $cumulative = 0;
        foreach ($servers as $server) {
            $cumulative += $server->{$this->emailServerService->weight()};
            if ($random <= $cumulative) {
                return $server;
            }
        }

        return $servers->first();
    }

    protected function getFailoverServer(EmailRoutingRule $rule): ?EmailServer
    {
        if ($rule->server && $rule->server->status === EmailServerStatus::ACTIVE->value) {
            return $rule->server;
        }

        if ($rule->failoverServer && $rule->failoverServer->status === EmailServerStatus::ACTIVE->value) {
            return $rule->failoverServer;
        }

        if ($rule->server_group) {
            return $this->getServerFromGroup($rule->server_group, $rule);
        }

        return $this->getDefaultServer();
    }

    protected function getDefaultServer(): ?EmailServer
    {
        if ($this->defaultServer) {
            return $this->defaultServer;
        }

        $this->defaultServer = $this->emailServerService->query()->where($this->emailServerService->isDefault(), true)
            ->where($this->emailServerService->status(), EmailServerStatus::ACTIVE->value)
            ->first();

        return $this->defaultServer;
    }

    protected function isBounced(string $email): bool
    {
        $bounce = $this->emailQueueService->query()->where($this->emailQueueService->toEmail(), $email)->first();

        if (!$bounce) {
            return false;
        }

        if ($bounce->bounce_type === 'soft' && $bounce->blocked_until && $bounce->blocked_until > now()) {
            return true;
        }

        return $bounce->bounce_type === 'hard';
    }

    protected function attachFiles(EmailQueue $emailQueue, array $attachments): void
    {
        foreach ($attachments as $attachment) {
            $emailQueue->attachments()->create([
                $this->emailAttachmentService->emailQueueId()   => $emailQueue->id,
                $this->emailAttachmentService->documentId()     => $attachment['document_id'] ?? null,
                $this->emailAttachmentService->filename()       => $attachment['filename'],
                $this->emailAttachmentService->filePath()       => $attachment['file_path'] ?? null,
                $this->emailAttachmentService->fileSize()       => $attachment['file_size'] ?? null,
                $this->emailAttachmentService->mimeType()       => $attachment['mime_type'] ?? null,
                $this->emailAttachmentService->cid()            => $attachment['cid'] ?? null,
                $this->emailAttachmentService->isInline()       => $attachment['is_inline'] ?? false
            ]);
        }
    }

    public function sendFromTemplate(string $templateCode, string $toEmail, array $data = [], array $options = []): EmailQueue
    {
        $template = $this->emailTemplateService->query()->where($this->emailTemplateService->templateCode(), $templateCode)
            ->where($this->emailTemplateService->isActive(), true)
            ->firstOrFail();

        $subject = $this->replaceVariables($template->{$this->emailTemplateService->subject()}, $data);
        $bodyHtml = $this->replaceVariables($template->{$this->emailTemplateService->bodyHtml()}, $data);
        $bodyText = $template->{$this->emailTemplateService->bodyText()} ? $this->replaceVariables($template->{$this->emailTemplateService->bodyText()}, $data) : null;

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
