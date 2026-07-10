<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1/client')->middleware(['auth:api', 'role:client_admin|client_user', 'throttle:100,1'])->group(function () {

    // Global Search
    Route::get('search', [\App\Http\Controllers\Api\Client\SearchController::class, 'index']);
    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('data', [App\Http\Controllers\Api\DashboardController::class, 'index']);
        Route::get('stats', [App\Http\Controllers\Controller::class, 'stats']);
        Route::get('recent-orders', [App\Http\Controllers\Controller::class, 'recentOrders']);
        Route::get('recent-candidates', [App\Http\Controllers\Controller::class, 'recentCandidates']);
        Route::get('credit-balance', [App\Http\Controllers\Controller::class, 'creditBalance']);
        Route::get('processing-summary', [App\Http\Controllers\Controller::class, 'processingSummary']);
    });

    // Candidate Management
    Route::prefix('candidates')->group(function () {
        Route::get('import/sample', [App\Http\Controllers\Api\Client\Candidate\CandidatesController::class, 'importSample']);
        Route::post('import', [App\Http\Controllers\Api\Client\Candidate\CandidatesController::class, 'import']);
        Route::get('imports', [App\Http\Controllers\Api\Client\Candidate\CandidatesController::class, 'imports']);
        Route::post('bulk-delete', [App\Http\Controllers\Api\Client\Candidate\CandidatesController::class, 'bulkDelete']);
        Route::get('export', [App\Http\Controllers\Api\Client\Candidate\CandidatesController::class, 'export']);
        Route::get('{candidate}/report', [App\Http\Controllers\Api\Client\Candidate\CandidatesController::class, 'downloadReport']);
        Route::post('{candidate}/invite', [App\Http\Controllers\Api\Client\Invitation\CandidateInvitationController::class, 'invite']);
        Route::post('bulk-invite', [App\Http\Controllers\Api\Client\Invitation\CandidateInvitationController::class, 'store']);
        Route::apiResource('', App\Http\Controllers\Api\Client\Candidate\CandidatesController::class)->parameters(['' => 'candidate']);
    });

    // Candidate Invitations
    Route::prefix('invitations')->group(function () {
        Route::get('', [App\Http\Controllers\Api\Client\Invitation\CandidateInvitationController::class, 'index']);
        Route::get('{invitation_token}', [App\Http\Controllers\Api\Client\Invitation\CandidateInvitationController::class, 'showByToken'])->withoutMiddleware(['auth:api', 'role:client_admin|client_user']);
        Route::post('{invitation_token}', [App\Http\Controllers\Api\Client\Invitation\CandidateInvitationController::class, 'updateByToken'])->withoutMiddleware(['auth:api', 'role:client_admin|client_user']);
        Route::post('{invitation_token}/parse-resume', [App\Http\Controllers\Api\Client\Invitation\CandidateInvitationController::class, 'parseResume'])->withoutMiddleware(['auth:api', 'role:client_admin|client_user']);
        Route::post('{invitation}/resend', [App\Http\Controllers\Api\Client\Invitation\CandidateInvitationController::class, 'resend']);
        Route::delete('{invitation}', [App\Http\Controllers\Api\Client\Invitation\CandidateInvitationController::class, 'destroy']);
        Route::get('{invitation}/logs', [App\Http\Controllers\Api\Client\Invitation\CandidateInvitationController::class, 'logs']);
        Route::get('stats', [App\Http\Controllers\Api\Client\Invitation\CandidateInvitationController::class, 'stats']);
    });

    // Package Management (Client Packages)
    Route::prefix('packages')->group(function () {
        Route::get('', [App\Http\Controllers\Api\Client\Package\PackageController::class, 'index']);
        Route::post('', [App\Http\Controllers\Api\Client\Package\PackageController::class, 'store']);
        Route::get('available', [App\Http\Controllers\Api\Client\Package\PackageController::class, 'available']);
        Route::get('{package}', [App\Http\Controllers\Api\Client\Package\PackageController::class, 'show']);
        Route::put('{package}', [App\Http\Controllers\Api\Client\Package\PackageController::class, 'update']);
        Route::delete('{package}', [App\Http\Controllers\Api\Client\Package\PackageController::class, 'destroy']);
        Route::get('{package}/services', [App\Http\Controllers\Api\Client\Package\PackageController::class, 'services']);
        Route::get('{package}/candidates', [App\Http\Controllers\Api\Client\Package\PackageController::class, 'candidates']);
        Route::post('{package}/duplicate', [App\Http\Controllers\Api\Client\Package\PackageController::class, 'duplicate']);
    });

    // Service Management (for client reference)
    Route::prefix('services')->group(function () {
        Route::get('', [App\Http\Controllers\Api\Client\Service\ServicesController::class, 'index']);
        Route::get('{service}', [App\Http\Controllers\Api\Client\Service\ServicesController::class, 'show']);
        Route::get('{service}/fields', [App\Http\Controllers\Api\Client\Service\ServicesController::class, 'fields']);
        Route::get('{service}/price', [App\Http\Controllers\Api\Client\Service\ServicesController::class, 'getPrice']);
    });

    // Order Management
    Route::prefix('orders')->group(function () {
        Route::apiResource('', App\Http\Controllers\Api\Client\Order\OrderController::class)->except(['show']);
        Route::get('{order}', [App\Http\Controllers\Api\Client\Order\OrderController::class, 'show']);
        Route::post('preview', [App\Http\Controllers\Api\Client\Order\OrderController::class, 'preview']);
        Route::post('{order}/confirm', [App\Http\Controllers\Api\Client\Order\OrderController::class, 'confirm']);
        Route::post('{order}/cancel', [App\Http\Controllers\Api\Client\Order\OrderController::class, 'cancel']);
        Route::get('{order}/summary', [App\Http\Controllers\Api\Client\Order\OrderController::class, 'summary']);
        Route::get('{order}/timeline', [App\Http\Controllers\Api\Client\Order\OrderController::class, 'timeline']);
        Route::get('{order}/candidates', [App\Http\Controllers\Api\Client\Order\OrderController::class, 'candidates']);
        Route::get('{order}/invoice', [App\Http\Controllers\Api\Client\Order\OrderController::class, 'invoice']);
        Route::get('{order}/track', [App\Http\Controllers\Api\Client\Order\OrderController::class, 'track']);
        Route::post('{order}/payment', [App\Http\Controllers\Api\Client\Order\OrderController::class, 'initiatePayment']);
        Route::post('{order}/payment/complete', [App\Http\Controllers\Api\Client\Order\OrderController::class, 'completePayment']);
    });

    // Candidate Services (Verification Status)
    Route::prefix('candidates')->group(function () {
        Route::get('{candidate}/services', [App\Http\Controllers\Controller::class, 'candidateServices']);
        Route::get('{candidate}/services/{service}', [App\Http\Controllers\Controller::class, 'showCandidateService']);
        Route::get('{candidate}/services/{service}/details', [App\Http\Controllers\Controller::class, 'getCandidateServiceDetails']);
        Route::get('{candidate}/services/{service}/timeline', [App\Http\Controllers\Controller::class, 'getCandidateServiceTimeline']);
    });

    // Invoices
    Route::prefix('invoices')->group(function () {
        Route::get('', [App\Http\Controllers\Api\Client\Invoice\InvoiceController::class, 'index']);
        Route::get('{invoice}', [App\Http\Controllers\Api\Client\Invoice\InvoiceController::class, 'show']);
        Route::get('{invoice}/pdf', [App\Http\Controllers\Api\Client\Invoice\InvoiceController::class, 'downloadPdf']);
        Route::get('{invoice}/payment-history', [App\Http\Controllers\Api\Client\Invoice\InvoiceController::class, 'paymentHistory']);
    });

    // Billing
    Route::prefix('billing')->group(function () {
        Route::get('summary', [App\Http\Controllers\Api\Client\Billing\BillingController::class, 'summary']);
        Route::get('transactions', [App\Http\Controllers\Api\Client\Billing\BillingController::class, 'transactions']);
        Route::get('credit-history', [App\Http\Controllers\Api\Client\Billing\BillingController::class, 'creditHistory']);
        Route::post('add-credit', [App\Http\Controllers\Api\Client\Billing\BillingController::class, 'addCredit']);
        Route::get('payment-methods', [App\Http\Controllers\Api\Client\Billing\BillingController::class, 'paymentMethods']);
        Route::get('payment-gateways', [App\Http\Controllers\Api\Client\Billing\BillingController::class, 'paymentGateways']);
        Route::get('{payment_method}/payment-gateways', [App\Http\Controllers\Api\Client\Billing\BillingController::class, 'paymentGatewaysByMethod']);
    });

    // Support Tickets
    Route::prefix('tickets')->group(function () {
        Route::apiResource('', App\Http\Controllers\Api\Client\Support\SupportTicketController::class)->except(['show']);
        Route::get('departments', [App\Http\Controllers\Api\Client\Support\SupportTicketController::class, 'departments']);
        Route::get('priorities', [App\Http\Controllers\Api\Client\Support\SupportTicketController::class, 'priorities']);
        Route::get('{ticket}', [App\Http\Controllers\Api\Client\Support\SupportTicketController::class, 'show']);
        Route::get('{ticket}/conversations', [App\Http\Controllers\Api\Client\Support\SupportTicketController::class, 'conversations']);
        Route::post('{ticket}/reply', [App\Http\Controllers\Api\Client\Support\SupportTicketController::class, 'reply']);
    });

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('spending', [App\Http\Controllers\Controller::class, 'spending']);
        Route::get('orders', [App\Http\Controllers\Controller::class, 'orders']);
        Route::get('candidates', [App\Http\Controllers\Controller::class, 'candidates']);
        Route::get('verification-status', [App\Http\Controllers\Controller::class, 'verificationStatus']);
        Route::get('turnaround-time', [App\Http\Controllers\Controller::class, 'turnaroundTime']);
        Route::get('export/{type}', [App\Http\Controllers\Controller::class, 'export']);
    });

    // API Management
    Route::prefix('api')->group(function () {
        Route::get('keys', [App\Http\Controllers\Controller::class, 'index']);
        Route::post('keys', [App\Http\Controllers\Controller::class, 'store']);
        Route::get('keys/{key}', [App\Http\Controllers\Controller::class, 'show']);
        Route::put('keys/{key}', [App\Http\Controllers\Controller::class, 'update']);
        Route::delete('keys/{key}', [App\Http\Controllers\Controller::class, 'destroy']);
        Route::post('keys/{key}/revoke', [App\Http\Controllers\Controller::class, 'revoke']);
        Route::post('keys/{key}/regenerate', [App\Http\Controllers\Controller::class, 'regenerate']);

        Route::get('stats', [App\Http\Controllers\Controller::class, 'index']);
        Route::get('stats/daily', [App\Http\Controllers\Controller::class, 'daily']);
        Route::get('stats/endpoints', [App\Http\Controllers\Controller::class, 'topEndpoints']);
        Route::get('logs', [App\Http\Controllers\Controller::class, 'logs']);
        Route::get('logs/{log}', [App\Http\Controllers\Controller::class, 'showLog']);

        Route::get('quota', [App\Http\Controllers\Controller::class, 'current']);
        Route::get('quota/history', [App\Http\Controllers\Controller::class, 'history']);
    });

    // Webhooks
    Route::prefix('webhooks')->group(function () {
        Route::get('', [App\Http\Controllers\Controller::class, 'index']);
        Route::post('', [App\Http\Controllers\Controller::class, 'store']);
        Route::get('/{webhook}', [App\Http\Controllers\Controller::class, 'show']);
        Route::put('/{webhook}', [App\Http\Controllers\Controller::class, 'update']);
        Route::delete('/{webhook}', [App\Http\Controllers\Controller::class, 'destroy']);
        Route::post('/{webhook}/test', [App\Http\Controllers\Controller::class, 'test']);
        Route::post('/{webhook}/toggle', [App\Http\Controllers\Controller::class, 'toggle']);

        Route::get('events', [App\Http\Controllers\Controller::class, 'availableEvents']);
        Route::get('events/{category}', [App\Http\Controllers\Controller::class, 'eventsByCategory']);

        Route::get('{webhook}/logs', [App\Http\Controllers\Controller::class, 'index']);
        Route::get('{webhook}/logs/{log}', [App\Http\Controllers\Controller::class, 'show']);
        Route::post('{webhook}/logs/{log}/retry', [App\Http\Controllers\Controller::class, 'retry']);

        Route::get('stats', [App\Http\Controllers\Controller::class, 'index']);
        Route::get('stats/delivery', [App\Http\Controllers\Controller::class, 'deliveryStats']);
    });

    // Settings
    Route::prefix('settings')->group(function () {
        Route::get('company', [App\Http\Controllers\Api\Client\Company\CompanyController::class, 'index']);
        Route::put('company', [App\Http\Controllers\Api\Client\Company\CompanyController::class, 'update']);
        Route::get('users', [App\Http\Controllers\Api\Client\Members\MemberController::class, 'index']);
        Route::post('users', [App\Http\Controllers\Api\Client\Members\MemberController::class, 'store']);
        Route::get('users/{user}', [App\Http\Controllers\Api\Client\Members\MemberController::class, 'show']);
        Route::put('users/{user}', [App\Http\Controllers\Api\Client\Members\MemberController::class, 'update']);
        Route::delete('users/{user}', [App\Http\Controllers\Api\Client\Members\MemberController::class, 'destroy']);
        Route::get('notifications/preferences', [App\Http\Controllers\Controller::class, 'notificationPreferences']);
        Route::put('notifications/preferences', [App\Http\Controllers\Controller::class, 'updateNotificationPreferences']);
    });

    // Protean Verification APIs
    Route::prefix('protean')->group(function () {
        Route::post('silent-verify', [App\Http\Controllers\Api\Client\ProteanController::class, 'silentVerify']);
        Route::post('generate-otp', [App\Http\Controllers\Api\Client\ProteanController::class, 'generateOtp']);
        Route::post('geo-fencing', [App\Http\Controllers\Api\Client\ProteanController::class, 'geoFencing']);
        Route::post('reverse-geocode', [App\Http\Controllers\Api\Client\ProteanController::class, 'reverseGeocode']);
        Route::post('kyc-ocr', [App\Http\Controllers\Api\Client\ProteanController::class, 'kycOcr']);
        Route::post('bank-verify', [App\Http\Controllers\Api\Client\ProteanController::class, 'bankVerify']);
        Route::post('bank-verify-amount', [App\Http\Controllers\Api\Client\ProteanController::class, 'bankVerifyAmount']);
        Route::post('shop-estab', [App\Http\Controllers\Api\Client\ProteanController::class, 'shopEstablishment']);
        Route::post('epf-uan', [App\Http\Controllers\Api\Client\ProteanController::class, 'epfUan']);
    });
});

// Public Verification Routes
Route::prefix('v1/verifications')->group(function () {
    Route::get('employment/{token}', [\App\Http\Controllers\Api\EmploymentVerificationController::class, 'show']);
    Route::post('employment/{token}', [\App\Http\Controllers\Api\EmploymentVerificationController::class, 'verify']);
});
