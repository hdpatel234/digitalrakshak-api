<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends BaseModel
{

    protected $table = "clients";

    const COMPANY_NAME = "company_name";
    const EMAIL = "email";
    const LOGO = "logo";
    const PHONE_CODE = "phone_code";
    const PHONE = "phone";
    const GST_NUMBER = "gst_number";
    const PAN_NUMBER = "pan_number";
    const ADDRESS = "address";
    const COUNTRY_ID = "country_id";
    const STATE_ID = "state_id";
    const CITY_ID = "city_id";
    const PINCODE = "pincode";
    const CURRENCY = "currency";
    const CREDIT_LIMIT = "credit_limit";
    const CREDIT_BALANCE = "credit_balance";
    const PAYMENT_TERMS = "payment_terms";
    const DEFAULT_SUPPORT_CONFIG_ID = "default_support_config_id";
    const STATUS = "status";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";
    protected $fillable = [
        self::COMPANY_NAME,
        self::EMAIL,
        self::LOGO,
        self::PHONE_CODE,
        self::PHONE,
        self::GST_NUMBER,
        self::PAN_NUMBER,
        self::ADDRESS,
        self::COUNTRY_ID,
        self::STATE_ID,
        self::CITY_ID,
        self::PINCODE,
        self::CURRENCY,
        self::CREDIT_LIMIT,
        self::CREDIT_BALANCE,
        self::PAYMENT_TERMS,
        self::DEFAULT_SUPPORT_CONFIG_ID,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];

    public function defaultSupportConfig(): BelongsTo
    {
        return $this->belongsTo(SupportConfig::class, self::DEFAULT_SUPPORT_CONFIG_ID);
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class, Candidate::CLIENT_ID);
    }

    public function candidateInvitations(): HasMany
    {
        return $this->hasMany(CandidateInvitation::class, CandidateInvitation::CLIENT_ID);
    }

    public function candidateImportHistories(): HasMany
    {
        return $this->hasMany(CandidateImportHistory::class, CandidateImportHistory::CLIENT_ID);
    }

    public function candidateOrders(): HasMany
    {
        return $this->hasMany(CandidateOrder::class, CandidateOrder::CLIENT_ID);
    }

    public function clientApiKeys(): HasMany
    {
        return $this->hasMany(ClientApiKey::class, ClientApiKey::CLIENT_ID);
    }

    public function clientApiLogs(): HasMany
    {
        return $this->hasMany(ClientApiLog::class, ClientApiLog::CLIENT_ID);
    }

    public function clientApiQuotas(): HasMany
    {
        return $this->hasMany(ClientApiQuota::class, ClientApiQuota::CLIENT_ID);
    }

    public function servicePricings(): HasMany
    {
        return $this->hasMany(ClientServicePricing::class, ClientServicePricing::CLIENT_ID);
    }

    public function supportConfigs(): HasMany
    {
        return $this->hasMany(SupportConfig::class);
    }

    public function webhooks(): HasMany
    {
        return $this->hasMany(ClientWebhook::class, ClientWebhook::CLIENT_ID);
    }

    public function clientWebhookLogs(): HasMany
    {
        return $this->hasMany(ClientWebhookLog::class, ClientWebhookLog::CLIENT_ID);
    }

    public function emailQueues(): HasMany
    {
        return $this->hasMany(EmailQueue::class, EmailQueue::CLIENT_ID);
    }

    public function emailRoutingRules(): HasMany
    {
        return $this->hasMany(EmailRoutingRule::class, EmailRoutingRule::CLIENT_ID);
    }

    public function generatedDocuments(): HasMany
    {
        return $this->hasMany(GeneratedDocument::class, GeneratedDocument::CLIENT_ID);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, Invoice::CLIENT_ID);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class, Package::CLIENT_ID);
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, PaymentTransaction::CLIENT_ID);
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, SupportTicket::CLIENT_ID);
    }

    public function syncJobs(): HasMany
    {
        return $this->hasMany(SyncJob::class, SyncJob::CLIENT_ID);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, User::CLIENT_ID);
    }

    public function webhookLogs(): HasMany
    {
        return $this->hasMany(WebhookLog::class, WebhookLog::CLIENT_ID);
    }
}
