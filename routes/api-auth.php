<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RefreshTokenController;
use App\Http\Controllers\Api\Auth\UserConfigController;

Route::prefix('v1/auth')->group(function () {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('forgot-password', [LoginController::class, 'forgotPassword']);
    Route::post('reset-password', [LoginController::class, 'resetPassword']);
    Route::post('/verify-email/{token}', [LoginController::class, 'verifyEmail']); // Pending
    Route::post('register', [LoginController::class, 'register']); // Pending
    Route::post('social-login/{provider}', [LoginController::class, 'socialLogin']); // Pending

    Route::post('2fa/enable', [LoginController::class, 'enableTwoFactor']); // Pending
    Route::post('2fa/verify', [LoginController::class, 'verifyTwoFactor']); // Pending

    Route::middleware(['auth:api'])->group(function () {
        Route::post('refresh-token', [RefreshTokenController::class, 'refreshToken']);
        Route::post('logout', [LogoutController::class, 'logout']);
        Route::post('logout-all', [LogoutController::class, 'logoutAll']);

        Route::get('me', [LoginController::class, 'me']); // Pending
        Route::put('profile', [LoginController::class, 'updateProfile']); // Pending
        Route::post('change-password', [LoginController::class, 'changePassword']); // Pending
        Route::get('permissions', [LoginController::class, 'getPermissions']); // Pending
        Route::get('notifications', [LoginController::class, 'index']); // Pending
        Route::put('notifications/{id}/read', [LoginController::class, 'markAsRead']); // Pending
        Route::put('notifications/read-all', [LoginController::class, 'markAllAsRead']); // Pending

        Route::get('config', [UserConfigController::class,'index']);
        Route::post('config' , [UserConfigController::class,'store']);
    });
});
