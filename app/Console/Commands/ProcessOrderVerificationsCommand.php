<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ServiceFieldName;
use App\Services\OrderService;
use App\Services\CandidateServiceService;
use App\Services\CandidateServiceDataService;
use App\Services\ProteanService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessOrderVerificationsCommand extends Command
{
    protected $signature = 'orders:process-verifications';
    protected $description = 'Process pending candidate verifications (Bank Account and EPF) using Protean APIs';

    public function __construct(
        protected ProteanService $proteanService,
        protected OrderService $candidateOrderService,
        protected CandidateServiceService $candidateServiceService,
        protected CandidateServiceDataService $candidateServiceDataService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Starting order verifications processing...');

        // 1. Fetch all pending or retry candidate services under paid orders
        $candidateServices = $this->candidateServiceService->query()
            ->whereIn($this->candidateServiceService->processingStatus(), ['pending', 'retry'])
            ->whereHas('order', function ($query) {
                $query->where($this->candidateOrderService->paymentStatus(), PaymentStatus::PAID->value)
                    ->whereIn($this->candidateOrderService->status(), [OrderStatus::PROCESSING->value, OrderStatus::COMPLETED->value]);
            })
            ->with(['candidate'])
            ->get();

        if ($candidateServices->isEmpty()) {
            $this->info('No pending candidate services found for paid orders.');
            return self::SUCCESS;
        }

        $this->info(sprintf('Found %d candidate services to process.', $candidateServices->count()));

        $processedOrders = [];

        foreach ($candidateServices as $candidateService) {
            $serviceId = (int) $candidateService->{$this->candidateServiceService->serviceId()};
            $orderId = (int) $candidateService->{$this->candidateServiceService->orderId()};

            $this->info(sprintf('Processing Service ID: %d for Candidate ID: %d, Order ID: %d', $serviceId, $candidateService->{$this->candidateServiceService->candidateId()}, $orderId));

            // Set state to processing
            $candidateService->update([
                $this->candidateServiceService->processingStatus() => 'processing',
                $this->candidateServiceService->processingAttempts() => $candidateService->{$this->candidateServiceService->processingAttempts()} + 1,
            ]);

            try {
                if ($serviceId === 2) {
                    $this->processBankAccountVerification($candidateService);
                } elseif ($serviceId === 5) {
                    $this->processEpfVerification($candidateService);
                } else {
                    // Mark as completed or skipped if not an auto-verifiable service
                    $candidateService->update([
                        $this->candidateServiceService->processingStatus() => 'completed',
                        $this->candidateServiceService->processedAt() => now(),
                        $this->candidateServiceService->completedAt() => now(),
                    ]);
                    $this->warn("Skipped Service ID {$serviceId} (no automatic API handler configured).");
                }

                $processedOrders[$orderId] = true;
            } catch (\Exception $e) {
                Log::error("Failed to process candidate service ID {$candidateService->{$this->candidateServiceService->id()}}: " . $e->getMessage());
                $candidateService->update([
                    $this->candidateServiceService->processingStatus() => 'failed',
                    $this->candidateServiceService->errorMessage() => $e->getMessage(),
                ]);
                $this->error("Error for Service ID {$candidateService->{$this->candidateServiceService->id()}}: " . $e->getMessage());
            }
        }

        // 2. Check if all services in processed orders are completed and update order status
        foreach (array_keys($processedOrders) as $orderId) {
            $orderServices = $this->candidateServiceService->query()->where($this->candidateServiceService->orderId(), $orderId)->get();
            $allCompleted = $orderServices->every(fn($item) => $item->{$this->candidateServiceService->processingStatus()} === 'completed');

            if ($allCompleted) {
                $this->candidateOrderService->query()->where($this->candidateOrderService->id(), $orderId)->update([
                    $this->candidateOrderService->status() => OrderStatus::COMPLETED->value,
                    $this->candidateOrderService->completedAt() => now(),
                ]);
                $this->info("Order ID {$orderId} has been successfully completed.");
            }
        }

        $this->info('Order verifications processing finished.');
        return self::SUCCESS;
    }

    protected function processBankAccountVerification($candidateService): void
    {
        // Get all input fields for this candidate service
        $dataRows = $this->candidateServiceDataService->query()->where($this->candidateServiceDataService->candidateServiceId(), $candidateService->{$this->candidateServiceService->id()})
            ->with(['field' => function ($q) {
                $q->select('id', 'field_name');
            }])
            ->get();

        $account = null;
        $ifsc = null;
        $accountFieldRow = null;
        $ifscFieldRow = null;

        // Fetch inputs from mapped fields
        foreach ($dataRows as $row) {
            $fieldName = $row->field?->field_name ?? '';
            if ($fieldName === ServiceFieldName::BENEFICIARY_ACCOUNT->value) {
                $account = trim((string) $row->{$this->candidateServiceDataService->fieldValue()});
                $accountFieldRow = $row;
            } elseif ($fieldName === ServiceFieldName::BENEFICIARY_IFSC->value) {
                $ifsc = trim((string) $row->{$this->candidateServiceDataService->fieldValue()});
                $ifscFieldRow = $row;
            }
        }

        if (!$account || !$ifsc) {
            throw new \Exception("Missing beneficiary_account or beneficiary_ifsc input data.");
        }

        $this->info("Calling Protean Bank Verify API with Account: $account and IFSC: $ifsc");

        $startTime = microtime(true);
        $apiResult = null;
        $error = null;

        try {
            $apiResult = $this->proteanService->bankVerify($account, $ifsc);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            throw $e;
        } finally {
            $durationMs = (int) round((microtime(true) - $startTime) * 1000);
            \Illuminate\Support\Facades\DB::table('api_logs')->insert([
                'service_id' => $candidateService->{$this->candidateServiceService->serviceId()},
                'order_item_id' => $candidateService->{$this->candidateServiceService->orderItemId()},
                'endpoint' => '/api/v1/protean-variablepennydrop/bankaccountverifications/advancedverification',
                'method' => 'POST',
                'request_data' => json_encode([
                    'beneficiaryAccount' => $account,
                    'beneficiaryIFSC' => $ifsc,
                ]),
                'response_data' => json_encode($apiResult['data'] ?? $apiResult['raw'] ?? []),
                'http_status' => $apiResult['status_code'] ?? ($error ? 500 : 200),
                'status' => ($apiResult && $apiResult['success']) ? 'success' : 'failed',
                'error_message' => $error ?? $apiResult['error'] ?? null,
                'duration_ms' => $durationMs,
                'ip_address' => '127.0.0.1',
                'created_at' => now(),
            ]);
        }

        if (!$apiResult['success']) {
            throw new \Exception($apiResult['error'] ?? 'Protean Bank Verification API call failed.');
        }

        // Update both fields in DB as verified
        $isVerified = 1;

        $accountFieldRow->update([
            $this->candidateServiceDataService->isVerified() => $isVerified,
            $this->candidateServiceDataService->verifiedAt() => now(),
            $this->candidateServiceDataService->status() => 'verified',
        ]);

        $ifscFieldRow->update([
            $this->candidateServiceDataService->isVerified() => $isVerified,
            $this->candidateServiceDataService->verifiedAt() => now(),
            $this->candidateServiceDataService->status() => 'verified',
        ]);

        $candidateService->update([
            $this->candidateServiceService->processingStatus() => 'completed',
            $this->candidateServiceService->processedAt() => now(),
            $this->candidateServiceService->completedAt() => now(),
            $this->candidateServiceService->errorMessage() => null,
        ]);

        $this->info("Bank Account Verification completed successfully.");
    }

    protected function processEpfVerification($candidateService): void
    {
        // Get all input fields for this candidate service
        $dataRows = $this->candidateServiceDataService->query()->where($this->candidateServiceDataService->candidateServiceId(), $candidateService->{$this->candidateServiceService->id()})
            ->with(['field' => function ($q) {
                $q->select('id', 'field_name');
            }])
            ->get();

        $uan = null;
        $uanFieldRow = null;

        // Fetch inputs from mapped fields
        foreach ($dataRows as $row) {
            $fieldName = $row->field?->field_name ?? '';
            if ($fieldName === ServiceFieldName::UAN->value) {
                $uan = trim((string) $row->{$this->candidateServiceDataService->fieldValue()});
                $uanFieldRow = $row;
            }
        }

        if (!$uan) {
            throw new \Exception("Missing UAN number input data.");
        }

        $this->info("Calling Protean EPF UAN Validation API with UAN: $uan");

        $startTime = microtime(true);
        $apiResult = null;
        $error = null;

        try {
            $apiResult = $this->proteanService->epfUanValidation($uan);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            throw $e;
        } finally {
            $durationMs = (int) round((microtime(true) - $startTime) * 1000);
            \Illuminate\Support\Facades\DB::table('api_logs')->insert([
                'service_id' => $candidateService->{$this->candidateServiceService->serviceId()},
                'order_item_id' => $candidateService->{$this->candidateServiceService->orderItemId()},
                'endpoint' => '/api/v1/protean/fetch-employment-history',
                'method' => 'POST',
                'request_data' => json_encode([
                    'uan' => $uan,
                ]),
                'response_data' => json_encode($apiResult['data'] ?? $apiResult['raw'] ?? []),
                'http_status' => $apiResult['status_code'] ?? ($error ? 500 : 200),
                'status' => ($apiResult && $apiResult['success']) ? 'success' : 'failed',
                'error_message' => $error ?? $apiResult['error'] ?? null,
                'duration_ms' => $durationMs,
                'ip_address' => '127.0.0.1',
                'created_at' => now(),
            ]);
        }

        if (!$apiResult['success']) {
            throw new \Exception($apiResult['error'] ?? 'Protean EPF UAN Validation API call failed.');
        }

        // Save result and verify
        $isVerified = 1;

        $uanFieldRow->update([
            $this->candidateServiceDataService->isVerified() => $isVerified,
            $this->candidateServiceDataService->verifiedAt() => now(),
            $this->candidateServiceDataService->status() => 'verified',
        ]);

        $candidateService->update([
            $this->candidateServiceService->processingStatus() => 'completed',
            $this->candidateServiceService->processedAt() => now(),
            $this->candidateServiceService->completedAt() => now(),
            $this->candidateServiceService->errorMessage() => null,
        ]);

        $this->info("EPF UAN Validation completed successfully.");
    }
}
