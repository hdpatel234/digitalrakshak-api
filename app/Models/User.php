<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends BaseAuthenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected string $guard_name = 'api';

    const CLIENT_ID = 'client_id';
    const USER_TYPE = 'user_type';
    const FIRST_NAME = 'first_name';
    const LAST_NAME = 'last_name';
    const EMAIL = 'email';
    const EMAIL_VERIFIED_AT = 'email_verified_at';
    const PASSWORD = 'password';
    const REMEMBER_TOKEN = 'remember_token';
    const LAST_LOGIN_AT = 'last_login_at';
    const LAST_LOGIN_IP = 'last_login_ip';
    const LAST_LOGIN_BROWSER = 'last_login_browser';
    const LAST_LOGIN_DEVICE = 'last_login_device';
    const IS_ACTIVE = 'is_active';
    const IS_ADMIN = 'is_admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        self::FIRST_NAME,
        self::LAST_NAME,
        self::EMAIL,
        self::PASSWORD,
        self::IS_ACTIVE,
        self::IS_ADMIN,
        self::LAST_LOGIN_AT,
        self::LAST_LOGIN_IP,
        self::LAST_LOGIN_BROWSER,
        self::LAST_LOGIN_DEVICE,
    ];

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
}
