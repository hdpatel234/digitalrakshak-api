<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateInvitationsLog extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "candidate_invitations_logs";
    
    const INVITATION_ID = "invitation_id";
    const ACTION = "action";
    const IP_ADDRESS = "ip_address";
    const USER_AGENT = "user_agent";
    const STATUS = "status";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";
    protected $fillable = [
        self::INVITATION_ID,
        self::ACTION,
        self::IP_ADDRESS,
        self::USER_AGENT,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(CandidateInvitation::class, self::INVITATION_ID);
    }
}
