<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Models\CandidateOrder;
use App\Models\CandidateService;
use App\Models\CandidateServiceData;
use App\Services\ProteanService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessOrderVerificationsCommand extends Command
{
    protected $signature = 'orders:process-verifications';
    protected $description = 'Process pending candidate verifications (Bank Account and EPF) using Protean APIs';

    public function __construct(
        protected ProteanService $proteanService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Starting order verifications processing...');

        // 1. Fetch all pending or retry candidate services under paid orders
        $candidateServices = CandidateService::whereIn('processing_status', ['pending', 'retry'])
            ->whereHas('order', function ($query) {
                $query->where('payment_status', 'paid')
                    ->whereIn('status', ['processing', 'completed']);
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
            $serviceId = (int) $candidateService->service_id;
            $orderId = (int) $candidateService->order_id;
            
            $this->info(sprintf('Processing Service ID: %d for Candidate ID: %d, Order ID: %d', $serviceId, $candidateService->candidate_id, $orderId));

            // Set state to processing
            $candidateService->update([
                'processing_status' => 'processing',
                'processing_attempts' => $candidateService->processing_attempts + 1,
            ]);

            try {
                if ($serviceId === 2) {
                    $this->processBankAccountVerification($candidateService);
                } elseif ($serviceId === 5) {
                    $this->processEpfVerification($candidateService);
                } else {
                    // Mark as completed or skipped if not an auto-verifiable service
                    $candidateService->update([
                        'processing_status' => 'completed',
                        'processed_at' => now(),
                        'completed_at' => now(),
                    ]);
                    $this->warn("Skipped Service ID {$serviceId} (no automatic API handler configured).");
                }

                $processedOrders[$orderId] = true;

            } catch (\Exception $e) {
                Log::error("Failed to process candidate service ID {$candidateService->id}: " . $e->getMessage());
                $candidateService->update([
                    'processing_status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
                $this->error("Error for Service ID {$candidateService->id}: " . $e->getMessage());
            }
        }

        // 2. Check if all services in processed orders are completed and update order status
        foreach (array_keys($processedOrders) as $orderId) {
            $orderServices = CandidateService::where('order_id', $orderId)->get();
            $allCompleted = $orderServices->every(fn($item) => $item->processing_status === 'completed');

            if ($allCompleted) {
                CandidateOrder::where('id', $orderId)->update([
                    'status' => OrderStatus::COMPLETED->value,
                    'completed_at' => now(),
                ]);
                $this->info("Order ID {$orderId} has been successfully completed.");
            }
        }

        $this->info('Order verifications processing finished.');
        return self::SUCCESS;
    }

    protected function processBankAccountVerification(CandidateService $candidateService): void
    {
        // Get all input fields for this candidate service
        $dataRows = CandidateServiceData::where('candidate_service_id', $candidateService->id)
            ->with(['field' => function($q) {
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
            if ($fieldName === 'beneficiary_account') {
                $account = trim((string) $row->field_value);
                $accountFieldRow = $row;
            } elseif ($fieldName === 'beneficiary_ifsc') {
                $ifsc = trim((string) $row->field_value);
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
                'service_id' => $candidateService->service_id,
                'order_item_id' => $candidateService->order_item_id,
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
            'is_verified' => $isVerified,
            'verified_at' => now(),
            'status' => 'verified',
        ]);
        
        $ifscFieldRow->update([
            'is_verified' => $isVerified,
            'verified_at' => now(),
            'status' => 'verified',
        ]);

        $candidateService->update([
            'processing_status' => 'completed',
            'processed_at' => now(),
            'completed_at' => now(),
            'error_message' => null,
        ]);

        $this->info("Bank Account Verification completed successfully.");
    }

    protected function processEpfVerification(CandidateService $candidateService): void
    {
        // Get all input fields for this candidate service
        $dataRows = CandidateServiceData::where('candidate_service_id', $candidateService->id)
            ->with(['field' => function($q) {
                $q->select('id', 'field_name');
            }])
            ->get();

        $uan = null;
        $uanFieldRow = null;

        // Fetch inputs from mapped fields
        foreach ($dataRows as $row) {
            $fieldName = $row->field?->field_name ?? '';
            if ($fieldName === 'uan') {
                $uan = trim((string) $row->field_value);
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
                'service_id' => $candidateService->service_id,
                'order_item_id' => $candidateService->order_item_id,
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
            'is_verified' => $isVerified,
            'verified_at' => now(),
            'status' => 'verified',
        ]);

        $candidateService->update([
            'processing_status' => 'completed',
            'processed_at' => now(),
            'completed_at' => now(),
            'error_message' => null,
        ]);

        $this->info("EPF UAN Validation completed successfully.");
    }
}
