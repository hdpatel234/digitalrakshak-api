<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateInvitation extends BaseModel
{
    
    protected $table = "candidate_invitations";
    protected $casts = [
        self::FORM_DATA => 'array',
    ];
    
    const CANDIDATE_ID = "candidate_id";
    const CLIENT_ID = "client_id";
    const PACKAGE_ID = "package_id";
    const INVITATION_TYPE = "invitation_type";
    const INVITATION_TOKEN = "invitation_token";
    const FORM_LINK = "form_link";
    const FORM_DATA = "form_data";
    const INVITED_BY = "invited_by";
    const INVITED_AT = "invited_at";
    const VIEWED_AT = "viewed_at";
    const REMINDER_SENT_AT = "reminder_sent_at";
    const EXPIRES_AT = "expires_at";
    const REMINDER_COUNT = "reminder_count";
    const LAST_REMINDER_SENT_AT = "last_reminder_sent_at";
    const COMPLETED_AT = "completed_at";
    const STATUS = "status";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";
    protected $fillable = [
        self::CANDIDATE_ID,
        self::CLIENT_ID,
        self::PACKAGE_ID,
        self::INVITATION_TYPE,
        self::INVITATION_TOKEN,
        self::FORM_LINK,
        self::FORM_DATA,
        self::INVITED_BY,
        self::INVITED_AT,
        self::VIEWED_AT,
        self::REMINDER_SENT_AT,
        self::EXPIRES_AT,
        self::REMINDER_COUNT,
        self::LAST_REMINDER_SENT_AT,
        self::COMPLETED_AT,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, self::CANDIDATE_ID);
    }
}
