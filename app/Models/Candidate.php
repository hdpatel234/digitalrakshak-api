<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

use Illuminate\Database\Eloquent\SoftDeletes;

class Candidate extends BaseModel
{
    use SoftDeletes;

    protected $table = "candidates";

    protected static function booted(): void
    {
        parent::booted();

        static::deleting(function (self $candidate): void {
            if (method_exists($candidate, 'isForceDeleting') && $candidate->isForceDeleting()) {
                return;
            }
            $candidate->candidateInvitations()->delete();
            $candidate->candidateServices()->delete();
        });
    }

    const CLIENT_ID = "client_id";
    const FIRST_NAME = "first_name";
    const LAST_NAME = "last_name";
    const EMAIL = "email";
    const PHONE = "phone";
    const ALTERNATE_PHONE = "alternate_phone";
    const ADDRESS = "address";
    const COUNTRY_ID = "country_id";
    const STATE_ID = "state_id";
    const CITY_ID = "city_id";
    const PINCODE = "pincode";
    const DATE_OF_BIRTH = "date_of_birth";
    const GENDER = "gender";
    const SOURCE = "source";
    const COUNTRY = "country";
    const STATE = "state";
    const CITY = "city";
    protected $fillable = [
        self::CLIENT_ID,
        self::FIRST_NAME,
        self::LAST_NAME,
        self::EMAIL,
        self::PHONE,
        self::ALTERNATE_PHONE,
        self::ADDRESS,
        self::COUNTRY_ID,
        self::STATE_ID,
        self::CITY_ID,
        self::PINCODE,
        self::DATE_OF_BIRTH,
        self::GENDER,
        self::SOURCE,
        self::STATUS
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, self::CLIENT_ID);
    }

    public function candidateInvitations(): HasMany
    {
        return $this->hasMany(CandidateInvitation::class, CandidateInvitation::CANDIDATE_ID);
    }

    public function candidateServices(): HasManyThrough
    {
        return $this->hasManyThrough(
            OrderItem::class,
            OrderCandidate::class,
            OrderCandidate::CANDIDATE_ID,
            OrderItem::ORDER_CANDIDATE_ID,
            'id',
            'id'
        );
    }

    public function orderCandidates(): HasMany
    {
        return $this->hasMany(OrderCandidate::class, OrderCandidate::CANDIDATE_ID);
    }

    public function candidateOrders(): BelongsToMany
    {
        return $this->belongsToMany(
            Order::class,
            "order_candidates",
            OrderCandidate::CANDIDATE_ID,
            OrderCandidate::ORDER_ID
        );
    }

    public function emailQueues(): HasMany
    {
        return $this->hasMany(EmailQueue::class, EmailQueue::CANDIDATE_ID);
    }

    public function serviceProcessingQueues(): HasMany
    {
        return $this->hasMany(ServiceProcessingQueue::class, ServiceProcessingQueue::CANDIDATE_ID);
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'candidate_packages', 'candidate_id', 'package_id');
    }

    public function serviceLogs(): HasMany
    {
        return $this->hasMany(CandidateServiceLog::class, 'candidate_id')->orderBy('created_at', 'desc');
    }
}
