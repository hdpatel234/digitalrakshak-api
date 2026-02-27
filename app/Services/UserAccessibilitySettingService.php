<?php

namespace App\Services;

use App\Repositories\UserAccessibilitySettingRepository;

class UserAccessibilitySettingService extends BaseService
{
    protected $repository;
    
    public function __construct(UserAccessibilitySettingRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function userId()
    {
        return $this->repository->userId();
    }

    public function highContrast()
    {
        return $this->repository->highContrast();
    }

    public function largeText()
    {
        return $this->repository->largeText();
    }

    public function reduceMotion()
    {
        return $this->repository->reduceMotion();
    }

    public function screenReaderOptimized()
    {
        return $this->repository->screenReaderOptimized();
    }

    public function keyboardNavigation()
    {
        return $this->repository->keyboardNavigation();
    }

    public function focusIndicators()
    {
        return $this->repository->focusIndicators();
    }

    public function colorBlindMode()
    {
        return $this->repository->colorBlindMode();
    }

    public function fontFamily()
    {
        return $this->repository->fontFamily();
    }

    public function fontSizeMultiplier()
    {
        return $this->repository->fontSizeMultiplier();
    }

    public function lineHeightMultiplier()
    {
        return $this->repository->lineHeightMultiplier();
    }

    public function letterSpacing()
    {
        return $this->repository->letterSpacing();
    }
    // functions
}