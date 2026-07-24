<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Verification\VerificationServiceFactory;
use Illuminate\Support\Facades\Log;
use App\Services\OrderService;
use App\Services\CandidateServiceService;
use App\Services\CandidateService as CandidateModelService;
use App\Services\ServiceService;
use App\Services\CandidateReportService;
use App\Services\CandidateServiceLogService;
use App\Enums\OrderStatus;

class ProcessOrderItemsCommand extends Command
{
    protected $signature = 'app:process-candidate-services';
    protected $description = 'Process candidate services for orders in processing state';
    public function __construct(
        protected OrderService $candidateOrderService,
        protected CandidateServiceService $candidateServiceService,
        protected CandidateModelService $candidateModelService,
        protected ServiceService $serviceService,
        protected CandidateServiceLogService $candidateServiceLogService
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
                        $msg = "Candidate Service ID {$candidateService->{$this->candidateServiceService->id()}} is missing service or service_code.";
                        $this->warn($msg);
                        Log::warning($msg);
                        continue;
                    }

                    try {
                        $serviceInstance = VerificationServiceFactory::make($candidateService->service->{$this->serviceService->serviceCode()});
                        $serviceInstance->process($candidateService);

                        $freshService = $candidateService->fresh();
                        if ($freshService->{$this->candidateServiceService->status()} !== OrderStatus::COMPLETED->value) {
                        } else {
                            $existingLog = $this->candidateServiceLogService->query()->where($this->candidateServiceLogService->candidateServiceId(), $candidateService->{$this->candidateServiceService->id()})->first();
                            if (!$existingLog) {
                                $this->candidateServiceLogService->create([
                                    $this->candidateServiceLogService->candidateId() => $candidate->{$this->candidateModelService->id()},
                                    $this->candidateServiceLogService->candidateServiceId() => $candidateService->{$this->candidateServiceService->id()},
                                    $this->candidateServiceLogService->title() => "Provider Service Approved: " . ($candidateService->service->{$this->serviceService->serviceName()} ?? 'Service Verification'),
                                    $this->candidateServiceLogService->description() => "Verified via " . ($candidateService->service->{$this->serviceService->serviceCode()} ?? 'Internal Gateway'),
                                    $this->candidateServiceLogService->status() => OrderStatus::COMPLETED->value
                                ]);
                            }
                        }
                    } catch (\Exception $e) {
                        $msg = "Failed to process Candidate Service ID {$candidateService->{$this->candidateServiceService->id()}}: " . $e->getMessage();
                        Log::error($msg);
                        $this->error($msg);
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

                            $this->candidateServiceLogService->create([
                                $this->candidateServiceLogService->candidateId() => $candidate->{$this->candidateModelService->id()},
                                $this->candidateServiceLogService->candidateServiceId() => null,
                                $this->candidateServiceLogService->title() => "Verification Report Cryptographically Sealed",
                                $this->candidateServiceLogService->description() => "System Automated Agent",
                                $this->candidateServiceLogService->status() => OrderStatus::COMPLETED->value
                            ]);
                        } else {
                            $this->error("Failed to generate report for Candidate ID {$candidate->{$this->candidateModelService->id()}}");
                        }
                    } catch (\Exception $e) {
                        $msg = "Report generation failed for candidate {$candidate->{$this->candidateModelService->id()}}: " . $e->getMessage();
                        Log::error($msg);
                        $this->error($msg);
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
