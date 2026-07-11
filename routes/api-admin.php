<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1/admin')->middleware(['auth:api', 'role:super_admin|admin', 'throttle:60,1'])->group(function () {

    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/overview', [App\Http\Controllers\Api\Admin\DashboardController::class, 'overview']);
    });

    // Global Search
    Route::get('/search', [App\Http\Controllers\Api\Admin\SearchController::class, 'search']);


    // Client Management
    Route::prefix('clients/{client}')->group(function () {
        Route::post('/toggle-status', [\App\Http\Controllers\Api\Admin\ClientController::class, 'toggleStatus']);
        Route::get('/stats', [App\Http\Controllers\Controller::class, 'getStats']);
        Route::get('/orders', [App\Http\Controllers\Controller::class, 'getOrders']);
        Route::get('/invoices', [App\Http\Controllers\Controller::class, 'getInvoices']);
        Route::get('/pricing', [\App\Http\Controllers\Api\Admin\ClientController::class, 'getClientPricing']);
        Route::post('/pricing', [\App\Http\Controllers\Api\Admin\ClientController::class, 'setPricing']);
        Route::get('/pricing/history', [\App\Http\Controllers\Api\Admin\ClientController::class, 'history']);
        Route::get('/api-keys', [App\Http\Controllers\Controller::class, 'index']);
        Route::post('/api-keys', [App\Http\Controllers\Controller::class, 'store']);

        // User Management
        Route::get('/users', [App\Http\Controllers\Controller::class, 'getUsers']);
        Route::post('/users', [App\Http\Controllers\Controller::class, 'addUser']);
        Route::put('/users/{user}', [App\Http\Controllers\Controller::class, 'updateUser']);
        Route::delete('/users/{user}', [App\Http\Controllers\Controller::class, 'removeUser']);
    });

    // Client standalone routes
    Route::prefix('clients')->group(function () {
        Route::get('/pricing', [App\Http\Controllers\Controller::class, 'index']);
        Route::put('/pricing/{pricing}', [App\Http\Controllers\Controller::class, 'update']);
        Route::delete('/pricing/{pricing}', [App\Http\Controllers\Controller::class, 'destroy']);
        Route::put('/api-keys/{key}', [App\Http\Controllers\Controller::class, 'update']);
        Route::delete('/api-keys/{key}', [App\Http\Controllers\Controller::class, 'destroy']);
    });

    Route::apiResource('clients', App\Http\Controllers\Api\Admin\ClientController::class);

    // Service Providers
    Route::prefix('service-providers/{service_provider}')->group(function () {
        Route::post('/toggle-status', [\App\Http\Controllers\Api\Admin\ServiceProviderController::class, 'toggleStatus']);
    });
    Route::apiResource('service-providers', \App\Http\Controllers\Api\Admin\ServiceProviderController::class);

    // Service Categories
    Route::get('/service-categories', [\App\Http\Controllers\Api\Admin\ServiceCategoryController::class, 'index']);

    // Service Management
    Route::prefix('services')->group(function () {
        Route::prefix('{service}')->group(function () {
            Route::post('/toggle-status', [App\Http\Controllers\Controller::class, 'toggleStatus']);
            Route::get('/fields', [App\Http\Controllers\Controller::class, 'getFields']);
            Route::post('/fields', [App\Http\Controllers\Controller::class, 'addField']);
            Route::get('/processing-rules', [App\Http\Controllers\Controller::class, 'getProcessingRules']);
            Route::post('/processing-rules', [App\Http\Controllers\Controller::class, 'createProcessingRule']);
            Route::get('/providers', [App\Http\Controllers\Controller::class, 'index']);
            Route::post('/providers', [App\Http\Controllers\Controller::class, 'assign']);
        });

        // Field Management (standalone routes)
        Route::put('/fields/{field}', [App\Http\Controllers\Controller::class, 'updateField']);
        Route::delete('/fields/{field}', [App\Http\Controllers\Controller::class, 'deleteField']);
        Route::post('/fields/reorder', [App\Http\Controllers\Controller::class, 'reorderFields']);

        // Processing Rules (standalone routes)
        Route::put('/processing-rules/{rule}', [App\Http\Controllers\Controller::class, 'updateProcessingRule']);
        Route::delete('/processing-rules/{rule}', [App\Http\Controllers\Controller::class, 'deleteProcessingRule']);
        Route::post('/processing-rules/{rule}/test', [App\Http\Controllers\Controller::class, 'testProcessingRule']);

        // Service-Provider Assignments (standalone routes)
        Route::put('/providers/{assignment}', [App\Http\Controllers\Controller::class, 'update']);
        Route::delete('/providers/{assignment}', [App\Http\Controllers\Controller::class, 'remove']);
        Route::post('/providers/reorder', [App\Http\Controllers\Controller::class, 'reorder']);

        // Field Mappings
        Route::prefix('providers/{assignment}')->group(function () {
            Route::get('/field-mappings', [App\Http\Controllers\Controller::class, 'index']);
            Route::post('/field-mappings', [App\Http\Controllers\Controller::class, 'store']);
            Route::get('/response-mappings', [App\Http\Controllers\Controller::class, 'index']);
            Route::post('/response-mappings', [App\Http\Controllers\Controller::class, 'store']);
        });

        Route::put('/providers/field-mappings/{mapping}', [App\Http\Controllers\Controller::class, 'update']);
        Route::delete('/providers/field-mappings/{mapping}', [App\Http\Controllers\Controller::class, 'destroy']);
        Route::put('/providers/response-mappings/{mapping}', [App\Http\Controllers\Controller::class, 'update']);
        Route::delete('/providers/response-mappings/{mapping}', [App\Http\Controllers\Controller::class, 'destroy']);
    });

    Route::apiResource('services', App\Http\Controllers\Api\Admin\ServiceController::class);
    Route::get('service-fields/sections', [\App\Http\Controllers\Api\Admin\ServiceFieldController::class, 'sections']);
    Route::get('service-fields/stats', [\App\Http\Controllers\Api\Admin\ServiceFieldController::class, 'stats']);
    Route::apiResource('service-fields', \App\Http\Controllers\Api\Admin\ServiceFieldController::class);

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
    Route::get('orders/filters', [App\Http\Controllers\Api\Admin\OrderController::class, 'filters']);

    // Invoices Management (Global)
    Route::get('invoices', [App\Http\Controllers\Api\Admin\InvoiceController::class, 'index']);

    // Transactions Management (Global)
    Route::get('transactions', [App\Http\Controllers\Api\Admin\TransactionController::class, 'index']);
    Route::get('transactions/filters', [App\Http\Controllers\Api\Admin\TransactionController::class, 'filters']);

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
        Route::get('/payment-methods', [\App\Http\Controllers\Api\Admin\BillingController::class, 'paymentMethods']);
        Route::get('/payment-gateways', [\App\Http\Controllers\Api\Admin\BillingController::class, 'paymentGateways']);
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
        Route::get('/platforms', [App\Http\Controllers\Controller::class, 'index']);
        Route::post('/platforms', [App\Http\Controllers\Controller::class, 'store']);
        Route::put('/platforms/{platform}', [App\Http\Controllers\Controller::class, 'update']);
        Route::delete('/platforms/{platform}', [App\Http\Controllers\Controller::class, 'destroy']);

        // Support Tickets
        Route::get('/tickets', [\App\Http\Controllers\Api\Admin\SupportTicketController::class, 'index']);
        Route::get('/tickets/meta/departments', [\App\Http\Controllers\Api\Admin\SupportTicketController::class, 'departments']);
        Route::get('/tickets/meta/priorities', [\App\Http\Controllers\Api\Admin\SupportTicketController::class, 'priorities']);
        Route::get('/tickets/meta/orders', [\App\Http\Controllers\Api\Admin\SupportTicketController::class, 'orders']);
        Route::post('/tickets', [\App\Http\Controllers\Api\Admin\SupportTicketController::class, 'store']);
        Route::get('/tickets/{ticket}', [\App\Http\Controllers\Api\Admin\SupportTicketController::class, 'show']);
        Route::get('/tickets/{ticket}/conversations', [\App\Http\Controllers\Api\Admin\SupportTicketController::class, 'conversations']);
        Route::post('/tickets/{ticket}/reply', [\App\Http\Controllers\Api\Admin\SupportTicketController::class, 'reply']);
    });

    // Email Templates
    Route::prefix('email-templates/{template}')->group(function () {
        Route::post('/duplicate', [App\Http\Controllers\Controller::class, 'duplicate']);
        Route::post('/test', [App\Http\Controllers\Controller::class, 'test']);
    });

    Route::apiResource('email-templates', App\Http\Controllers\Controller::class);

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/revenue', [\App\Http\Controllers\Api\Admin\ReportController::class, 'revenue']);
        Route::get('/orders', [\App\Http\Controllers\Api\Admin\ReportController::class, 'orders']);
        Route::get('/services', [\App\Http\Controllers\Api\Admin\ReportController::class, 'services']);
        Route::get('/services/filters', [\App\Http\Controllers\Api\Admin\ReportController::class, 'serviceFilters']);
        Route::get('/clients', [\App\Http\Controllers\Api\Admin\ReportController::class, 'clients']);
        Route::get('/candidates', [\App\Http\Controllers\Api\Admin\ReportController::class, 'candidates']);
        Route::get('/processing-times', [App\Http\Controllers\Controller::class, 'processingTimes']);
        Route::get('/export/{type}', [App\Http\Controllers\Controller::class, 'export']);
    });

    // Provider CRUD
    Route::prefix('providers/{provider}')->group(function () {
        Route::post('/toggle-status', [App\Http\Controllers\Controller::class, 'toggleStatus']);
        Route::get('/stats', [App\Http\Controllers\Controller::class, 'stats']);
        Route::get('/configs', [App\Http\Controllers\Controller::class, 'index']);
        Route::post('/configs', [App\Http\Controllers\Controller::class, 'store']);
        Route::get('/costs', [App\Http\Controllers\Controller::class, 'index']);
        Route::post('/costs', [App\Http\Controllers\Controller::class, 'store']);
    });

    Route::apiResource('providers', App\Http\Controllers\Controller::class);

    // Provider standalone routes
    Route::prefix('providers')->group(function () {
        Route::put('/configs/{config}', [App\Http\Controllers\Controller::class, 'update']);
        Route::delete('/configs/{config}', [App\Http\Controllers\Controller::class, 'destroy']);
        Route::post('/configs/{config}/test', [App\Http\Controllers\Controller::class, 'testConnection']);
        Route::put('/costs/{cost}', [App\Http\Controllers\Controller::class, 'update']);

        // Provider Monitoring
        Route::prefix('monitoring')->group(function () {
            Route::get('/health', [App\Http\Controllers\Controller::class, 'health']);
            Route::get('/performance', [App\Http\Controllers\Controller::class, 'performance']);
            Route::get('/outages', [App\Http\Controllers\Controller::class, 'outages']);
            Route::get('/costs', [App\Http\Controllers\Controller::class, 'costs']);
        });
    });

    // System Monitoring
    Route::prefix('system')->group(function () {
        Route::get('/health', [App\Http\Controllers\Controller::class, 'health']);
        Route::get('/queue-stats', [App\Http\Controllers\Controller::class, 'queueStats']);
        Route::get('/failed-jobs', [App\Http\Controllers\Controller::class, 'failedJobs']);
        Route::post('/failed-jobs/{id}/retry', [App\Http\Controllers\Controller::class, 'retryJob']);
        Route::delete('/failed-jobs/{id}', [App\Http\Controllers\Controller::class, 'deleteFailedJob']);
        Route::get('/cron-jobs', [App\Http\Controllers\Controller::class, 'cronJobs']);
        Route::post('/cron-jobs/{job}/run', [App\Http\Controllers\Controller::class, 'runCronJob']);
        Route::get('/email/overview', [App\Http\Controllers\Api\Admin\SystemEmailController::class, 'overview']);
        Route::get('/email/templates', [App\Http\Controllers\Api\Admin\SystemEmailController::class, 'templates']);
        Route::get('/email/server-types', [\App\Http\Controllers\Api\Admin\SystemEmailServerController::class, 'types']);
        Route::get('/email/server-types/{id}/fields', [\App\Http\Controllers\Api\Admin\SystemEmailServerController::class, 'getServerTypeFields']);
        Route::get('/email/servers/statuses', [\App\Http\Controllers\Api\Admin\SystemEmailServerController::class, 'statuses']);
        Route::apiResource('/email/servers', \App\Http\Controllers\Api\Admin\SystemEmailServerController::class);
        Route::post('/email/servers/{server}/test', [\App\Http\Controllers\Api\Admin\SystemEmailServerController::class, 'testConnection']);
        Route::post('/email/servers/{server}/send-test', [\App\Http\Controllers\Api\Admin\SystemEmailServerController::class, 'sendTestEmail']);
        
        Route::get('/email/queue/stats', [\App\Http\Controllers\Api\Admin\SystemEmailQueueController::class, 'stats']);
        Route::get('/email/queue', [\App\Http\Controllers\Api\Admin\SystemEmailQueueController::class, 'index']);
        Route::post('/email/queue/{source}/{id}/retry', [\App\Http\Controllers\Api\Admin\SystemEmailQueueController::class, 'retry']);
    });

    // Audit Logs
    Route::prefix('audit-logs')->group(function () {
        Route::get('/', [App\Http\Controllers\Controller::class, 'index']);
        Route::get('/{id}', [App\Http\Controllers\Controller::class, 'show']);
        Route::get('/export', [App\Http\Controllers\Controller::class, 'export']);
    });

    // API Management
    Route::prefix('api')->group(function () {
        Route::get('/settings', [App\Http\Controllers\Controller::class, 'index']);
        Route::put('/settings', [App\Http\Controllers\Controller::class, 'update']);

        Route::prefix('monitoring')->group(function () {
            Route::get('/overview', [App\Http\Controllers\Controller::class, 'overview']);
            Route::get('/clients', [App\Http\Controllers\Controller::class, 'clientUsage']);
            Route::get('/errors', [App\Http\Controllers\Controller::class, 'errorLogs']);
            Route::get('/rate-limits', [App\Http\Controllers\Controller::class, 'rateLimitHits']);
        });
    });

    // Webhook Monitoring
    Route::prefix('webhooks')->group(function () {
        Route::get('/monitoring', [App\Http\Controllers\Controller::class, 'index']);
        Route::get('/failed', [App\Http\Controllers\Controller::class, 'failedDeliveries']);
        Route::post('/{log}/retry', [App\Http\Controllers\Controller::class, 'retry']);
    });
});
