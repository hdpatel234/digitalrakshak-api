<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1/auth')->middleware('throttle:100,1')->group(function () {
    Route::post('login', [App\Http\Controllers\Api\Auth\LoginController::class, 'login']);
    Route::post('forgot-password', [App\Http\Controllers\Api\Auth\LoginController::class, 'forgotPassword']);
    Route::post('reset-password', [App\Http\Controllers\Api\Auth\LoginController::class, 'resetPassword']);
    Route::post('verify-email/{token}', [App\Http\Controllers\Api\Auth\LoginController::class, 'verifyEmail']);
    Route::post('register', [App\Http\Controllers\Api\Auth\LoginController::class, 'register']);

    Route::prefix('social-login')->group(function () {
        // Route::prefix('google')->group(function () ;
        // });
        // Route::prefix('facebook')->group(function () {
        //     Route::post('/', [App\Http\Controllers\Api\Auth\SocialLoginController::class, 'facebookLogin']);
        // });
        Route::prefix('digilocker')->group(function () {
            Route::post('/', [App\Http\Controllers\Api\Auth\SocialLoginController::class, 'digiLockerLogin']);
            Route::post('/callback', [App\Http\Controllers\Api\Auth\SocialLoginController::class, 'digiLockerCallback']);
        });
    });

    // Route::post('2fa/enable', [App\Http\Controllers\Api\Auth\LoginController::class, 'enableTwoFactor']);
    // Route::post('2fa/verify', [App\Http\Controllers\Api\Auth\LoginController::class, 'verifyTwoFactor']);

    Route::prefix('public')->group(function () {
        Route::get('countries', [App\Http\Controllers\Api\Auth\CountryController::class, 'index']);
        Route::get('states', [App\Http\Controllers\Api\Auth\StateController::class, 'index']);
        Route::get('cities', [App\Http\Controllers\Api\Auth\CityController::class, 'index']);
        Route::get('postal-codes', [App\Http\Controllers\Api\Auth\PostalCodeController::class, 'index']);
    });

    Route::middleware(['auth:api'])->group(function () {
        Route::post('refresh-token', [App\Http\Controllers\Api\Auth\RefreshTokenController::class, 'refreshToken']);
        Route::post('logout', [App\Http\Controllers\Api\Auth\LogoutController::class, 'logout']);
        Route::post('logout-all', [App\Http\Controllers\Api\Auth\LogoutController::class, 'logoutAll']);

        Route::get('me', [App\Http\Controllers\Api\Auth\ProfileController::class, 'me']);
        Route::post('profile', [App\Http\Controllers\Api\Auth\ProfileController::class, 'updateProfile']);

        Route::post('change-password', [App\Http\Controllers\Api\Auth\ChangePasswordController::class, 'changePassword']);

        Route::get('permissions', [App\Http\Controllers\Api\Auth\ProfileController::class, 'getPermissions']);
        // Route::get('notifications', [App\Http\Controllers\Api\Auth\LoginController::class, 'index']);
        // Route::post('notifications/{id}/read', [App\Http\Controllers\Api\Auth\LoginController::class, 'markAsRead']);
        // Route::post('notifications/read-all', [App\Http\Controllers\Api\Auth\LoginController::class, 'markAllAsRead']);

        Route::get('config', [App\Http\Controllers\Api\Auth\UserConfigController::class, 'index']);
        Route::post('config', [App\Http\Controllers\Api\Auth\UserConfigController::class, 'store']);

        Route::get('countries', [App\Http\Controllers\Api\Auth\CountryController::class, 'index']);
        Route::get('states', [App\Http\Controllers\Api\Auth\StateController::class, 'index']);
        Route::get('cities', [App\Http\Controllers\Api\Auth\CityController::class, 'index']);
        Route::get('postal-codes', [App\Http\Controllers\Api\Auth\PostalCodeController::class, 'index']);
    });
});
