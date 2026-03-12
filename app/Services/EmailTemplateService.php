<?php

namespace App\Services;

use App\Models\EmailTemplate;
use App\Repositories\EmailTemplateRepository;
use App\Services\Email\TemplateVariableRenderer;

class EmailTemplateService extends BaseService
{
    protected TemplateVariableRenderer $templateVariableRenderer;

    public function __construct(
        EmailTemplateRepository $repository,
        TemplateVariableRenderer $templateVariableRenderer
    )
    {
        $this->repository = $repository;
        $this->templateVariableRenderer = $templateVariableRenderer;
    }

    // column constants
    public function serverId()
    {
        return $this->repository->serverId();
    }

    public function templateName()
    {
        return $this->repository->templateName();
    }

    public function templateCode()
    {
        return $this->repository->templateCode();
    }

    public function emailType()
    {
        return $this->repository->emailType();
    }

    public function subject()
    {
        return $this->repository->subject();
    }

    public function bodyHtml()
    {
        return $this->repository->bodyHtml();
    }

    public function bodyText()
    {
        return $this->repository->bodyText();
    }

    public function variables()
    {
        return $this->repository->variables();
    }

    public function defaultPriority()
    {
        return $this->repository->defaultPriority();
    }

    public function allowedAttachments()
    {
        return $this->repository->allowedAttachments();
    }

    public function isActive()
    {
        return $this->repository->isActive();
    }

    public function createdBy()
    {
        return $this->repository->createdBy();
    }

    public function updatedBy()
    {
        return $this->repository->updatedBy();
    }

    public function findActiveByCode(string $templateCode): ?EmailTemplate
    {
        return $this->query()
            ->where($this->templateCode(), $templateCode)
            ->where($this->isActive(), 1)
            ->first();
    }

    public function renderTemplate(EmailTemplate $template, array $variables = []): array
    {
        $fallbacks = $this->defaultVariables($variables);

        return [
            'subject' => $this->templateVariableRenderer->render(
                (string) ($template->{$this->subject()} ?? ''),
                $variables,
                $fallbacks
            ),
            'body_html' => $this->templateVariableRenderer->render(
                $template->{$this->bodyHtml()},
                $variables,
                $fallbacks
            ),
            'body_text' => $this->templateVariableRenderer->render(
                $template->{$this->bodyText()},
                $variables,
                $fallbacks
            ),
        ];
    }

    protected function defaultVariables(array $variables): array
    {
        return [
            'app_name' => config('app.name'),
            'app_url' => rtrim((string) config('app.url'), '/'),
            'current_year' => now()->year,
            'current_date' => now()->format('Y-m-d'),
            ...$variables,
        ];
    }
}
