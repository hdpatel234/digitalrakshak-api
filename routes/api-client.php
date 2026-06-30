<?php

use App\Http\Controllers\Api\Client\Billing\BillingController;
use App\Http\Controllers\Api\Client\Candidate\CandidatesController;
use App\Http\Controllers\Api\Client\Company\CompanyController;
use App\Http\Controllers\Api\Client\Invitation\CandidateInvitationController;
use App\Http\Controllers\Api\Client\Invoice\InvoiceController;
use App\Http\Controllers\Api\Client\Members\MemberController;
use App\Http\Controllers\Api\Client\Order\OrderController;
use App\Http\Controllers\Api\Client\Package\PackageController;
use App\Http\Controllers\Api\Client\Service\ServicesController;
use App\Http\Controllers\Api\Client\Support\SupportTicketController;
use App\Http\Controllers\Api\Client\ProteanController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;


Route::prefix('v1/client')->middleware(['auth:api', 'role:client_admin|client_user', 'throttle:100,1'])->group(function () {

    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('data', [DashboardController::class, 'index']);
        Route::get('stats', [Controller::class, 'stats']); // Pending
        Route::get('recent-orders', [Controller::class, 'recentOrders']); // Pending
        Route::get('recent-candidates', [Controller::class, 'recentCandidates']); // Pending
        Route::get('credit-balance', [Controller::class, 'creditBalance']); // Pending
        Route::get('processing-summary', [Controller::class, 'processingSummary']); // Pending
    });

    // Candidate Management
    Route::prefix('candidates')->group(function () {
        Route::get('import/sample', [CandidatesController::class, 'importSample']);
        Route::post('import', [CandidatesController::class, 'import']);
        Route::get('imports', [CandidatesController::class, 'imports']);
        Route::post('bulk-delete', [CandidatesController::class, 'bulkDelete']); // Pending
        Route::get('export', [CandidatesController::class, 'export']); // Pending
        Route::post('{candidate}/invite', [CandidateInvitationController::class, 'invite']);
        Route::post('bulk-invite', [CandidateInvitationController::class, 'store']);
        Route::apiResource('', CandidatesController::class)->parameters(['' => 'candidate']);
    });

    // Candidate Invitations
    Route::prefix('invitations')->group(function () {
        Route::get('', [CandidateInvitationController::class, 'index']);
        Route::get('{invitation_token}', [CandidateInvitationController::class, 'showByToken'])->withoutMiddleware(['auth:api', 'role:client_admin|client_user']);
        Route::post('{invitation_token}', [CandidateInvitationController::class, 'updateByToken'])->withoutMiddleware(['auth:api', 'role:client_admin|client_user']);
        Route::post('{invitation_token}/parse-resume', [CandidateInvitationController::class, 'parseResume'])->withoutMiddleware(['auth:api', 'role:client_admin|client_user']);
        Route::post('{invitation}/resend', [CandidateInvitationController::class, 'resend']); // Pending
        Route::delete('{invitation}', [CandidateInvitationController::class, 'destroy']); // Pending
        Route::get('{invitation}/logs', [CandidateInvitationController::class, 'logs']); // Pending
        Route::get('stats', [CandidateInvitationController::class, 'stats']); // Pending
    });

    // Package Management (Client Packages)
    Route::prefix('packages')->group(function () {
        Route::get('', [PackageController::class, 'index']);
        Route::post('', [PackageController::class, 'store']);
        Route::get('available', [PackageController::class, 'available']); // Pending
        Route::get('{package}', [PackageController::class, 'show']);
        Route::put('{package}', [PackageController::class, 'update']);
        Route::delete('{package}', [PackageController::class, 'destroy']);
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
        Route::apiResource('', OrderController::class)->except(['show']);
        Route::get('{order}', [OrderController::class, 'show']);
        Route::post('preview', [OrderController::class, 'preview']); // Pending
        Route::post('{order}/confirm', [OrderController::class, 'confirm']); // Pending
        Route::post('{order}/cancel', [OrderController::class, 'cancel']); // Pending
        Route::get('{order}/summary', [OrderController::class, 'summary']); // Pending
        Route::get('{order}/timeline', [OrderController::class, 'timeline']); // Pending
        Route::get('{order}/candidates', [OrderController::class, 'candidates']); // Pending
        Route::get('{order}/invoice', [OrderController::class, 'invoice']); // Pending
        Route::get('{order}/track', [OrderController::class, 'track']); // Pending
        Route::post('{order}/payment', [OrderController::class, 'initiatePayment']);
        Route::post('{order}/payment/complete', [OrderController::class, 'completePayment']);
    });

    // Candidate Services (Verification Status)
    Route::prefix('candidates')->group(function () {
        Route::get('{candidate}/services', [Controller::class, 'candidateServices']); // Pending
        Route::get('{candidate}/services/{service}', [Controller::class, 'showCandidateService']); // Pending
        Route::get('{candidate}/services/{service}/details', [Controller::class, 'getCandidateServiceDetails']); // Pending
        Route::get('{candidate}/services/{service}/timeline', [Controller::class, 'getCandidateServiceTimeline']); // Pending
    });

    // Invoices
    Route::prefix('invoices')->group(function () {
        Route::get('', [InvoiceController::class, 'index']);
        Route::get('{invoice}', [InvoiceController::class, 'show']);
        Route::get('{invoice}/pdf', [InvoiceController::class, 'downloadPdf']);
        Route::get('{invoice}/payment-history', [InvoiceController::class, 'paymentHistory']); // Pending
    });

    // Billing
    Route::prefix('billing')->group(function () {
        Route::get('summary', [BillingController::class, 'summary']); // Pending
        Route::get('transactions', [BillingController::class, 'transactions']);
        Route::get('credit-history', [BillingController::class, 'creditHistory']); // Pending
        Route::post('add-credit', [BillingController::class, 'addCredit']); // Pending
        Route::get('payment-methods', [BillingController::class, 'paymentMethods']);
        Route::get('payment-gateways', [BillingController::class, 'paymentGateways']);
        Route::get('{payment_method}/payment-gateways', [BillingController::class, 'paymentGatewaysByMethod']);
    });

    // Support Tickets
    Route::prefix('tickets')->group(function () {
        Route::apiResource('', SupportTicketController::class)->except(['show']);
        Route::get('departments', [SupportTicketController::class, 'departments']);
        Route::get('priorities', [SupportTicketController::class, 'priorities']);
        Route::get('{ticket}', [SupportTicketController::class, 'show']);
        Route::get('{ticket}/conversations', [SupportTicketController::class, 'conversations']);
        Route::post('{ticket}/reply', [SupportTicketController::class, 'reply']);
    });

    // Reports
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
        Route::get('company', [CompanyController::class, 'index']);
        Route::put('company', [CompanyController::class, 'update']);
        Route::get('users', [MemberController::class, 'index']);
        Route::post('users', [MemberController::class, 'store']);
        Route::get('users/{user}', [MemberController::class, 'show']);
        Route::put('users/{user}', [MemberController::class, 'update']);
        Route::delete('users/{user}', [MemberController::class, 'destroy']);
        Route::get('notifications/preferences', [Controller::class, 'notificationPreferences']); // Pending
        Route::put('notifications/preferences', [Controller::class, 'updateNotificationPreferences']); // Pending
    });

    // Protean Verification APIs
    Route::prefix('protean')->group(function () {
        Route::post('silent-verify', [ProteanController::class, 'silentVerify']);
        Route::post('generate-otp', [ProteanController::class, 'generateOtp']);
        Route::post('geo-fencing', [ProteanController::class, 'geoFencing']);
        Route::post('reverse-geocode', [ProteanController::class, 'reverseGeocode']);
        Route::post('kyc-ocr', [ProteanController::class, 'kycOcr']);
        Route::post('bank-verify', [ProteanController::class, 'bankVerify']);
        Route::post('bank-verify-amount', [ProteanController::class, 'bankVerifyAmount']);
        Route::post('shop-estab', [ProteanController::class, 'shopEstablishment']);
        Route::post('epf-uan', [ProteanController::class, 'epfUan']);
    });
});
