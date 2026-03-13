<?php

use App\Http\Controllers\Api\Client\Candidate\CandidatesController;
use App\Http\Controllers\Api\Client\Invitation\CandidateInvitationController;
use App\Http\Controllers\Api\Client\Order\OrderController;
use App\Http\Controllers\Api\Client\Package\PackageController;
use App\Http\Controllers\Api\Client\Service\ServicesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;


Route::prefix('v1/client')->middleware(['auth:api', 'role:client_admin|client_user'])->group(function () {

    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('stats', [Controller::class, 'stats']); // Pending
        Route::get('recent-orders', [Controller::class, 'recentOrders']); // Pending
        Route::get('recent-candidates', [Controller::class, 'recentCandidates']); // Pending
        Route::get('credit-balance', [Controller::class, 'creditBalance']); // Pending
        Route::get('processing-summary', [Controller::class, 'processingSummary']); // Pending
    });

    // Candidate Management
    Route::prefix('candidates')->group(function () {
        Route::apiResource('', CandidatesController::class);
        Route::get('import/sample', [CandidatesController::class, 'importSample']);
        Route::post('import', [CandidatesController::class, 'import']);
        Route::get('imports', [CandidatesController::class, 'imports']);
        Route::post('bulk-delete', [CandidatesController::class, 'bulkDelete']); // Pending
        Route::get('export', [CandidatesController::class, 'export']); // Pending
        Route::post('{candidate}/invite', [CandidateInvitationController::class, 'invite']);
        Route::post('bulk-invite', [CandidateInvitationController::class, 'store']);
    });

    // Candidate Invitations
    Route::prefix('invitations')->group(function () {
        Route::get('', [CandidateInvitationController::class, 'index']);
        Route::get('{invitation_token}', [CandidateInvitationController::class, 'showByToken'])->withoutMiddleware(['auth:api', 'role:client_admin|client_user']);
        Route::post('{invitation_token}', [CandidateInvitationController::class, 'updateByToken'])->withoutMiddleware(['auth:api', 'role:client_admin|client_user']);
        Route::post('{invitation}/resend', [CandidateInvitationController::class, 'resend']); // Pending
        Route::delete('{invitation}', [CandidateInvitationController::class, 'destroy']); // Pending
        Route::get('{invitation}/logs', [CandidateInvitationController::class, 'logs']); // Pending
        Route::get('stats', [CandidateInvitationController::class, 'stats']); // Pending
    });

    // Package Management (Client Packages)
    Route::prefix('packages')->group(function () {
        Route::get('', [PackageController::class, 'index']);
        Route::apiResource('', PackageController::class)->except(['index', 'show']); // Pending
        Route::get('available', [PackageController::class, 'available']); // Pending
        Route::get('{package}', [PackageController::class, 'show']);
        Route::get('{package}/services', [PackageController::class, 'services']);
        Route::get('{package}/candidates', [PackageController::class, 'candidates']);
        Route::post('{package}/duplicate', [PackageController::class, 'duplicate']); // Pending
    });

    // Service Management (for client reference)
    Route::prefix('services')->group(function () {
        Route::get('', [ServicesController::class, 'index']);
        Route::get('{service}', [ServicesController::class, 'show']); // Pending
        Route::get('{service}/fields', [ServicesController::class, 'fields']); // Pending
        Route::get('{service}/price', [ServicesController::class, 'getPrice']); // Pending
    });

    // Order Management
    Route::prefix('orders')->group(function () {
        Route::apiResource('', OrderController::class);
        Route::post('preview', [OrderController::class, 'preview']); // Pending
        Route::post('{order}/confirm', [OrderController::class, 'confirm']); // Pending
        Route::post('{order}/cancel', [OrderController::class, 'cancel']); // Pending
        Route::get('{order}/summary', [OrderController::class, 'summary']); // Pending
        Route::get('{order}/timeline', [OrderController::class, 'timeline']); // Pending
        Route::get('{order}/candidates', [OrderController::class, 'candidates']); // Pending
        Route::get('{order}/invoice', [OrderController::class, 'invoice']); // Pending
        Route::get('{order}/track', [OrderController::class, 'track']); // Pending
        Route::post('{order}/payment', [OrderController::class, 'initiatePayment']); // Pending
    });

    // Candidate Services (Verification Status)
    Route::get('candidates/{candidate}/services', [Controller::class, 'candidateServices']); // Pending
    Route::get('candidates/{candidate}/services/{service}', [Controller::class, 'showCandidateService']); // Pending
    Route::get('candidates/{candidate}/services/{service}/details', [Controller::class, 'getCandidateServiceDetails']); // Pending
    Route::get('candidates/{candidate}/services/{service}/timeline', [Controller::class, 'getCandidateServiceTimeline']); // Pending

    // Billing & Invoices
    Route::get('invoices', [Controller::class, 'index']); // Pending
    Route::get('invoices/{invoice}', [Controller::class, 'show']); // Pending
    Route::get('invoices/{invoice}/pdf', [Controller::class, 'downloadPdf']); // Pending
    Route::get('invoices/{invoice}/payment-history', [Controller::class, 'paymentHistory']); // Pending
    Route::get('billing/summary', [Controller::class, 'summary']); // Pending
    Route::get('billing/transactions', [Controller::class, 'transactions']); // Pending
    Route::get('billing/credit-history', [Controller::class, 'creditHistory']); // Pending
    Route::post('billing/add-credit', [Controller::class, 'addCredit']); // Pending
    Route::get('billing/payment-methods', [Controller::class, 'paymentMethods']); // Pending

    // Support Tickets (via UVdesk or other)
    Route::apiResource('tickets', Controller::class);
    Route::post('tickets/{ticket}/reply', [Controller::class, 'reply']); // Pending
    Route::post('tickets/{ticket}/close', [Controller::class, 'close']); // Pending
    Route::post('tickets/{ticket}/reopen', [Controller::class, 'reopen']); // Pending
    Route::get('tickets/{ticket}/conversations', [Controller::class, 'conversations']); // Pending
    Route::post('tickets/{ticket}/attachments', [Controller::class, 'uploadAttachment']); // Pending

    // Reports (Client-specific)
    Route::prefix('reports')->group(function () {
        Route::get('spending', [Controller::class, 'spending']); // Pending
        Route::get('orders', [Controller::class, 'orders']); // Pending
        Route::get('candidates', [Controller::class, 'candidates']); // Pending
        Route::get('verification-status', [Controller::class, 'verificationStatus']); // Pending
        Route::get('turnaround-time', [Controller::class, 'turnaroundTime']); // Pending
        Route::get('export/{type}', [Controller::class, 'export']); // Pending
    });

    // API Management
    Route::prefix('api')->group(function () {
        Route::get('keys', [Controller::class, 'index']); // Pending
        Route::post('keys', [Controller::class, 'store']); // Pending
        Route::get('keys/{key}', [Controller::class, 'show']); // Pending
        Route::put('keys/{key}', [Controller::class, 'update']); // Pending
        Route::delete('keys/{key}', [Controller::class, 'destroy']); // Pending
        Route::post('keys/{key}/revoke', [Controller::class, 'revoke']); // Pending
        Route::post('keys/{key}/regenerate', [Controller::class, 'regenerate']); // Pending

        Route::get('stats', [Controller::class, 'index']); // Pending
        Route::get('stats/daily', [Controller::class, 'daily']); // Pending
        Route::get('stats/endpoints', [Controller::class, 'topEndpoints']); // Pending
        Route::get('logs', [Controller::class, 'logs']); // Pending
        Route::get('logs/{log}', [Controller::class, 'showLog']); // Pending

        Route::get('quota', [Controller::class, 'current']); // Pending
        Route::get('quota/history', [Controller::class, 'history']); // Pending
    });

    // Webhooks
    Route::prefix('webhooks')->group(function () {
        Route::get('', [Controller::class, 'index']); // Pending
        Route::post('', [Controller::class, 'store']); // Pending
        Route::get('/{webhook}', [Controller::class, 'show']); // Pending
        Route::put('/{webhook}', [Controller::class, 'update']); // Pending
        Route::delete('/{webhook}', [Controller::class, 'destroy']); // Pending
        Route::post('/{webhook}/test', [Controller::class, 'test']); // Pending
        Route::post('/{webhook}/toggle', [Controller::class, 'toggle']); // Pending

        Route::get('events', [Controller::class, 'availableEvents']); // Pending
        Route::get('events/{category}', [Controller::class, 'eventsByCategory']); // Pending

        Route::get('{webhook}/logs', [Controller::class, 'index']); // Pending
        Route::get('{webhook}/logs/{log}', [Controller::class, 'show']); // Pending
        Route::post('{webhook}/logs/{log}/retry', [Controller::class, 'retry']); // Pending

        Route::get('stats', [Controller::class, 'index']); // Pending
        Route::get('stats/delivery', [Controller::class, 'deliveryStats']); // Pending
    });

    // Settings
    Route::prefix('settings')->group(function () {
        Route::get('profile', [Controller::class, 'profile']); // Pending
        Route::put('profile', [Controller::class, 'updateProfile']); // Pending
        Route::get('company', [Controller::class, 'company']); // Pending
        Route::put('company', [Controller::class, 'updateCompany']); // Pending
        Route::get('users', [Controller::class, 'users']); // Pending
        Route::post('users', [Controller::class, 'addUser']); // Pending
        Route::put('users/{user}', [Controller::class, 'updateUser']); // Pending
        Route::delete('users/{user}', [Controller::class, 'removeUser']); // Pending
        Route::get('notifications/preferences', [Controller::class, 'notificationPreferences']); // Pending
        Route::put('notifications/preferences', [Controller::class, 'updateNotificationPreferences']); // Pending
        Route::get('billing/config', [Controller::class, 'billingConfig']); // Pending
        Route::get('support/config', [Controller::class, 'supportConfig']); // Pending
    });
});
