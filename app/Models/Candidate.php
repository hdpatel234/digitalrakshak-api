<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Candidate extends BaseModel
{
    
    protected $table = "candidates";
    
    const CLIENT_ID = "client_id";
    const FIRST_NAME = "first_name";
    const LAST_NAME = "last_name";
    const EMAIL = "email";
    const PHONE = "phone";
    const ALTERNATE_PHONE = "alternate_phone";
    const ADDRESS = "address";
    const CITY = "city";
    const STATE = "state";
    const PINCODE = "pincode";
    const COUNTRY = "country";
    const DATE_OF_BIRTH = "date_of_birth";
    const GENDER = "gender";
    const SOURCE = "source";
    const STATUS = "status";
    const INVITATION_SENT_AT = "invitation_sent_at";
    const INVITATION_ACCEPTED_AT = "invitation_accepted_at";
    const LAST_ORDER_ID = "last_order_id";
    const TOTAL_ORDERS = "total_orders";
    const TOTAL_ORDER_VALUE = "total_order_value";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";
    protected $fillable = [
        self::CLIENT_ID,
        self::FIRST_NAME,
        self::LAST_NAME,
        self::EMAIL,
        self::PHONE,
        self::ALTERNATE_PHONE,
        self::ADDRESS,
        self::CITY,
        self::STATE,
        self::PINCODE,
        self::COUNTRY,
        self::DATE_OF_BIRTH,
        self::GENDER,
        self::SOURCE,
        self::STATUS,
        self::INVITATION_SENT_AT,
        self::INVITATION_ACCEPTED_AT,
        self::LAST_ORDER_ID,
        self::TOTAL_ORDERS,
        self::TOTAL_ORDER_VALUE,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, self::CLIENT_ID);
    }

    public function lastOrder(): BelongsTo
    {
        return $this->belongsTo(CandidateOrder::class, self::LAST_ORDER_ID);
    }

    public function candidateInvitations(): HasMany
    {
        return $this->hasMany(CandidateInvitation::class, CandidateInvitation::CANDIDATE_ID);
    }

    public function candidateServices(): HasMany
    {
        return $this->hasMany(CandidateService::class, CandidateService::CANDIDATE_ID);
    }

    public function orderCandidates(): HasMany
    {
        return $this->hasMany(OrderCandidate::class, OrderCandidate::CANDIDATE_ID);
    }

    public function candidateOrders(): BelongsToMany
    {
        return $this->belongsToMany(
            CandidateOrder::class,
            "order_candidates",
            OrderCandidate::CANDIDATE_ID,
            OrderCandidate::ORDER_ID
        );
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, Document::CANDIDATE_ID);
    }

    public function emailQueues(): HasMany
    {
        return $this->hasMany(EmailQueue::class, EmailQueue::CANDIDATE_ID);
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, SupportTicket::CANDIDATE_ID);
    }

    public function serviceProcessingQueues(): HasMany
    {
        return $this->hasMany(ServiceProcessingQueue::class, ServiceProcessingQueue::CANDIDATE_ID);
    }
}
