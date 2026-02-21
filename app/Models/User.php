<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

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
}
