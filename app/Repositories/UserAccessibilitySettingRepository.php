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
        return $this->model::USER_ID;
    }

    public function highContrast()
    {
        return $this->model::HIGH_CONTRAST;
    }

    public function largeText()
    {
        return $this->model::LARGE_TEXT;
    }

    public function reduceMotion()
    {
        return $this->model::REDUCE_MOTION;
    }

    public function screenReaderOptimized()
    {
        return $this->model::SCREEN_READER_OPTIMIZED;
    }

    public function keyboardNavigation()
    {
        return $this->model::KEYBOARD_NAVIGATION;
    }

    public function focusIndicators()
    {
        return $this->model::FOCUS_INDICATORS;
    }

    public function colorBlindMode()
    {
        return $this->model::COLOR_BLIND_MODE;
    }

    public function fontFamily()
    {
        return $this->model::FONT_FAMILY;
    }

    public function fontSizeMultiplier()
    {
        return $this->model::FONT_SIZE_MULTIPLIER;
    }

    public function lineHeightMultiplier()
    {
        return $this->model::LINE_HEIGHT_MULTIPLIER;
    }

    public function letterSpacing()
    {
        return $this->model::LETTER_SPACING;
    }
    // functions
}