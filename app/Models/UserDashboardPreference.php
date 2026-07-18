<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDashboardPreference extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "user_dashboard_preferences";
    
    const USER_ID = "user_id";
    const DEFAULT_DASHBOARD = "default_dashboard";
    const WIDGET_LAYOUT = "widget_layout";
    const HIDDEN_WIDGETS = "hidden_widgets";
    const WIDGET_SETTINGS = "widget_settings";
    const REFRESH_INTERVAL = "refresh_interval";
    const DEFAULT_VIEW = "default_view";
    const ITEMS_PER_PAGE = "items_per_page";
    protected $fillable = [
        self::USER_ID,
        self::DEFAULT_DASHBOARD,
        self::WIDGET_LAYOUT,
        self::HIDDEN_WIDGETS,
        self::WIDGET_SETTINGS,
        self::REFRESH_INTERVAL,
        self::DEFAULT_VIEW,
        self::ITEMS_PER_PAGE,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, self::USER_ID);
    }
}
