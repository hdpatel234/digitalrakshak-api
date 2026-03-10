<?php

namespace App\Services;

use App\Repositories\UserThemeRepository;

class UserThemeService extends BaseService
{
    
    public function __construct(UserThemeRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function themeName()
    {
        return $this->repository->themeName();
    }

    public function themeCode()
    {
        return $this->repository->themeCode();
    }

    public function isDefault()
    {
        return $this->repository->isDefault();
    }

    public function isSystem()
    {
        return $this->repository->isSystem();
    }

    public function colors()
    {
        return $this->repository->colors();
    }

    public function fonts()
    {
        return $this->repository->fonts();
    }

    public function borderRadius()
    {
        return $this->repository->borderRadius();
    }

    public function spacing()
    {
        return $this->repository->spacing();
    }

    public function animations()
    {
        return $this->repository->animations();
    }

    public function backgroundImage()
    {
        return $this->repository->backgroundImage();
    }

    public function customCss()
    {
        return $this->repository->customCss();
    }

    public function createdBy()
    {
        return $this->repository->createdBy();
    }
    // functions
}