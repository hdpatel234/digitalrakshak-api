<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;

Route::prefix('v1/auth')->group(function () {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('forgot-password', [LoginController::class, 'forgotPassword']);
    Route::post('reset-password', [LoginController::class, 'resetPassword']);

    Route::middleware('auth:api')->group(function () {
        Route::post('refresh', [LoginController::class, 'refresh']);
        Route::post('logout', [LogoutController::class, 'logout']);
        Route::post('logout-all', [LogoutController::class, 'logoutAll']);
    });
});