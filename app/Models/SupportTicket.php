<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CandidateOrder;
use App\Models\SupportDepartment;
use App\Models\SupportPriority;

class SupportTicket extends BaseModel
{
    protected $table = "support_tickets";

    const CLIENT_ID = "client_id";
    const ORDER_ID = "order_id";
    const EXTERNAL_TICKET_ID = "external_ticket_id";
    const DEPARTMENT_ID = "department_id";
    const PRIORITY_ID = "priority_id";
    const TICKET_NUMBER = "ticket_number";
    const SUBJECT = "subject";
    const DESCRIPTION = "description";
    const STATUS = "status";
    const ASSIGNED_TO = "assigned_to";
    const ASSIGNED_NAME = "assigned_name";
    const RESOLUTION = "resolution";
    const RESOLVED_AT = "resolved_at";
    const CLOSED_AT = "closed_at";
    const TICKET_DATA = "ticket_data";
    const DOCUMENT_ID = "document_id";
    const SYNC_STATUS = "sync_status";
    const SYNC_MESSAGE = "sync_message";
    const LAST_SYNC_AT = "last_sync_at";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::CLIENT_ID,
        self::ORDER_ID,
        self::EXTERNAL_TICKET_ID,
        self::DEPARTMENT_ID,
        self::PRIORITY_ID,
        self::TICKET_NUMBER,
        self::SUBJECT,
        self::DESCRIPTION,
        self::STATUS,
        self::ASSIGNED_TO,
        self::ASSIGNED_NAME,
        self::RESOLUTION,
        self::RESOLVED_AT,
        self::CLOSED_AT,
        self::TICKET_DATA,
        self::SYNC_STATUS,
        self::SYNC_MESSAGE,
        self::LAST_SYNC_AT,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(CandidateOrder::class, self::ORDER_ID);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(SupportDepartment::class, self::DEPARTMENT_ID);
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(SupportPriority::class, self::PRIORITY_ID);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, self::CLIENT_ID);
    }
}
