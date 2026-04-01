<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AiAccount extends Model
{
    use HasFactory;

    protected $table = 'ai_accounts';

    protected $fillable = [
        'provider_id',
        'api_key',
        'daily_usage',
        'limit_per_day',
        'last_used_date',
        'is_active',
    ];

    protected $casts = [
        'last_used_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(AiProvider::class, 'provider_id');
    }

    /**
     * Scope a query to only include accounts that are available for the given provider today.
     */
    public function scopeAvailableForProvider($query, int $providerId)
    {
        return $query->where('provider_id', $providerId)
                     ->where('is_active', true)
                     ->where(function ($q) {
                         // Either it hasn't been used today OR it hasn't reached its limit
                         $q->whereDate('last_used_date', '!=', Carbon::today())
                           ->orWhereNull('last_used_date')
                           ->orWhereColumn('daily_usage', '<', 'limit_per_day');
                     });
    }

    /**
     * Increment the daily usage for this account.
     */
    public function incrementUsage(): void
    {
        $today = Carbon::today();

        if (!$this->last_used_date || !$this->last_used_date->isSameDay($today)) {
            $this->daily_usage = 1;
            $this->last_used_date = $today;
        } else {
            $this->daily_usage += 1;
        }

        $this->save();
    }

    /**
     * Immediately exhaust the account's quota for today (e.g., when a 429 error is hit early).
     */
    public function markAsExhaustedForToday(): void
    {
        $this->daily_usage = $this->limit_per_day;
        $this->last_used_date = Carbon::today();
        $this->save();
    }
}
