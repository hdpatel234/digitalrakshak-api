<?php

namespace App\Repositories;

use App\Models\UserTheme;

class UserThemeRepository extends BaseRepository
{
    public function __construct(UserTheme $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function themeName()
    {
        return UserTheme::THEME_NAME;
    }

    public function themeCode()
    {
        return UserTheme::THEME_CODE;
    }

    public function isDefault()
    {
        return UserTheme::IS_DEFAULT;
    }

    public function isSystem()
    {
        return UserTheme::IS_SYSTEM;
    }

    public function colors()
    {
        return UserTheme::COLORS;
    }

    public function fonts()
    {
        return UserTheme::FONTS;
    }

    public function borderRadius()
    {
        return UserTheme::BORDER_RADIUS;
    }

    public function spacing()
    {
        return UserTheme::SPACING;
    }

    public function animations()
    {
        return UserTheme::ANIMATIONS;
    }

    public function backgroundImage()
    {
        return UserTheme::BACKGROUND_IMAGE;
    }

    public function customCss()
    {
        return UserTheme::CUSTOM_CSS;
    }

    public function createdBy()
    {
        return UserTheme::CREATED_BY;
    }
    // functions
}