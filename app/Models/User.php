<?php

namespace App\Models;

use App\Enums\UserType;
use App\Enums\UserStatus;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends BaseAuthenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected string $guard_name = 'api';

    const CLIENT_ID = 'client_id';
    const USER_TYPE = 'user_type';
    const FIRST_NAME = 'first_name';
    const LAST_NAME = 'last_name';
    const EMAIL = 'email';
    const EMAIL_VERIFIED_AT = 'email_verified_at';
    const PHONE_CODE = 'phone_code';
    const PHONE = 'phone';
    const PASSWORD = 'password';
    const REMEMBER_TOKEN = 'remember_token';
    const AVATAR = 'avatar';
    const LAST_LOGIN_AT = 'last_login_at';
    const LAST_LOGIN_IP = 'last_login_ip';
    const LAST_LOGIN_BROWSER = 'last_login_browser';
    const LAST_LOGIN_DEVICE = 'last_login_device';
    const LAST_LOGIN_OS = 'last_login_os';
    const LAST_LOGIN_PROVIDER = 'last_login_provider';
    const LAST_LOGIN_PROVIDER_ID = 'last_login_provider_id';


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        self::CLIENT_ID,
        self::USER_TYPE,
        self::FIRST_NAME,
        self::LAST_NAME,
        self::EMAIL,
        self::EMAIL_VERIFIED_AT,
        self::PHONE,
        self::PHONE_CODE,
        self::PASSWORD,
        self::AVATAR,
        self::STATUS,
        self::LAST_LOGIN_AT,
        self::LAST_LOGIN_IP,
        self::LAST_LOGIN_BROWSER,
        self::LAST_LOGIN_OS,
        self::LAST_LOGIN_DEVICE,
        self::LAST_LOGIN_PROVIDER,
        self::LAST_LOGIN_PROVIDER_ID,
    ];

    public static function supportedUserTypes(): array
    {
        return UserType::values();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        self::PASSWORD,
        self::REMEMBER_TOKEN,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            self::EMAIL_VERIFIED_AT => 'datetime',
            self::PASSWORD => 'hashed',
            self::USER_TYPE => UserType::class,
            self::STATUS => UserStatus::class,
        ];
    }

    protected function getDefaultGuardName(): string
    {
        return $this->guard_name;
    }

    public function emailQueues(): HasMany
    {
        return $this->hasMany(EmailQueue::class, EmailQueue::USER_ID);
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, SupportTicket::ASSIGNED_TO);
    }

    public function accessibilitySettings(): HasMany
    {
        return $this->hasMany(UserAccessibilitySetting::class, UserAccessibilitySetting::USER_ID);
    }

    public function configValues(): HasMany
    {
        return $this->hasMany(UserConfigValue::class, UserConfigValue::USER_ID);
    }

    public function dashboardPreferences(): HasMany
    {
        return $this->hasMany(UserDashboardPreference::class, UserDashboardPreference::USER_ID);
    }

    public function datetimePreferences(): HasMany
    {
        return $this->hasMany(UserDatetimePreference::class, UserDatetimePreference::USER_ID);
    }

    public function exportPreferences(): HasMany
    {
        return $this->hasMany(UserExportPreference::class, UserExportPreference::USER_ID);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(UserFavorite::class, UserFavorite::USER_ID);
    }

    public function notificationPreferences(): HasMany
    {
        return $this->hasMany(UserNotificationPreference::class, UserNotificationPreference::USER_ID);
    }

    public function privacySettings(): HasMany
    {
        return $this->hasMany(UserPrivacySetting::class, UserPrivacySetting::USER_ID);
    }

    public function recentItems(): HasMany
    {
        return $this->hasMany(UserRecentItem::class, UserRecentItem::USER_ID);
    }

    public function savedSearches(): HasMany
    {
        return $this->hasMany(UserSavedSearch::class, UserSavedSearch::USER_ID);
    }

    public function searchPreferences(): HasMany
    {
        return $this->hasMany(UserSearchPreference::class, UserSearchPreference::USER_ID);
    }

    public function shortcuts(): HasMany
    {
        return $this->hasMany(UserShortcut::class, UserShortcut::USER_ID);
    }

    public function toArray()
    {
        $array = parent::toArray();
        if (!empty($array[self::AVATAR])) {
            $array[self::AVATAR] = rtrim((string) config('app.url'), '/') . '/storage/' . ltrim((string) $array[self::AVATAR], '/');
        }
        return $array;
    }

    public function getIsAdminAttribute(): bool
    {
        return in_array($this->user_type, [
            UserType::SUPER_ADMIN,
            UserType::ADMIN_USER
        ], true);
    }
}
