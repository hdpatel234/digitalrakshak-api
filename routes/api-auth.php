<?php

use App\Http\Controllers\Api\Auth\ChangePasswordController;
use App\Http\Controllers\Api\Auth\CityController;
use App\Http\Controllers\Api\Auth\CountryController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\ProfileController;
use App\Http\Controllers\Api\Auth\RefreshTokenController;
use App\Http\Controllers\Api\Auth\SocialLoginController;
use App\Http\Controllers\Api\Auth\StateController;
use App\Http\Controllers\Api\Auth\UserConfigController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/auth')->middleware('throttle:100,1')->group(function () {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('forgot-password', [LoginController::class, 'forgotPassword']);
    Route::post('reset-password', [LoginController::class, 'resetPassword']);
    Route::post('verify-email/{token}', [LoginController::class, 'verifyEmail']);
    Route::post('register', [LoginController::class, 'register']);

    Route::prefix('social-login')->group(function () {
        // Route::prefix('google')->group(function () {
        //     Route::post('/', [SocialLoginController::class, 'googleLogin']);
        // });
        // Route::prefix('facebook')->group(function () {
        //     Route::post('/', [SocialLoginController::class, 'facebookLogin']);
        // });
        Route::prefix('digilocker')->group(function () {
            Route::post('/', [SocialLoginController::class, 'digiLockerLogin']);
            Route::post('/callback', [SocialLoginController::class, 'digiLockerCallback']);
        });
    });

    // Route::post('2fa/enable', [LoginController::class, 'enableTwoFactor']);
    // Route::post('2fa/verify', [LoginController::class, 'verifyTwoFactor']);

    Route::prefix('public')->group(function () {
        Route::get('countries', [CountryController::class, 'index']);
        Route::get('states', [StateController::class, 'index']);
        Route::get('cities', [CityController::class, 'index']);
    });

    Route::middleware(['auth:api'])->group(function () {
        Route::post('refresh-token', [RefreshTokenController::class, 'refreshToken']);
        Route::post('logout', [LogoutController::class, 'logout']);
        Route::post('logout-all', [LogoutController::class, 'logoutAll']);

        Route::get('me', [ProfileController::class, 'me']);
        Route::post('profile', [ProfileController::class, 'updateProfile']);

        Route::post('change-password', [ChangePasswordController::class, 'changePassword']);

        // Route::get('permissions', [LoginController::class, 'getPermissions']);
        // Route::get('notifications', [LoginController::class, 'index']);
        // Route::post('notifications/{id}/read', [LoginController::class, 'markAsRead']);
        // Route::post('notifications/read-all', [LoginController::class, 'markAllAsRead']);

        Route::get('config', [UserConfigController::class, 'index']);
        Route::post('config', [UserConfigController::class, 'store']);

        Route::get('countries', [CountryController::class, 'index']);
        Route::get('states', [StateController::class, 'index']);
        Route::get('cities', [CityController::class, 'index']);
    });
});
