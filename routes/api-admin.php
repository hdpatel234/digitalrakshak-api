<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

Route::prefix('v1/admin')->middleware(['auth:api', 'role:super_admin|admin', 'permission.route'])->group(function () {

    // Dashboard
    Route::get('/dashboard/stats', [Controller::class, 'stats']); // Pending
    Route::get('/dashboard/revenue-chart', [Controller::class, 'revenueChart']); // Pending
    Route::get('/dashboard/recent-activities', [Controller::class, 'recentActivities']); // Pending

    // Client Management
    Route::apiResource('clients', Controller::class);
    Route::post('clients/{client}/toggle-status', [Controller::class, 'toggleStatus']); // Pending
    Route::get('clients/{client}/stats', [Controller::class, 'getStats']); // Pending
    Route::get('clients/{client}/orders', [Controller::class, 'getOrders']); // Pending
    Route::get('clients/{client}/invoices', [Controller::class, 'getInvoices']); // Pending
    Route::get('clients/{client}/users', [Controller::class, 'getUsers']); // Pending
    Route::post('clients/{client}/users', [Controller::class, 'addUser']); // Pending
    Route::put('clients/{client}/users/{user}', [Controller::class, 'updateUser']); // Pending
    Route::delete('clients/{client}/users/{user}', [Controller::class, 'removeUser']); // Pending

    // Service Management
    Route::apiResource('services', Controller::class);
    Route::post('services/{service}/toggle-status', [Controller::class, 'toggleStatus']); // Pending
    Route::get('services/{service}/fields', [Controller::class, 'getFields']); // Pending
    Route::post('services/{service}/fields', [Controller::class, 'addField']); // Pending
    Route::put('services/fields/{field}', [Controller::class, 'updateField']); // Pending
    Route::delete('services/fields/{field}', [Controller::class, 'deleteField']); // Pending
    Route::post('services/fields/reorder', [Controller::class, 'reorderFields']); // Pending

    // Service Processing Rules
    Route::get('services/{service}/processing-rules', [Controller::class, 'getProcessingRules']); // Pending
    Route::post('services/{service}/processing-rules', [Controller::class, 'createProcessingRule']); // Pending
    Route::put('services/processing-rules/{rule}', [Controller::class, 'updateProcessingRule']); // Pending
    Route::delete('services/processing-rules/{rule}', [Controller::class, 'deleteProcessingRule']); // Pending
    Route::post('services/processing-rules/{rule}/test', [Controller::class, 'testProcessingRule']); // Pending

    // Package Management (Admin Packages)
    Route::apiResource('packages', Controller::class);
    Route::get('packages/{package}/services', [Controller::class, 'getServices']); // Pending
    Route::post('packages/{package}/services', [Controller::class, 'addService']); // Pending
    Route::put('packages/{package}/services/{service}', [Controller::class, 'updateService']); // Pending
    Route::delete('packages/{package}/services/{service}', [Controller::class, 'removeService']); // Pending
    Route::post('packages/{package}/duplicate', [Controller::class, 'duplicate']); // Pending
    Route::post('packages/{package}/toggle-status', [Controller::class, 'toggleStatus']); // Pending

    // Client Service Pricing
    Route::get('client-pricing', [Controller::class, 'index']); // Pending
    Route::get('clients/{client}/pricing', [Controller::class, 'getClientPricing']); // Pending
    Route::post('clients/{client}/pricing', [Controller::class, 'setPricing']); // Pending
    Route::put('clients/pricing/{pricing}', [Controller::class, 'update']); // Pending
    Route::delete('clients/pricing/{pricing}', [Controller::class, 'destroy']); // Pending
    Route::get('clients/{client}/pricing/history', [Controller::class, 'history']); // Pending

    // Billing Platform Configuration
    Route::get('billing/platforms', [Controller::class, 'index']); // Pending
    Route::post('billing/platforms', [Controller::class, 'store']); // Pending
    Route::put('billing/platforms/{platform}', [Controller::class, 'update']); // Pending
    Route::delete('billing/platforms/{platform}', [Controller::class, 'destroy']); // Pending

    // Support Platform Configuration
    Route::get('support/platforms', [Controller::class, 'index']); // Pending
    Route::post('support/platforms', [Controller::class, 'store']); // Pending
    Route::put('support/platforms/{platform}', [Controller::class, 'update']); // Pending
    Route::delete('support/platforms/{platform}', [Controller::class, 'destroy']); // Pending

    // Email Templates
    Route::apiResource('email-templates', Controller::class);
    Route::post('email-templates/{template}/duplicate', [Controller::class, 'duplicate']); // Pending
    Route::post('email-templates/{template}/test', [Controller::class, 'test']); // Pending

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('revenue', [Controller::class, 'revenue']); // Pending
        Route::get('orders', [Controller::class, 'orders']); // Pending
        Route::get('services', [Controller::class, 'services']); // Pending
        Route::get('clients', [Controller::class, 'clients']); // Pending
        Route::get('candidates', [Controller::class, 'candidates']); // Pending
        Route::get('processing-times', [Controller::class, 'processingTimes']); // Pending
        Route::get('export/{type}', [Controller::class, 'export']); // Pending
    });

    // Provider CRUD
    Route::apiResource('providers', Controller::class); // Pending
    Route::post('providers/{provider}/toggle-status', [Controller::class, 'toggleStatus']); // Pending
    Route::get('providers/{provider}/stats', [Controller::class, 'stats']); // Pending

    // Provider API Configs
    Route::get('providers/{provider}/configs', [Controller::class, 'index']); // Pending
    Route::post('providers/{provider}/configs', [Controller::class, 'store']); // Pending
    Route::put('providers/configs/{config}', [Controller::class, 'update']); // Pending
    Route::delete('providers/configs/{config}', [Controller::class, 'destroy']); // Pending
    Route::post('providers/configs/{config}/test', [Controller::class, 'testConnection']); // Pending

    // Service-Provider Assignments
    Route::get('services/{service}/providers', [Controller::class, 'index']); // Pending
    Route::post('services/{service}/providers', [Controller::class, 'assign']); // Pending
    Route::put('services/providers/{assignment}', [Controller::class, 'update']); // Pending
    Route::delete('services/providers/{assignment}', [Controller::class, 'remove']); // Pending
    Route::post('services/providers/reorder', [Controller::class, 'reorder']); // Pending

    // Field Mappings
    Route::get('services/providers/{assignment}/field-mappings', [Controller::class, 'index']); // Pending
    Route::post('services/providers/{assignment}/field-mappings', [Controller::class, 'store']); // Pending
    Route::put('services/providers/field-mappings/{mapping}', [Controller::class, 'update']); // Pending
    Route::delete('services/providers/field-mappings/{mapping}', [Controller::class, 'destroy']); // Pending

    // Response Mappings
    Route::get('services/providers/{assignment}/response-mappings', [Controller::class, 'index']); // Pending
    Route::post('services/providers/{assignment}/response-mappings', [Controller::class, 'store']); // Pending
    Route::put('services/providers/response-mappings/{mapping}', [Controller::class, 'update']);  // Pending
    Route::delete('services/providers/response-mappings/{mapping}', [Controller::class, 'destroy']); // Pending

    // Provider Monitoring
    Route::get('providers/monitoring/health', [Controller::class, 'health']); // Pending
    Route::get('providers/monitoring/performance', [Controller::class, 'performance']); // Pending
    Route::get('providers/monitoring/outages', [Controller::class, 'outages']); // Pending
    Route::get('providers/monitoring/costs', [Controller::class, 'costs']); // Pending

    // Provider Costs
    Route::get('providers/{provider}/costs', [Controller::class, 'index']); // Pending
    Route::post('providers/{provider}/costs', [Controller::class, 'store']); // Pending
    Route::put('providers/costs/{cost}', [Controller::class, 'update']); // Pending

    // System Monitoring
    Route::get('system/health', [Controller::class, 'health']); // Pending
    Route::get('system/queue-stats', [Controller::class, 'queueStats']); // Pending
    Route::get('system/failed-jobs', [Controller::class, 'failedJobs']); // Pending
    Route::post('system/failed-jobs/{id}/retry', [Controller::class, 'retryJob']); // Pending
    Route::delete('system/failed-jobs/{id}', [Controller::class, 'deleteFailedJob']); // Pending
    Route::get('system/cron-jobs', [Controller::class, 'cronJobs']); // Pending
    Route::post('system/cron-jobs/{job}/run', [Controller::class, 'runCronJob']); // Pending

    // Audit Logs
    Route::get('audit-logs', [Controller::class, 'index']); // Pending
    Route::get('audit-logs/{id}', [Controller::class, 'show']); // Pending
    Route::get('audit-logs/export', [Controller::class, 'export']); // Pending

    // Client API Configuration
    Route::get('clients/{client}/api-keys', [Controller::class, 'index']); // Pending
    Route::post('clients/{client}/api-keys', [Controller::class, 'store']); // Pending
    Route::put('clients/api-keys/{key}', [Controller::class, 'update']); // Pending
    Route::delete('clients/api-keys/{key}', [Controller::class, 'destroy']); // Pending

    // Global API Settings
    Route::get('api/settings', [Controller::class, 'index']); // Pending
    Route::put('api/settings', [Controller::class, 'update']); // Pending

    // API Monitoring
    Route::get('api/monitoring/overview', [Controller::class, 'overview']); // Pending
    Route::get('api/monitoring/clients', [Controller::class, 'clientUsage']); // Pending
    Route::get('api/monitoring/errors', [Controller::class, 'errorLogs']); // Pending
    Route::get('api/monitoring/rate-limits', [Controller::class, 'rateLimitHits']); // Pending

    // Webhook Monitoring
    Route::get('webhooks/monitoring', [Controller::class, 'index']); // Pending
    Route::get('webhooks/failed', [Controller::class, 'failedDeliveries']); // Pending
    Route::post('webhooks/{log}/retry', [Controller::class, 'retry']); // Pending
});
