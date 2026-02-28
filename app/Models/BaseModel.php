<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

abstract class BaseModel extends Model
{
    protected static array $auditColumnsCache = [];
    protected static array $dateTimeColumnsCache = [];
    protected static ?int $timezoneCacheUserId = null;
    protected static ?string $timezoneCacheValue = null;

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

    public function attributesToArray(): array
    {
        $attributes = parent::attributesToArray();

        return $this->convertDateColumnsToUserTimezone($attributes);
    }

    protected function convertDateColumnsToUserTimezone(array $attributes): array
    {
        $targetTimezone = $this->resolveUserTimezone();
        $sourceTimezone = config('app.timezone', 'UTC');
        $dateTimeColumns = $this->getDateTimeColumns();

        foreach ($dateTimeColumns as $column => $columnType) {
            if (!array_key_exists($column, $attributes) || empty($attributes[$column])) {
                continue;
            }

            $rawValue = $this->getRawOriginal($column);
            if (empty($rawValue)) {
                continue;
            }

            $attributes[$column] = $this->convertDateValueByType(
                (string) $rawValue,
                $columnType,
                $sourceTimezone,
                $targetTimezone
            );
        }

        return $attributes;
    }

    protected function getDateTimeColumns(): array
    {
        $table = $this->getTable();

        if (!array_key_exists($table, static::$dateTimeColumnsCache)) {
            $columns = [];
            foreach (Schema::getColumnListing($table) as $column) {
                $type = strtolower(Schema::getColumnType($table, $column));
                if (in_array($type, ['date', 'datetime', 'datetimetz', 'timestamp', 'timestamptz', 'time'], true)) {
                    $columns[$column] = $type;
                }
            }

            static::$dateTimeColumnsCache[$table] = $columns;
        }

        return static::$dateTimeColumnsCache[$table];
    }

    protected function convertDateValueByType(
        string $value,
        string $columnType,
        string $sourceTimezone,
        string $targetTimezone
    ): string {
        try {
            $parsed = Carbon::parse($value, $sourceTimezone)->setTimezone($targetTimezone);
        } catch (\Throwable $e) {
            return $value;
        }

        if ($columnType === 'date') {
            return $parsed->toDateString();
        }

        if ($columnType === 'time') {
            return $parsed->format('H:i:s');
        }

        return $parsed->format($this->getDateFormat());
    }

    protected function resolveUserTimezone(): string
    {
        $defaultTimezone = 'Asia/Kolkata';
        $userId = Auth::id();

        if (!$userId) {
            return $defaultTimezone;
        }

        if (static::$timezoneCacheUserId === $userId && static::$timezoneCacheValue) {
            return static::$timezoneCacheValue;
        }

        $timezone = DB::table('user_datetime_preferences')
            ->where(UserDatetimePreference::USER_ID, $userId)
            ->value(UserDatetimePreference::TIMEZONE);

        static::$timezoneCacheUserId = $userId;
        static::$timezoneCacheValue = $timezone ?: $defaultTimezone;

        return static::$timezoneCacheValue;
    }
}
