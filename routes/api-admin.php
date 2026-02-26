<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

Route::prefix('admin')->middleware(['auth:api', 'role:admin', 'permission.route'])->group(function () {
    
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
});
