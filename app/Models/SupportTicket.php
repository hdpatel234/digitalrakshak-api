<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class SupportTicket extends BaseModel
{
    
    protected $table = "support_tickets";
    
    const CLIENT_ID = "client_id";
    const SUPPORT_CONFIG_ID = "support_config_id";
    const ORDER_ITEM_ID = "order_item_id";
    const SERVICE_ID = "service_id";
    const CANDIDATE_ID = "candidate_id";
    const EXTERNAL_TICKET_ID = "external_ticket_id";
    const TICKET_NUMBER = "ticket_number";
    const SUBJECT = "subject";
    const DESCRIPTION = "description";
    const PRIORITY = "priority";
    const STATUS = "status";
    const CATEGORY = "category";
    const DEPARTMENT = "department";
    const ASSIGNED_TO = "assigned_to";
    const ASSIGNED_NAME = "assigned_name";
    const RESOLUTION = "resolution";
    const RESOLVED_AT = "resolved_at";
    const CLOSED_AT = "closed_at";
    const TICKET_DATA = "ticket_data";
    const SYNC_STATUS = "sync_status";
    const SYNC_MESSAGE = "sync_message";
    const LAST_SYNC_AT = "last_sync_at";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::CLIENT_ID,
        self::SUPPORT_CONFIG_ID,
        self::ORDER_ITEM_ID,
        self::SERVICE_ID,
        self::CANDIDATE_ID,
        self::EXTERNAL_TICKET_ID,
        self::TICKET_NUMBER,
        self::SUBJECT,
        self::DESCRIPTION,
        self::PRIORITY,
        self::STATUS,
        self::CATEGORY,
        self::DEPARTMENT,
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
}