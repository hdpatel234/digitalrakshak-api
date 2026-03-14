<?php

namespace App\Http\Controllers\Api\Client\Order;

use App\Http\Controllers\Api\Client\BaseController;
use App\Http\Requests\Api\Client\Order\StoreOrderRequest;
use App\Enums\OrderStatus;
use App\Services\CandidateService;
use App\Services\CandidateOrderService;
use App\Services\OrderCandidateService;
use App\Services\PackageService;
use App\Services\PaymentGatewayConfigService;
use App\Services\PaymentGatewayService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends BaseController
{
    use ApiResponse;
    protected CandidateOrderService $service;
    protected OrderCandidateService $orderCandidateService;
    protected PackageService $packageService;
    protected CandidateService $candidateService;
    protected PaymentGatewayConfigService $paymentGatewayConfigService;
    protected PaymentGatewayService $paymentGatewayService;

    public function __construct(
        CandidateOrderService $service,
        OrderCandidateService $orderCandidateService,
        PackageService $packageService,
        CandidateService $candidateService,
        PaymentGatewayConfigService $paymentGatewayConfigService,
        PaymentGatewayService $paymentGatewayService
    )
    {
        $this->service = $service;
        $this->orderCandidateService = $orderCandidateService;
        $this->packageService = $packageService;
        $this->candidateService = $candidateService;
        $this->paymentGatewayConfigService = $paymentGatewayConfigService;
        $this->paymentGatewayService = $paymentGatewayService;
    }

    public function index(Request $request)
    {
        //
    }
    public function store(StoreOrderRequest $request)
    {
        Log::info("Order create requst", $request->all());

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $payload = $request->validated();
        $packageId = (int) ($payload['package_id'] ?? 0);
        $paymentMethodId = (int) ($payload['payment_method_id'] ?? 0);
        $paymentProviderId = (int) ($payload['payment_provider_id'] ?? 0);
        $isDraft = (bool) ($payload['save_draft'] ?? false);
        $candidateIds = collect($payload['candidate_ids'] ?? [])
            ->map(static fn($id) => (int) $id)
            ->filter(static fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $package = $this->packageService->query()
            ->where($this->packageService->id(), $packageId)
            ->where(function ($builder) {
                $builder->where($this->packageService->status(), 'active')
                    ->orWhere($this->packageService->status(), 1);
            })
            ->where(function ($builder) {
                $builder->where($this->packageService->isActive(), 'active')
                    ->orWhere($this->packageService->isActive(), 1);
            })
            ->where(function ($builder) use ($clientId) {
                $builder->where($this->packageService->clientId(), $clientId)
                    ->orWhere($this->packageService->clientId(), 0);
            })
            ->first();

        if (!$package) {
            return $this->error('Package not found.', 404);
        }

        $selectedCandidates = $this->candidateService->query()
            ->whereIn($this->candidateService->id(), $candidateIds)
            ->where($this->candidateService->clientId(), $clientId)
            ->get();

        $validCandidateIds = $selectedCandidates
            ->pluck($this->candidateService->id())
            ->map(static fn($id) => (int) $id)
            ->values()
            ->all();

        $invalidCandidateIds = array_values(array_diff($candidateIds, $validCandidateIds));
        if ($invalidCandidateIds !== []) {
            return $this->validationError([
                'candidate_ids' => ['Some candidates are invalid or not accessible.'],
                'invalid_candidate_ids' => $invalidCandidateIds,
            ]);
        }

        $gatewayConfig = $this->paymentGatewayConfigService->query()
            ->with(['gateway' => function ($query) {
                $query->where($this->paymentGatewayService->isActive(), 1);
            }])
            ->where($this->paymentGatewayConfigService->id(), $paymentProviderId)
            ->where($this->paymentGatewayConfigService->isActive(), 1)
            ->first();

        if (!$gatewayConfig || !$gatewayConfig->gateway) {
            return $this->error('Invalid payment provider.', 422);
        }

        $supportedMethodIds = $this->extractMethodTypeIds(
            $gatewayConfig->gateway->{$this->paymentGatewayService->supportedMethods()} ?? null
        );
        $supportedMethodIds = collect($supportedMethodIds)
            ->map(static fn($id) => (int) $id)
            ->filter(static fn($id) => $id > 0)
            ->unique()
            ->values();

        if ($supportedMethodIds->isNotEmpty() && !$supportedMethodIds->contains($paymentMethodId)) {
            return $this->error('Payment method is not supported by this provider.', 422);
        }

        $unitPrice = (float) (
            $package->{$this->packageService->finalPrice()}
            ?? $package->{$this->packageService->totalPrice()}
            ?? 0
        );

        $subtotal = $unitPrice * count($candidateIds);

        $created = DB::transaction(function () use (
            $package,
            $candidateIds,
            $clientId,
            $user,
            $unitPrice,
            $subtotal,
            $gatewayConfig,
            $paymentMethodId,
            $isDraft
        ) {
            $orderNumber = $this->generateOrderNumber($clientId);

            $orderStatus = $isDraft ? OrderStatus::DRAFT->value : OrderStatus::PENDING->value;

            $order = $this->service->create([
                $this->service->orderNumber() => $orderNumber,
                $this->service->clientId() => $clientId,
                $this->service->packageId() => $package->{$this->packageService->id()},
                $this->service->orderType() => 'package',
                $this->service->subtotal() => $subtotal,
                $this->service->discountAmount() => 0,
                $this->service->taxAmount() => 0,
                $this->service->taxPercentage() => 0,
                $this->service->totalAmount() => $subtotal,
                $this->service->paymentStatus() => 'pending',
                $this->service->paymentMethod() => (string) $paymentMethodId,
                $this->service->billingConfigId() => $gatewayConfig->{$this->paymentGatewayConfigService->id()},
                $this->service->status() => $orderStatus,
                $this->service->createdBy() => $user?->id,
            ]);

            $candidateRows = [];
            foreach ($candidateIds as $candidateId) {
                $candidateRows[] = $this->orderCandidateService->create([
                    $this->orderCandidateService->orderId() => $order->{$this->service->id()},
                    $this->orderCandidateService->candidateId() => $candidateId,
                    $this->orderCandidateService->subtotal() => $unitPrice,
                    $this->orderCandidateService->discountAmount() => 0,
                    $this->orderCandidateService->taxAmount() => 0,
                    $this->orderCandidateService->totalAmount() => $unitPrice,
                    $this->orderCandidateService->status() => 'pending',
                    $this->orderCandidateService->createdBy() => $user?->id,
                ]);
            }

            return [$order, $candidateRows];
        });

        [$order, $orderCandidateRows] = $created;

        return $this->success('Order created successfully.', [
            'id' => $order->{$this->service->id()},
            'order_number' => $order->{$this->service->orderNumber()},
            'package_id' => $order->{$this->service->packageId()},
            'payment_provider_id' => $paymentProviderId,
            'payment_method_id' => $paymentMethodId,
            'billing_config_id' => $order->{$this->service->billingConfigId()},
            'order_type' => $order->{$this->service->orderType()},
            'subtotal' => $order->{$this->service->subtotal()},
            'total_amount' => $order->{$this->service->totalAmount()},
            'payment_status' => $order->{$this->service->paymentStatus()},
            'status' => $order->{$this->service->status()},
            'candidate_ids' => collect($orderCandidateRows)
                ->pluck($this->orderCandidateService->candidateId())
                ->map(static fn($id) => (int) $id)
                ->values()
                ->all(),
        ], 201);
    }
    public function show(CandidateOrderService $service)
    {
        //
    }
    public function update(Request $request, $id)
    {
        //
    }
    public function destroy(Request $request, $id)
    {
        //
    }

    protected function generateOrderNumber(int $clientId): string
    {
        do {
            $code = 'ORD-' . $clientId . '-' . Str::upper(Str::random(6));
            $exists = $this->service->query()
                ->where($this->service->orderNumber(), $code)
                ->exists();
        } while ($exists);

        return $code;
    }

    protected function extractMethodTypeIds($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (!is_string($value)) {
            return [];
        }

        $trimmed = trim($value);
        if ($trimmed === '') {
            return [];
        }

        $decoded = json_decode($trimmed, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        if (str_contains($trimmed, ',')) {
            return array_map('trim', explode(',', $trimmed));
        }

        return [$trimmed];
    }
}
