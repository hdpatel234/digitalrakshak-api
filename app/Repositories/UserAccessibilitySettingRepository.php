<?php

namespace App\Repositories;

use App\Models\UserAccessibilitySetting;

class UserAccessibilitySettingRepository extends BaseRepository
{
    public function __construct(UserAccessibilitySetting $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function userId()
    {
        return UserAccessibilitySetting::USER_ID;
    }

    public function highContrast()
    {
        return UserAccessibilitySetting::HIGH_CONTRAST;
    }

    public function largeText()
    {
        return UserAccessibilitySetting::LARGE_TEXT;
    }

    public function reduceMotion()
    {
        return UserAccessibilitySetting::REDUCE_MOTION;
    }

    public function screenReaderOptimized()
    {
        return UserAccessibilitySetting::SCREEN_READER_OPTIMIZED;
    }

    public function keyboardNavigation()
    {
        return UserAccessibilitySetting::KEYBOARD_NAVIGATION;
    }

    public function focusIndicators()
    {
        return UserAccessibilitySetting::FOCUS_INDICATORS;
    }

    public function colorBlindMode()
    {
        return UserAccessibilitySetting::COLOR_BLIND_MODE;
    }

    public function fontFamily()
    {
        return UserAccessibilitySetting::FONT_FAMILY;
    }

    public function fontSizeMultiplier()
    {
        return UserAccessibilitySetting::FONT_SIZE_MULTIPLIER;
    }

    public function lineHeightMultiplier()
    {
        return UserAccessibilitySetting::LINE_HEIGHT_MULTIPLIER;
    }

    public function letterSpacing()
    {
        return UserAccessibilitySetting::LETTER_SPACING;
    }
    // functions
}
