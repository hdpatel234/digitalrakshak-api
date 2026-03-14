<?php

namespace App\Http\Controllers\Api\Client\Order;

use App\Http\Controllers\Api\Client\BaseController;
use App\Http\Requests\Api\Client\Order\StoreOrderRequest;
use App\Enums\OrderStatus;
use App\Services\CandidateService;
use App\Services\CandidateOrderService;
use App\Services\OrderCandidateService;
use App\Services\PackageService;
use App\Services\PaymentGateway\PaymentGatewayDriverFactory;
use App\Services\PaymentGatewayConfigService;
use App\Services\PaymentGatewayService;
use App\Services\PaymentMethodTypeService;
use App\Services\PaymentTransactionService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class OrderController extends BaseController
{
    use ApiResponse;
    protected CandidateOrderService $service;
    protected OrderCandidateService $orderCandidateService;
    protected PackageService $packageService;
    protected CandidateService $candidateService;
    protected PaymentGatewayDriverFactory $paymentGatewayDriverFactory;
    protected PaymentGatewayConfigService $paymentGatewayConfigService;
    protected PaymentGatewayService $paymentGatewayService;
    protected PaymentMethodTypeService $paymentMethodTypeService;
    protected PaymentTransactionService $paymentTransactionService;

    public function __construct(
        CandidateOrderService $service,
        OrderCandidateService $orderCandidateService,
        PackageService $packageService,
        CandidateService $candidateService,
        PaymentGatewayDriverFactory $paymentGatewayDriverFactory,
        PaymentGatewayConfigService $paymentGatewayConfigService,
        PaymentGatewayService $paymentGatewayService,
        PaymentMethodTypeService $paymentMethodTypeService,
        PaymentTransactionService $paymentTransactionService
    ) {
        $this->service = $service;
        $this->orderCandidateService = $orderCandidateService;
        $this->packageService = $packageService;
        $this->candidateService = $candidateService;
        $this->paymentGatewayDriverFactory = $paymentGatewayDriverFactory;
        $this->paymentGatewayConfigService = $paymentGatewayConfigService;
        $this->paymentGatewayService = $paymentGatewayService;
        $this->paymentMethodTypeService = $paymentMethodTypeService;
        $this->paymentTransactionService = $paymentTransactionService;
    }

    public function index(Request $request)
    {
        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $orderTable = $this->service->query()->getModel()->getTable();
        $statusColumn = $this->service->status();
        $clientIdColumn = $this->service->clientId();
        $orderNumberColumn = $this->service->orderNumber();
        $clientOrderNumberColumn = $this->service->clientOrderNumber();
        $invoiceNumberColumn = $this->service->invoiceNumber();
        $paymentStatusColumn = $this->service->paymentStatus();
        $paymentMethodColumn = $this->service->paymentMethod();
        $orderDateColumn = $this->service->orderDate();

        $qualifiedStatusColumn = $orderTable . '.' . $statusColumn;
        $qualifiedClientIdColumn = $orderTable . '.' . $clientIdColumn;

        $query = $this->service->query()
            ->where($qualifiedClientIdColumn, $clientId);

        $params = $request->all();
        if (isset($params['payment_method_id']) && !isset($params['filters']['payment_method_id'])) {
            $params['filters']['payment_method_id'] = $params['payment_method_id'];
        }
        if (isset($params['limit']) && !isset($params['per_page'])) {
            $params['per_page'] = $params['limit'];
        }

        $result = $this->service->datatable(
            query: $query,
            params: $params,
            config: [
                'searchable' => [
                    $orderTable . '.' . $orderNumberColumn,
                    $orderTable . '.' . $clientOrderNumberColumn,
                    $orderTable . '.' . $invoiceNumberColumn,
                ],
                'status_column' => $qualifiedStatusColumn,
                'date_column' => $orderTable . '.' . $this->service->createdAt(),
                'allowed_filters' => [
                    'status' => $qualifiedStatusColumn,
                    'payment_status' => $orderTable . '.' . $paymentStatusColumn,
                    'order_date' => $orderTable . '.' . $orderDateColumn,
                    'payment_method_id' => function ($builder, $value) use ($orderTable, $paymentMethodColumn) {
                        $raw = is_array($value) ? $value : explode(',', (string) $value);
                        $ids = collect($raw)
                            ->map(static fn($id) => (int) $id)
                            ->filter(static fn($id) => $id > 0)
                            ->unique()
                            ->values()
                            ->all();

                        if ($ids === []) {
                            return;
                        }

                        $builder->whereIn($orderTable . '.' . $paymentMethodColumn, $ids);
                    },
                ],
                'allowed_sorts' => [
                    $orderTable . '.' . $this->service->id(),
                    $orderTable . '.' . $orderNumberColumn,
                    $orderTable . '.' . $clientOrderNumberColumn,
                    $orderTable . '.' . $paymentStatusColumn,
                    $orderTable . '.' . $this->service->totalAmount(),
                    $orderTable . '.' . $orderDateColumn,
                    $orderTable . '.' . $this->service->createdAt(),
                ],
                'default_sort_by' => $orderTable . '.' . $this->service->createdAt(),
                'default_sort_direction' => 'desc',
                'default_per_page' => 10,
                'max_per_page' => 100,
            ]
        );

        $statusList = array_map(
            static fn(OrderStatus $status): array => [
                'key' => $status->value,
                'name' => ucwords(str_replace('_', ' ', $status->value)),
            ],
            OrderStatus::cases()
        );

        $paymentMethodRows = $this->paymentMethodTypeService->query()
            ->where($this->paymentMethodTypeService->isActive(), 1)
            ->select([
                $this->paymentMethodTypeService->id(),
                $this->paymentMethodTypeService->methodName(),
                $this->paymentMethodTypeService->methodCode(),
                $this->paymentMethodTypeService->category(),
                $this->paymentMethodTypeService->icon(),
                $this->paymentMethodTypeService->description(),
                $this->paymentMethodTypeService->displayOrder(),
            ])
            ->orderBy($this->paymentMethodTypeService->displayOrder(), 'asc')
            ->get();

        $paymentMethods = $paymentMethodRows
            ->map(function ($method) {
                return [
                    'id' => (int) $method->{$this->paymentMethodTypeService->id()},
                    'method_name' => $method->{$this->paymentMethodTypeService->methodName()},
                    'method_code' => $method->{$this->paymentMethodTypeService->methodCode()},
                    'category' => $method->{$this->paymentMethodTypeService->category()},
                    'icon' => $method->{$this->paymentMethodTypeService->icon()},
                    'description' => $method->{$this->paymentMethodTypeService->description()},
                    'display_order' => (int) ($method->{$this->paymentMethodTypeService->displayOrder()} ?? 0),
                ];
            })
            ->values()
            ->all();

        $paymentMethodNameById = $paymentMethodRows
            ->mapWithKeys(function ($method) {
                return [
                    (int) $method->{$this->paymentMethodTypeService->id()} =>
                        $method->{$this->paymentMethodTypeService->methodName()},
                ];
            });

        if (is_array($result) && isset($result['list']) && is_array($result['list'])) {
            $result['list'] = collect($result['list'])
                ->map(function ($item) use ($paymentMethodNameById, $paymentMethodColumn) {
                    $row = is_array($item) ? $item : $item->toArray();
                    $methodId = (int) ($row[$paymentMethodColumn] ?? 0);
                    $row['payment_method_name'] = $paymentMethodNameById->get($methodId);

                    return $row;
                })
                ->values()
                ->all();
        }

        if (is_array($result)) {
            $result['status_list'] = $statusList;
            $result['payment_methods'] = $paymentMethods;
        } else {
            $result = [
                'items' => $result,
                'status_list' => $statusList,
                'payment_methods' => $paymentMethods,
            ];
        }

        return $this->success('Orders fetched successfully.', $result);
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
            ->with([
                'gateway' => function ($query) {
                    $query->where($this->paymentGatewayService->isActive(), 1);
                }
            ])
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

        $created = DB::transaction(function () use ($package, $candidateIds, $clientId, $user, $unitPrice, $subtotal, $gatewayConfig, $paymentMethodId, $isDraft) {
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
            'payment_provider_name' => $gatewayConfig->gateway->{$this->paymentGatewayService->gatewayName()},
            'payment_method_id' => $paymentMethodId,
            'billing_config_id' => $order->{$this->service->billingConfigId()},
            'order_type' => $order->{$this->service->orderType()},
            'subtotal' => $order->{$this->service->subtotal()},
            'total_amount' => (int) $order->{$this->service->totalAmount()},
            'total_amount_in_paise' => (int) $order->{$this->service->totalAmount()} * 100,
            'payment_status' => $order->{$this->service->paymentStatus()},
            'status' => $order->{$this->service->status()},
            'candidate_ids' => collect($orderCandidateRows)
                ->pluck($this->orderCandidateService->candidateId())
                ->map(static fn($id) => (int) $id)
                ->values()
                ->all(),
        ], 201);
    }
    public function show(Request $request, int $order)
    {
        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $orderId = (int) $order;
        if ($orderId <= 0) {
            return $this->error('Invalid order id.', 422);
        }

        $orderRow = $this->service->query()
            ->where($this->service->id(), $orderId)
            ->where($this->service->clientId(), $clientId)
            ->first();

        if (!$orderRow) {
            return $this->error('Order not found.', 404);
        }

        $orderData = $orderRow->toArray();
        $orderData['total_amount_in_paise'] = $orderData['total_amount'] * 100;

        $orderCandidateRows = $this->orderCandidateService->query()
            ->where($this->orderCandidateService->orderId(), $orderId)
            ->get();

        $candidateIds = $orderCandidateRows
            ->pluck($this->orderCandidateService->candidateId())
            ->map(static fn($id) => (int) $id)
            ->filter(static fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $candidatesById = $candidateIds === []
            ? collect()
            : $this->candidateService->query()
                ->whereIn($this->candidateService->id(), $candidateIds)
                ->get()
                ->keyBy($this->candidateService->id());

        $candidates = $orderCandidateRows
            ->map(function ($row) use ($candidatesById) {
                $candidateId = (int) ($row->{$this->orderCandidateService->candidateId()} ?? 0);
                $candidate = $candidatesById->get($candidateId);

                $candidateData = $row->{$this->orderCandidateService->candidateData()} ?? null;
                if (is_string($candidateData) && $candidateData !== '') {
                    $decoded = json_decode($candidateData, true);
                    $candidateData = is_array($decoded) ? $decoded : $candidateData;
                }

                return [
                    'id' => (int) ($row->{$this->orderCandidateService->id()} ?? 0),
                    'order_id' => (int) ($row->{$this->orderCandidateService->orderId()} ?? 0),
                    'candidate_id' => $candidateId,
                    'subtotal' => $row->{$this->orderCandidateService->subtotal()},
                    'discount_amount' => $row->{$this->orderCandidateService->discountAmount()},
                    'tax_amount' => $row->{$this->orderCandidateService->taxAmount()},
                    'total_amount' => $row->{$this->orderCandidateService->totalAmount()},
                    'status' => $row->{$this->orderCandidateService->status()},
                    'candidate' => $candidateData ?? ($candidate ? $candidate->toArray() : null),
                    'candidate_data' => $candidateData,
                ];
            })
            ->values()
            ->all();

        $paymentMethod = null;
        $paymentMethodId = (int) ($orderRow->{$this->service->paymentMethod()} ?? 0);
        if ($paymentMethodId > 0) {
            $methodRow = $this->paymentMethodTypeService->query()
                ->where($this->paymentMethodTypeService->id(), $paymentMethodId)
                ->select([
                    $this->paymentMethodTypeService->id(),
                    $this->paymentMethodTypeService->methodName(),
                    $this->paymentMethodTypeService->methodCode(),
                    $this->paymentMethodTypeService->category(),
                    $this->paymentMethodTypeService->icon(),
                    $this->paymentMethodTypeService->description(),
                    $this->paymentMethodTypeService->displayOrder(),
                ])
                ->first();

            if ($methodRow) {
                $paymentMethod = [
                    'id' => (int) $methodRow->{$this->paymentMethodTypeService->id()},
                    'method_name' => $methodRow->{$this->paymentMethodTypeService->methodName()},
                    'method_code' => $methodRow->{$this->paymentMethodTypeService->methodCode()},
                    'category' => $methodRow->{$this->paymentMethodTypeService->category()},
                    'icon' => $methodRow->{$this->paymentMethodTypeService->icon()},
                    'description' => $methodRow->{$this->paymentMethodTypeService->description()},
                    'display_order' => (int) ($methodRow->{$this->paymentMethodTypeService->displayOrder()} ?? 0),
                ];
            }
        }

        $paymentGateway = null;
        $gatewayConfigId = (int) ($orderRow->{$this->service->billingConfigId()} ?? 0);
        if ($gatewayConfigId > 0) {
            $gatewayConfig = $this->paymentGatewayConfigService->query()
                ->select([
                    $this->paymentGatewayConfigService->id(),
                    $this->paymentGatewayConfigService->gatewayId(),
                    $this->paymentGatewayConfigService->configName(),
                    $this->paymentGatewayConfigService->environment(),
                    $this->paymentGatewayConfigService->baseUrl(),
                    $this->paymentGatewayConfigService->enabledMethods(),
                    $this->paymentGatewayConfigService->currencies(),
                    $this->paymentGatewayConfigService->minAmount(),
                    $this->paymentGatewayConfigService->maxAmount(),
                    $this->paymentGatewayConfigService->transactionFeeType(),
                    $this->paymentGatewayConfigService->transactionFeeFixed(),
                    $this->paymentGatewayConfigService->transactionFeePercentage(),
                    $this->paymentGatewayConfigService->isActive(),
                    $this->paymentGatewayConfigService->isDefault(),
                    $this->paymentGatewayConfigService->isSandbox(),
                ])
                ->with([
                    'gateway' => function ($query) {
                        $query->select([
                            $this->paymentGatewayService->id(),
                            $this->paymentGatewayService->gatewayName(),
                            $this->paymentGatewayService->gatewayCode(),
                            $this->paymentGatewayService->providerCompany(),
                            $this->paymentGatewayService->logo(),
                            $this->paymentGatewayService->website(),
                            $this->paymentGatewayService->supportedMethods(),
                            $this->paymentGatewayService->isActive(),
                            $this->paymentGatewayService->displayOrder(),
                        ]);
                    },
                ])
                ->where($this->paymentGatewayConfigService->id(), $gatewayConfigId)
                ->first();

            if ($gatewayConfig) {
                $gateway = $gatewayConfig->gateway;

                $paymentGateway = [
                    'gateway_config_id' => (int) ($gatewayConfig->{$this->paymentGatewayConfigService->id()} ?? 0),
                    'gateway_id' => (int) ($gatewayConfig->{$this->paymentGatewayConfigService->gatewayId()} ?? 0),
                    'config_name' => $gatewayConfig->{$this->paymentGatewayConfigService->configName()},
                    'environment' => $gatewayConfig->{$this->paymentGatewayConfigService->environment()},
                    'base_url' => $gatewayConfig->{$this->paymentGatewayConfigService->baseUrl()},
                    'enabled_methods' => $gatewayConfig->{$this->paymentGatewayConfigService->enabledMethods()},
                    'currencies' => $gatewayConfig->{$this->paymentGatewayConfigService->currencies()},
                    'min_amount' => $gatewayConfig->{$this->paymentGatewayConfigService->minAmount()},
                    'max_amount' => $gatewayConfig->{$this->paymentGatewayConfigService->maxAmount()},
                    'transaction_fee_type' => $gatewayConfig->{$this->paymentGatewayConfigService->transactionFeeType()},
                    'transaction_fee_fixed' => $gatewayConfig->{$this->paymentGatewayConfigService->transactionFeeFixed()},
                    'transaction_fee_percentage' => $gatewayConfig->{$this->paymentGatewayConfigService->transactionFeePercentage()},
                    'is_active' => (int) ($gatewayConfig->{$this->paymentGatewayConfigService->isActive()} ?? 0),
                    'is_default' => (int) ($gatewayConfig->{$this->paymentGatewayConfigService->isDefault()} ?? 0),
                    'is_sandbox' => (int) ($gatewayConfig->{$this->paymentGatewayConfigService->isSandbox()} ?? 0),
                    'gateway' => $gateway ? [
                        'id' => (int) ($gateway->{$this->paymentGatewayService->id()} ?? 0),
                        'gateway_name' => $gateway->{$this->paymentGatewayService->gatewayName()},
                        'gateway_code' => $gateway->{$this->paymentGatewayService->gatewayCode()},
                        'provider_company' => $gateway->{$this->paymentGatewayService->providerCompany()},
                        'logo' => $gateway->{$this->paymentGatewayService->logo()},
                        'website' => $gateway->{$this->paymentGatewayService->website()},
                        'supported_methods' => $gateway->{$this->paymentGatewayService->supportedMethods()},
                        'is_active' => (int) ($gateway->{$this->paymentGatewayService->isActive()} ?? 0),
                        'display_order' => (int) ($gateway->{$this->paymentGatewayService->displayOrder()} ?? 0),
                    ] : null,
                ];
            }
        }

        $transactions = $this->paymentTransactionService->query()
            ->where($this->paymentTransactionService->orderId(), $orderId)
            ->where($this->paymentTransactionService->clientId(), $clientId)
            ->orderByDesc($this->paymentTransactionService->id())
            ->get()
            ->map(static fn($row) => $row->toArray())
            ->values()
            ->all();

        return $this->success('Order fetched successfully.', [
            'order' => $orderData,
            'candidates' => $candidates,
            'payment_method' => $paymentMethod,
            'payment_gateway' => $paymentGateway,
            'transactions' => $transactions,
        ]);
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

    public function initiatePayment(Request $request, $order)
    {
        Log::info('Order payment create', $request->all());
        Log::info('Order id' . $order);

        $validator = Validator::make($request->all(), [
            'payment_provider_name' => ['required', 'string'],
            'total_amount' => ['nullable', 'numeric', 'min:1'],
            'total_amount_in_paise' => ['nullable', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);
        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $orderId = (int) $order;
        if ($orderId <= 0) {
            return $this->error('Invalid order id.', 422);
        }

        $orderRow = $this->service->query()
            ->where($this->service->id(), $orderId)
            ->where($this->service->clientId(), $clientId)
            ->first();

        if (!$orderRow) {
            return $this->error('Order not found.', 404);
        }

        $paymentStatus = strtolower(trim((string) ($orderRow->{$this->service->paymentStatus()} ?? '')));
        if (in_array($paymentStatus, ['paid', 'success', 'completed'], true)) {
            return $this->error('Payment is already completed for this order.', 422);
        }

        $gatewayConfigId = (int) ($orderRow->{$this->service->billingConfigId()} ?? 0);
        if ($gatewayConfigId <= 0) {
            return $this->error('Payment gateway configuration not found for this order.', 422);
        }

        $gatewayConfig = $this->paymentGatewayConfigService->query()
            ->with(['gateway'])
            ->where($this->paymentGatewayConfigService->id(), $gatewayConfigId)
            ->where($this->paymentGatewayConfigService->isActive(), 1)
            ->first();

        if (!$gatewayConfig || !$gatewayConfig->gateway) {
            return $this->error('Invalid payment provider configuration.', 422);
        }

        $providerName = trim((string) $request->input('payment_provider_name', ''));
        $gatewayName = (string) ($gatewayConfig->gateway->{$this->paymentGatewayService->gatewayName()} ?? '');
        $gatewayCode = (string) ($gatewayConfig->gateway->{$this->paymentGatewayService->gatewayCode()} ?? '');

        if ($providerName !== '' && !$this->providerMatches($providerName, $gatewayName, $gatewayCode)) {
            return $this->error('Payment provider does not match the order gateway.', 422);
        }

        $orderAmount = (float) ($orderRow->{$this->service->totalAmount()} ?? 0);
        $payloadAmount = (float) ($request->input('total_amount') ?? 0);
        $amount = $payloadAmount > 0 ? $payloadAmount : $orderAmount;

        if ($amount <= 0) {
            return $this->error('Invalid total amount for payment.', 422);
        }

        if ($orderAmount > 0 && abs($amount - $orderAmount) > 0.01) {
            return $this->error('Total amount does not match the order amount.', 422);
        }

        $expectedPaise = (int) round($amount * 100);
        $payloadPaise = (int) ($request->input('total_amount_in_paise') ?? 0);
        if ($payloadPaise > 0 && $payloadPaise !== $expectedPaise) {
            return $this->error('Total amount in paise does not match the order amount.', 422);
        }

        $amountInPaise = $payloadPaise > 0 ? $payloadPaise : $expectedPaise;

        $currency = $this->resolveCurrency(
            $gatewayConfig->{$this->paymentGatewayConfigService->currencies()} ?? null
        ) ?? 'INR';

        $gatewayPayload = [
            'order_id' => $orderId,
            'order_number' => (string) ($orderRow->{$this->service->orderNumber()} ?? ''),
            'receipt' => 'order_' . $orderId . '_' . now()->format('YmdHis'),
            'amount' => $amount,
            'amount_in_paise' => $amountInPaise,
            'currency' => $currency,
            'customer' => [
                'id' => $user?->id,
                'name' => trim((string) ($user?->name ?? '')),
                'email' => $user?->email,
                'phone' => $user?->phone,
            ],
        ];

        try {
            $driver = $this->paymentGatewayDriverFactory->driver($gatewayConfig);
            $gatewayResponse = $driver->initiatePayment($gatewayPayload);
        } catch (InvalidArgumentException | RuntimeException $e) {
            Log::warning('Payment initiation rejected', [
                'order_id' => $orderId,
                'gateway_config_id' => $gatewayConfigId,
                'error' => $e->getMessage(),
            ]);

            return $this->error($e->getMessage(), 422);
        } catch (\Throwable $e) {
            Log::error('Payment initiation failed', [
                'order_id' => $orderId,
                'gateway_config_id' => $gatewayConfigId,
                'error' => $e->getMessage(),
            ]);

            return $this->error('Unable to initiate payment.', 500);
        }

        $transactionPayload = [
            $this->paymentTransactionService->transactionUuid() => (string) Str::uuid(),
            $this->paymentTransactionService->clientId() => $clientId,
            $this->paymentTransactionService->orderId() => $orderId,
            $this->paymentTransactionService->gatewayConfigId() => $gatewayConfigId,
            $this->paymentTransactionService->amount() => $amount,
            $this->paymentTransactionService->currency() => $currency,
            $this->paymentTransactionService->paymentStatus() => 'initiated',
            $this->paymentTransactionService->status() => 'pending',
            $this->paymentTransactionService->initiatedAt() => now(),
            $this->paymentTransactionService->gatewayOrderId() => $gatewayResponse['gateway_order_id'] ?? null,
            $this->paymentTransactionService->gatewayRequest() => json_encode($gatewayPayload),
            $this->paymentTransactionService->gatewayResponse() => json_encode($gatewayResponse),
            $this->paymentTransactionService->ipAddress() => $request->ip(),
            $this->paymentTransactionService->userAgent() => $request->userAgent(),
            $this->paymentTransactionService->createdBy() => $user?->id,
        ];

        $paymentMethodId = (int) ($orderRow->{$this->service->paymentMethod()} ?? 0);
        if ($paymentMethodId > 0) {
            $transactionPayload[$this->paymentTransactionService->methodTypeId()] = $paymentMethodId;
        }

        $transaction = $this->paymentTransactionService->create($transactionPayload);

        $response = [
            'order_id' => $orderId,
            'transaction_uuid' => $transaction->{$this->paymentTransactionService->transactionUuid()} ?? null,
            'payment_provider' => [
                'name' => $gatewayName,
                'code' => $gatewayCode,
            ],
            'amount' => $amount,
            'amount_in_paise' => $amountInPaise,
            'currency' => $currency,
            'gateway_order_id' => $gatewayResponse['gateway_order_id'] ?? null,
            'razorpay_order_id' => $gatewayResponse['gateway_order_id'] ?? null,
            'gateway' => $gatewayResponse,
            'company_name' => config('app.name')
        ];

        return $this->success('Payment initiated successfully.', $response);
    }

    public function completePayment(Request $request)
    {
        Log::info('Order payment complete request', $request->all());
    }

    protected function providerMatches(string $input, string $gatewayName, string $gatewayCode): bool
    {
        $normalizedInput = $this->normalizeProviderKey($input);
        if ($normalizedInput === '') {
            return false;
        }

        $normalizedName = $this->normalizeProviderKey($gatewayName);
        $normalizedCode = $this->normalizeProviderKey($gatewayCode);

        return $normalizedInput === $normalizedName || $normalizedInput === $normalizedCode;
    }

    protected function normalizeProviderKey(string $value): string
    {
        $value = strtolower(trim($value));
        if ($value === '') {
            return '';
        }

        $value = preg_replace('/[^a-z0-9]+/', '_', $value);
        return trim((string) $value, '_');
    }

    protected function resolveCurrency($currencies): ?string
    {
        if (is_string($currencies)) {
            $trimmed = trim($currencies);
            if ($trimmed === '') {
                return null;
            }

            $decoded = json_decode($trimmed, true);
            if (is_array($decoded)) {
                $currencies = $decoded;
            } elseif (str_contains($trimmed, ',')) {
                $currencies = array_map('trim', explode(',', $trimmed));
            } else {
                return strtoupper($trimmed);
            }
        }

        if (is_array($currencies)) {
            $first = collect($currencies)
                ->map(static fn($item) => strtoupper(trim((string) $item)))
                ->first(fn($item) => $item !== '');

            return $first ?: null;
        }

        return null;
    }
}
