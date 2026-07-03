<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CandidateOrder;
use App\Models\CandidateService;
use App\Models\CandidateServiceLog;
use App\Services\Verification\VerificationServiceFactory;
use Illuminate\Support\Facades\Log;

class ProcessCandidateServicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-candidate-services';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process candidate services for orders in processing state';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting candidate services processing...');

        // Get all orders in processing state
        $orders = CandidateOrder::where('status', 'processing')
            ->with(['candidates.candidateServices' => function($q) {
                // only get services that are not completed
                $q->where('status', '!=', 'completed')->with('service');
            }])
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No orders in processing state found.');
            return;
        }

        foreach ($orders as $order) {
            $this->info("Processing order ID: {$order->id}");
            
            $allServicesCompleted = true;

            foreach ($order->candidates as $candidate) {
                foreach ($candidate->candidateServices as $candidateService) {
                    if (!$candidateService->service || !$candidateService->service->service_code) {
                        $this->warn("Candidate Service ID {$candidateService->id} is missing service or service_code.");
                        $allServicesCompleted = false;
                        continue;
                    }
                    
                    try {
                        $serviceInstance = VerificationServiceFactory::make($candidateService->service->service_code);
                        $serviceInstance->process($candidateService);
                        
                        // Check if it's completed now
                        $freshService = $candidateService->fresh();
                        if ($freshService->status !== 'completed') {
                            $allServicesCompleted = false;
                        } else {
                            // Store Audit Log
                            $existingLog = CandidateServiceLog::where('candidate_service_id', $candidateService->id)->first();
                            if (!$existingLog) {
                                CandidateServiceLog::create([
                                    'candidate_id' => $candidate->id,
                                    'candidate_service_id' => $candidateService->id,
                                    'title' => "Provider Service Approved: " . ($candidateService->service->name ?? 'Service Verification'),
                                    'description' => "Verified via " . ($candidateService->service->service_code ?? 'Internal Gateway'),
                                    'status' => 'completed'
                                ]);
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error("Failed to process Candidate Service ID {$candidateService->id}: " . $e->getMessage());
                        $this->error("Failed to process Candidate Service ID {$candidateService->id}: " . $e->getMessage());
                        $allServicesCompleted = false;
                    }
                }
                
                // Check if candidate is completed
                $pendingCandidateServicesCount = \App\Models\CandidateService::where('candidate_id', $candidate->id)
                    ->where('status', '!=', 'completed')
                    ->count();

                if ($pendingCandidateServicesCount === 0 && $candidate->status !== 'completed') {
                    $candidate->status = 'completed';
                    $candidate->save();
                    $this->info("Candidate ID {$candidate->id} marked as completed.");
                    
                    // Generate PDF Report for candidate
                    try {
                        $reportService = new \App\Services\CandidateReportService();
                        $reportPath = $reportService->generateForCandidate($candidate);
                        if ($reportPath) {
                            $candidate->report_path = $reportPath;
                            $candidate->save();
                            $this->info("Generated report for Candidate ID {$candidate->id} at {$reportPath}");
                            
                            // Log report generation
                            CandidateServiceLog::create([
                                'candidate_id' => $candidate->id,
                                'candidate_service_id' => null,
                                'title' => "Verification Report Cryptographically Sealed",
                                'description' => "System Automated Agent",
                                'status' => 'completed'
                            ]);
                        } else {
                            $this->error("Failed to generate report for Candidate ID {$candidate->id}");
                        }
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error("Report generation failed for candidate {$candidate->id}: " . $e->getMessage());
                        $this->error("Report generation failed: " . $e->getMessage());
                    }
                }
            }

            // After processing all candidates and their services for this order, 
            // check if there are any pending services left for this order at the DB level
            // in case the eager load missed anything or if we successfully finished everything.
            
            $pendingServicesCount = CandidateService::where('order_id', $order->id)
                ->where('status', '!=', 'completed')
                ->count();

            if ($pendingServicesCount === 0) {
                $order->status = 'completed';
                $order->completed_at = now();
                $order->save();
                $this->info("Order ID {$order->id} marked as completed.");
            } else {
                $this->info("Order ID {$order->id} still has {$pendingServicesCount} pending services.");
            }
        }

        $this->info('Candidate services processing finished.');
    }
}
