<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

Route::prefix('v1/admin')->middleware(['auth:api', 'role:super_admin|admin', 'throttle:60,1'])->group(function () {

    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [Controller::class, 'stats']);
        Route::get('/revenue-chart', [Controller::class, 'revenueChart']);
        Route::get('/recent-activities', [Controller::class, 'recentActivities']);
    });
    
    // Global Search
    Route::get('/search', [App\Http\Controllers\Api\Admin\SearchController::class, 'search']);


    // Client Management
    Route::prefix('clients/{client}')->group(function () {
        Route::post('/toggle-status', [Controller::class, 'toggleStatus']);
        Route::get('/stats', [Controller::class, 'getStats']);
        Route::get('/orders', [Controller::class, 'getOrders']);
        Route::get('/invoices', [Controller::class, 'getInvoices']);
        Route::get('/pricing', [Controller::class, 'getClientPricing']);
        Route::post('/pricing', [Controller::class, 'setPricing']);
        Route::get('/pricing/history', [Controller::class, 'history']);
        Route::get('/api-keys', [Controller::class, 'index']);
        Route::post('/api-keys', [Controller::class, 'store']);

        // User Management
        Route::get('/users', [Controller::class, 'getUsers']);
        Route::post('/users', [Controller::class, 'addUser']);
        Route::put('/users/{user}', [Controller::class, 'updateUser']);
        Route::delete('/users/{user}', [Controller::class, 'removeUser']);
    });

    // Client standalone routes
    Route::prefix('clients')->group(function () {
        Route::get('/pricing', [Controller::class, 'index']);
        Route::put('/pricing/{pricing}', [Controller::class, 'update']);
        Route::delete('/pricing/{pricing}', [Controller::class, 'destroy']);
        Route::put('/api-keys/{key}', [Controller::class, 'update']);
        Route::delete('/api-keys/{key}', [Controller::class, 'destroy']);
    });

    Route::apiResource('clients', App\Http\Controllers\Api\Admin\ClientController::class);

    // Service Management
    Route::prefix('services')->group(function () {
        Route::prefix('{service}')->group(function () {
            Route::post('/toggle-status', [Controller::class, 'toggleStatus']);
            Route::get('/fields', [Controller::class, 'getFields']);
            Route::post('/fields', [Controller::class, 'addField']);
            Route::get('/processing-rules', [Controller::class, 'getProcessingRules']);
            Route::post('/processing-rules', [Controller::class, 'createProcessingRule']);
            Route::get('/providers', [Controller::class, 'index']);
            Route::post('/providers', [Controller::class, 'assign']);
        });

        // Field Management (standalone routes)
        Route::put('/fields/{field}', [Controller::class, 'updateField']);
        Route::delete('/fields/{field}', [Controller::class, 'deleteField']);
        Route::post('/fields/reorder', [Controller::class, 'reorderFields']);

        // Processing Rules (standalone routes)
        Route::put('/processing-rules/{rule}', [Controller::class, 'updateProcessingRule']);
        Route::delete('/processing-rules/{rule}', [Controller::class, 'deleteProcessingRule']);
        Route::post('/processing-rules/{rule}/test', [Controller::class, 'testProcessingRule']);

        // Service-Provider Assignments (standalone routes)
        Route::put('/providers/{assignment}', [Controller::class, 'update']);
        Route::delete('/providers/{assignment}', [Controller::class, 'remove']);
        Route::post('/providers/reorder', [Controller::class, 'reorder']);

        // Field Mappings
        Route::prefix('providers/{assignment}')->group(function () {
            Route::get('/field-mappings', [Controller::class, 'index']);
            Route::post('/field-mappings', [Controller::class, 'store']);
            Route::get('/response-mappings', [Controller::class, 'index']);
            Route::post('/response-mappings', [Controller::class, 'store']);
        });

        Route::put('/providers/field-mappings/{mapping}', [Controller::class, 'update']);
        Route::delete('/providers/field-mappings/{mapping}', [Controller::class, 'destroy']);
        Route::put('/providers/response-mappings/{mapping}', [Controller::class, 'update']);
        Route::delete('/providers/response-mappings/{mapping}', [Controller::class, 'destroy']);
    });

    Route::apiResource('services', App\Http\Controllers\Api\Admin\ServiceController::class);

    // Package Management (Admin Packages)
    Route::prefix('packages/{package}')->group(function () {
        Route::get('/services', [App\Http\Controllers\Api\Admin\PackageController::class, 'getServices']);
        Route::post('/services', [App\Http\Controllers\Api\Admin\PackageController::class, 'addService']);
        Route::put('/services/{service}', [App\Http\Controllers\Api\Admin\PackageController::class, 'updateService']);
        Route::delete('/services/{service}', [App\Http\Controllers\Api\Admin\PackageController::class, 'removeService']);
        Route::post('/duplicate', [App\Http\Controllers\Api\Admin\PackageController::class, 'duplicate']);
        Route::post('/toggle-status', [App\Http\Controllers\Api\Admin\PackageController::class, 'toggleStatus']);
    });

    Route::apiResource('packages', App\Http\Controllers\Api\Admin\PackageController::class);

    // Orders Management (Global)
    Route::get('orders', [App\Http\Controllers\Api\Admin\OrderController::class, 'index']);

    // Invoices Management (Global)
    Route::get('invoices', [App\Http\Controllers\Api\Admin\InvoiceController::class, 'index']);

    // Transactions Management (Global)
    Route::get('transactions', [App\Http\Controllers\Api\Admin\TransactionController::class, 'index']);

    // Candidates Management (Global)
    Route::get('candidates', [App\Http\Controllers\Api\Admin\CandidateController::class, 'index']);

    // Client Packages
    Route::prefix('client-packages')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\Admin\ClientPackageController::class, 'index']);
        Route::post('/{id}/toggle-status', [App\Http\Controllers\Api\Admin\ClientPackageController::class, 'toggleStatus']);
        Route::delete('/{id}', [App\Http\Controllers\Api\Admin\ClientPackageController::class, 'destroy']);
    });

    // Billing Platform Configuration
    Route::prefix('billing')->group(function () {
        Route::get('/platforms', [App\Http\Controllers\Api\Admin\BillingPlatformController::class, 'index']);
        Route::get('/platforms/{platform}', [App\Http\Controllers\Api\Admin\BillingPlatformController::class, 'show']);
        Route::post('/platforms', [App\Http\Controllers\Api\Admin\BillingPlatformController::class, 'store']);
        Route::put('/platforms/{platform}', [App\Http\Controllers\Api\Admin\BillingPlatformController::class, 'update']);
        Route::delete('/platforms/{platform}', [App\Http\Controllers\Api\Admin\BillingPlatformController::class, 'destroy']);
        Route::post('/platforms/{platform}/toggle-status', [App\Http\Controllers\Api\Admin\BillingPlatformController::class, 'toggleStatus']);
        Route::post('/platforms/{platform}/toggle-default', [App\Http\Controllers\Api\Admin\BillingPlatformController::class, 'toggleDefault']);

        Route::get('/platforms/{platform}/configs', [App\Http\Controllers\Api\Admin\BillingPlatformConfigController::class, 'index']);
        Route::post('/platforms/{platform}/configs', [App\Http\Controllers\Api\Admin\BillingPlatformConfigController::class, 'store']);
        Route::put('/platforms/{platform}/configs/{config}', [App\Http\Controllers\Api\Admin\BillingPlatformConfigController::class, 'update']);
        Route::delete('/platforms/{platform}/configs/{config}', [App\Http\Controllers\Api\Admin\BillingPlatformConfigController::class, 'destroy']);
        
        Route::get('/refunds', [App\Http\Controllers\Api\Admin\RefundController::class, 'index']);
    });

    // Support Platform Configuration
    Route::prefix('support')->group(function () {
        Route::get('/platforms', [Controller::class, 'index']);
        Route::post('/platforms', [Controller::class, 'store']);
        Route::put('/platforms/{platform}', [Controller::class, 'update']);
        Route::delete('/platforms/{platform}', [Controller::class, 'destroy']);
        
        // Support Tickets
        Route::get('/tickets', [\App\Http\Controllers\Api\Admin\SupportTicketController::class, 'index']);
        Route::get('/tickets/{ticket}', [\App\Http\Controllers\Api\Admin\SupportTicketController::class, 'show']);
        Route::get('/tickets/{ticket}/conversations', [\App\Http\Controllers\Api\Admin\SupportTicketController::class, 'conversations']);
        Route::post('/tickets/{ticket}/reply', [\App\Http\Controllers\Api\Admin\SupportTicketController::class, 'reply']);
    });

    // Email Templates
    Route::prefix('email-templates/{template}')->group(function () {
        Route::post('/duplicate', [Controller::class, 'duplicate']);
        Route::post('/test', [Controller::class, 'test']);
    });

    Route::apiResource('email-templates', Controller::class);

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/revenue', [Controller::class, 'revenue']);
        Route::get('/orders', [Controller::class, 'orders']);
        Route::get('/services', [Controller::class, 'services']);
        Route::get('/clients', [Controller::class, 'clients']);
        Route::get('/candidates', [Controller::class, 'candidates']);
        Route::get('/processing-times', [Controller::class, 'processingTimes']);
        Route::get('/export/{type}', [Controller::class, 'export']);
    });

    // Provider CRUD
    Route::prefix('providers/{provider}')->group(function () {
        Route::post('/toggle-status', [Controller::class, 'toggleStatus']);
        Route::get('/stats', [Controller::class, 'stats']);
        Route::get('/configs', [Controller::class, 'index']);
        Route::post('/configs', [Controller::class, 'store']);
        Route::get('/costs', [Controller::class, 'index']);
        Route::post('/costs', [Controller::class, 'store']);
    });

    Route::apiResource('providers', Controller::class);

    // Provider standalone routes
    Route::prefix('providers')->group(function () {
        Route::put('/configs/{config}', [Controller::class, 'update']);
        Route::delete('/configs/{config}', [Controller::class, 'destroy']);
        Route::post('/configs/{config}/test', [Controller::class, 'testConnection']);
        Route::put('/costs/{cost}', [Controller::class, 'update']);

        // Provider Monitoring
        Route::prefix('monitoring')->group(function () {
            Route::get('/health', [Controller::class, 'health']);
            Route::get('/performance', [Controller::class, 'performance']);
            Route::get('/outages', [Controller::class, 'outages']);
            Route::get('/costs', [Controller::class, 'costs']);
        });
    });

    // System Monitoring
    Route::prefix('system')->group(function () {
        Route::get('/health', [Controller::class, 'health']);
        Route::get('/queue-stats', [Controller::class, 'queueStats']);
        Route::get('/failed-jobs', [Controller::class, 'failedJobs']);
        Route::post('/failed-jobs/{id}/retry', [Controller::class, 'retryJob']);
        Route::delete('/failed-jobs/{id}', [Controller::class, 'deleteFailedJob']);
        Route::get('/cron-jobs', [Controller::class, 'cronJobs']);
        Route::post('/cron-jobs/{job}/run', [Controller::class, 'runCronJob']);
    });

    // Audit Logs
    Route::prefix('audit-logs')->group(function () {
        Route::get('/', [Controller::class, 'index']);
        Route::get('/{id}', [Controller::class, 'show']);
        Route::get('/export', [Controller::class, 'export']);
    });

    // API Management
    Route::prefix('api')->group(function () {
        Route::get('/settings', [Controller::class, 'index']);
        Route::put('/settings', [Controller::class, 'update']);

        Route::prefix('monitoring')->group(function () {
            Route::get('/overview', [Controller::class, 'overview']);
            Route::get('/clients', [Controller::class, 'clientUsage']);
            Route::get('/errors', [Controller::class, 'errorLogs']);
            Route::get('/rate-limits', [Controller::class, 'rateLimitHits']);
        });
    });

    // Webhook Monitoring
    Route::prefix('webhooks')->group(function () {
        Route::get('/monitoring', [Controller::class, 'index']);
        Route::get('/failed', [Controller::class, 'failedDeliveries']);
        Route::post('/{log}/retry', [Controller::class, 'retry']);
    });
});
