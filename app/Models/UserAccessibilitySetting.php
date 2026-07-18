<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAccessibilitySetting extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "user_accessibility_settings";
    
    const USER_ID = "user_id";
    const HIGH_CONTRAST = "high_contrast";
    const LARGE_TEXT = "large_text";
    const REDUCE_MOTION = "reduce_motion";
    const SCREEN_READER_OPTIMIZED = "screen_reader_optimized";
    const KEYBOARD_NAVIGATION = "keyboard_navigation";
    const FOCUS_INDICATORS = "focus_indicators";
    const COLOR_BLIND_MODE = "color_blind_mode";
    const FONT_FAMILY = "font_family";
    const FONT_SIZE_MULTIPLIER = "font_size_multiplier";
    const LINE_HEIGHT_MULTIPLIER = "line_height_multiplier";
    const LETTER_SPACING = "letter_spacing";
    protected $fillable = [
        self::USER_ID,
        self::HIGH_CONTRAST,
        self::LARGE_TEXT,
        self::REDUCE_MOTION,
        self::SCREEN_READER_OPTIMIZED,
        self::KEYBOARD_NAVIGATION,
        self::FOCUS_INDICATORS,
        self::COLOR_BLIND_MODE,
        self::FONT_FAMILY,
        self::FONT_SIZE_MULTIPLIER,
        self::LINE_HEIGHT_MULTIPLIER,
        self::LETTER_SPACING,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, self::USER_ID);
    }
}
