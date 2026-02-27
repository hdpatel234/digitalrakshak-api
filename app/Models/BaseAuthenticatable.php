<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

abstract class BaseAuthenticatable extends Authenticatable
{
    protected static array $auditColumnsCache = [];

    // column constants
    const ID = 'id';
    const STATUS = 'status';
    const CREATED_BY = 'created_by';
    const UPDATED_BY = 'updated_by';
    const DELETED_BY = 'deleted_by';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            $userId = Auth::id();

            if (!$userId) {
                return;
            }

            if ($model->hasAuditColumn(static::CREATED_BY) && empty($model->{static::CREATED_BY})) {
                $model->{static::CREATED_BY} = $userId;
            }

            if ($model->hasAuditColumn(static::UPDATED_BY) && empty($model->{static::UPDATED_BY})) {
                $model->{static::UPDATED_BY} = $userId;
            }
        });

        static::updating(function (self $model): void {
            $userId = Auth::id();

            if (!$userId) {
                return;
            }

            if ($model->hasAuditColumn(static::UPDATED_BY)) {
                $model->{static::UPDATED_BY} = $userId;
            }
        });

        static::deleting(function (self $model): void {
            $userId = Auth::id();

            if (!$userId || !$model->hasAuditColumn(static::DELETED_BY)) {
                return;
            }

            if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
                return;
            }

            $model->{static::DELETED_BY} = $userId;
            $model->saveQuietly();
        });
    }

    protected function hasAuditColumn(string $column): bool
    {
        $table = $this->getTable();
        $cacheKey = $table . ':' . $column;

        if (!array_key_exists($cacheKey, static::$auditColumnsCache)) {
            static::$auditColumnsCache[$cacheKey] = Schema::hasColumn($table, $column);
        }

        return static::$auditColumnsCache[$cacheKey];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, static::CREATED_BY);
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, static::UPDATED_BY);
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, static::DELETED_BY);
    }
}
