<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;


Route::prefix('client')->middleware(['auth:api', 'role:client', 'permission.route'])->group(function () {

    // Dashboard
    Route::get('/dashboard/stats', [Controller::class, 'stats']); // Pending
    Route::get('/dashboard/recent-orders', [Controller::class, 'recentOrders']); // Pending
    Route::get('/dashboard/recent-candidates', [Controller::class, 'recentCandidates']); // Pending
    Route::get('/dashboard/credit-balance', [Controller::class, 'creditBalance']); // Pending
    Route::get('/dashboard/processing-summary', [Controller::class, 'processingSummary']); // Pending

    // Candidate Management
    Route::apiResource('candidates', Controller::class);
    Route::post('candidates/import', [Controller::class, 'import']); // Pending
    Route::get('candidates/import/{import}/status', [Controller::class, 'importStatus']); // Pending
    Route::get('candidates/import/{import}/errors', [Controller::class, 'importErrors']); // Pending
    Route::post('candidates/bulk-delete', [Controller::class, 'bulkDelete']); // Pending
    Route::post('candidates/{candidate}/toggle-status', [Controller::class, 'toggleStatus']); // Pending
    Route::get('candidates/export', [Controller::class, 'export']); // Pending

    // Candidate Invitations
    Route::post('candidates/{candidate}/invite', [Controller::class, 'invite']); // Pending
    Route::post('candidates/bulk-invite', [Controller::class, 'bulkInvite']); // Pending
    Route::get('invitations', [Controller::class, 'index']); // Pending
    Route::get('invitations/{invitation}', [Controller::class, 'show']); // Pending
    Route::post('invitations/{invitation}/resend', [Controller::class, 'resend']); // Pending
    Route::delete('invitations/{invitation}', [Controller::class, 'destroy']); // Pending
    Route::get('invitations/{invitation}/logs', [Controller::class, 'logs']); // Pending
    Route::get('invitations/stats', [Controller::class, 'stats']); // Pending

    // Package Management (Client Packages)
    Route::apiResource('packages', Controller::class)->except(['show']); // Pending
    Route::get('packages/available', [Controller::class, 'available']); // Pending
    Route::get('packages/{package}', [Controller::class, 'show']); // Pending
    Route::get('packages/{package}/services', [Controller::class, 'services']); // Pending
    Route::post('packages/{package}/duplicate', [Controller::class, 'duplicate']); // Pending

    // Service Management (for client reference)
    Route::get('services', [Controller::class, 'index']); // Pending
    Route::get('services/{service}', [Controller::class, 'show']); // Pending
    Route::get('services/{service}/fields', [Controller::class, 'fields']); // Pending
    Route::get('services/{service}/price', [Controller::class, 'getPrice']); // Pending

    // Order Management
    Route::apiResource('orders', Controller::class);
    Route::post('orders/preview', [Controller::class, 'preview']); // Pending
    Route::post('orders/{order}/confirm', [Controller::class, 'confirm']); // Pending
    Route::post('orders/{order}/cancel', [Controller::class, 'cancel']); // Pending
    Route::get('orders/{order}/summary', [Controller::class, 'summary']); // Pending
    Route::get('orders/{order}/timeline', [Controller::class, 'timeline']); // Pending
    Route::get('orders/{order}/candidates', [Controller::class, 'candidates']); // Pending
    Route::get('orders/{order}/invoice', [Controller::class, 'invoice']); // Pending
    Route::get('orders/{order}/track', [Controller::class, 'track']); // Pending
    Route::post('orders/{order}/payment', [Controller::class, 'initiatePayment']); // Pending

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
