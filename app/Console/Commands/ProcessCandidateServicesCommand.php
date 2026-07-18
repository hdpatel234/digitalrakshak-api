<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CandidateServiceLog;
use App\Services\Verification\VerificationServiceFactory;
use Illuminate\Support\Facades\Log;
use App\Services\CandidateOrderService;
use App\Services\CandidateServiceService;
use App\Services\CandidateService as CandidateModelService;
use App\Services\ServiceService;
use App\Services\CandidateReportService;
use App\Enums\OrderStatus;

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
    public function __construct(
        protected CandidateOrderService $candidateOrderService,
        protected CandidateServiceService $candidateServiceService,
        protected CandidateModelService $candidateModelService,
        protected ServiceService $serviceService
    ) {
        parent::__construct();
    }
    public function handle()
    {
        $this->info('Starting candidate services processing...');

        $orders = $this->candidateOrderService->query()
            ->where($this->candidateOrderService->status(), OrderStatus::PROCESSING->value)
            ->with(['candidates.candidateServices' => function ($q) {
                $q->where($this->candidateServiceService->status(), '!=', OrderStatus::COMPLETED->value)->with('service');
            }])
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No orders in processing state found.');
            return;
        }

        foreach ($orders as $order) {
            $this->info("Processing order ID: {$order->{$this->candidateOrderService->id()}}");

            foreach ($order->candidates as $candidate) {
                foreach ($candidate->candidateServices as $candidateService) {
                    if (!$candidateService->service || !$candidateService->service->{$this->serviceService->serviceCode()}) {
                        $this->warn("Candidate Service ID {$candidateService->{$this->candidateServiceService->id()}} is missing service or service_code.");
                        continue;
                    }

                    try {
                        $serviceInstance = VerificationServiceFactory::make($candidateService->service->{$this->serviceService->serviceCode()});
                        $serviceInstance->process($candidateService);

                        $freshService = $candidateService->fresh();
                        if ($freshService->{$this->candidateServiceService->status()} !== OrderStatus::COMPLETED->value) {
                        } else {
                            $existingLog = CandidateServiceLog::where('candidate_service_id', $candidateService->{$this->candidateServiceService->id()})->first();
                            if (!$existingLog) {
                                CandidateServiceLog::create([
                                    'candidate_id' => $candidate->{$this->candidateModelService->id()},
                                    'candidate_service_id' => $candidateService->{$this->candidateServiceService->id()},
                                    'title' => "Provider Service Approved: " . ($candidateService->service->{$this->serviceService->serviceName()} ?? 'Service Verification'),
                                    'description' => "Verified via " . ($candidateService->service->{$this->serviceService->serviceCode()} ?? 'Internal Gateway'),
                                    'status' => OrderStatus::COMPLETED->value
                                ]);
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error("Failed to process Candidate Service ID {$candidateService->{$this->candidateServiceService->id()}}: " . $e->getMessage());
                        $this->error("Failed to process Candidate Service ID {$candidateService->{$this->candidateServiceService->id()}}: " . $e->getMessage());
                    }
                }

                $pendingCandidateServicesCount = $this->candidateServiceService->query()
                    ->where($this->candidateServiceService->candidateId(), $candidate->{$this->candidateModelService->id()})
                    ->where($this->candidateServiceService->status(), '!=', OrderStatus::COMPLETED->value)
                    ->count();

                if ($pendingCandidateServicesCount === 0 && $candidate->{$this->candidateModelService->status()} !== OrderStatus::COMPLETED->value) {
                    $candidate->{$this->candidateModelService->status()} = OrderStatus::COMPLETED->value;
                    $candidate->save();
                    $this->info("Candidate ID {$candidate->{$this->candidateModelService->id()}} marked as completed.");

                    try {
                        $reportService = app(CandidateReportService::class);
                        $reportPath = $reportService->generateForCandidate($candidate);
                        if ($reportPath) {
                            $candidate->report_path = $reportPath;
                            $candidate->save();
                            $this->info("Generated report for Candidate ID {$candidate->{$this->candidateModelService->id()}} at {$reportPath}");

                            CandidateServiceLog::create([
                                'candidate_id' => $candidate->{$this->candidateModelService->id()},
                                'candidate_service_id' => null,
                                'title' => "Verification Report Cryptographically Sealed",
                                'description' => "System Automated Agent",
                                'status' => OrderStatus::COMPLETED->value
                            ]);
                        } else {
                            $this->error("Failed to generate report for Candidate ID {$candidate->{$this->candidateModelService->id()}}");
                        }
                    } catch (\Exception $e) {
                        Log::error("Report generation failed for candidate {$candidate->{$this->candidateModelService->id()}}: " . $e->getMessage());
                        $this->error("Report generation failed: " . $e->getMessage());
                    }
                }
            }

            $pendingServicesCount = $this->candidateServiceService->query()
                ->where($this->candidateServiceService->orderId(), $order->{$this->candidateOrderService->id()})
                ->where($this->candidateServiceService->status(), '!=', OrderStatus::COMPLETED->value)
                ->count();

            if ($pendingServicesCount === 0) {
                $order->{$this->candidateOrderService->status()} = OrderStatus::COMPLETED->value;
                $order->{$this->candidateOrderService->completedAt()} = now();
                $order->save();
                $this->info("Order ID {$order->{$this->candidateOrderService->id()}} marked as completed.");
            } else {
                $this->info("Order ID {$order->{$this->candidateOrderService->id()}} still has {$pendingServicesCount} pending services.");
            }
        }

        $this->info('Candidate services processing finished.');
    }
}
