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
        return $this->model::THEME_NAME;
    }

    public function themeCode()
    {
        return $this->model::THEME_CODE;
    }

    public function isDefault()
    {
        return $this->model::IS_DEFAULT;
    }

    public function isSystem()
    {
        return $this->model::IS_SYSTEM;
    }

    public function colors()
    {
        return $this->model::COLORS;
    }

    public function fonts()
    {
        return $this->model::FONTS;
    }

    public function borderRadius()
    {
        return $this->model::BORDER_RADIUS;
    }

    public function spacing()
    {
        return $this->model::SPACING;
    }

    public function animations()
    {
        return $this->model::ANIMATIONS;
    }

    public function backgroundImage()
    {
        return $this->model::BACKGROUND_IMAGE;
    }

    public function customCss()
    {
        return $this->model::CUSTOM_CSS;
    }

    public function createdBy()
    {
        return $this->model::CREATED_BY;
    }
    // functions
}