<?php

namespace App\Services\ApiService\Client;

use App\Enums\EmailPriority;
use App\Enums\EmailQueueStatus;
use App\Enums\EmailTemplateCode;
use App\Enums\OrderStatus;
use App\Enums\UserStatus;
use App\Services\BaseService;
use App\Repositories\ClientRepository;
use App\Repositories\CandidateOrderRepository;
use App\Repositories\OrderCandidateRepository;
use App\Repositories\PackageRepository;
use App\Repositories\CandidateRepository;
use App\Repositories\PaymentGatewayConfigRepository;
use App\Repositories\PaymentGatewayRepository;
use App\Repositories\PaymentMethodTypeRepository;
use App\Repositories\PaymentTransactionRepository;
use App\Repositories\EmailTemplateRepository;
use App\Repositories\EmailQueueRepository;
use App\Repositories\UserRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\InvoiceItemRepository;
use App\Repositories\ConfigurationRepository;
use App\Repositories\CandidateServiceRepository;
use App\Repositories\CandidateServiceDataRepository;
use App\Services\EmailTemplateService;
use App\Services\PaymentGateway\PaymentGatewayDriverFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderService extends BaseService
{
    public function __construct(
        protected ClientRepository $clientRepo,
        protected CandidateOrderRepository $candidateOrderRepo,
        protected OrderCandidateRepository $orderCandidateRepo,
        protected PackageRepository $packageRepo,
        protected CandidateRepository $candidateRepo,
        protected PaymentGatewayDriverFactory $paymentGatewayDriverFactory,
        protected PaymentGatewayConfigRepository $paymentGatewayConfigRepo,
        protected PaymentGatewayRepository $paymentGatewayRepo,
        protected PaymentMethodTypeRepository $paymentMethodTypeRepo,
        protected PaymentTransactionRepository $paymentTransactionRepo,
        protected EmailTemplateRepository $emailTemplateRepo,
        protected EmailQueueRepository $emailQueueRepo,
        protected UserRepository $userRepo,
        protected InvoiceRepository $invoiceRepo,
        protected InvoiceItemRepository $invoiceItemRepo,
        protected ConfigurationRepository $configurationRepo,
        protected CandidateServiceRepository $candidateServiceRepo,
        protected CandidateServiceDataRepository $candidateServiceDataRepo,
        protected EmailTemplateService $emailTemplateService
    ) {}

    public function getOrders(array $params, int $clientId): array
    {
        $orderTable = $this->candidateOrderRepo->query()->getModel()->getTable();
        $statusColumn = $this->candidateOrderRepo->status();
        $clientIdColumn = $this->candidateOrderRepo->clientId();
        $orderNumberColumn = $this->candidateOrderRepo->orderNumber();
        $clientOrderNumberColumn = $this->candidateOrderRepo->clientOrderNumber();
        $invoiceNumberColumn = $this->candidateOrderRepo->invoiceNumber();
        $paymentStatusColumn = $this->candidateOrderRepo->paymentStatus();
        $paymentMethodColumn = $this->candidateOrderRepo->paymentMethod();
        $orderDateColumn = $this->candidateOrderRepo->orderDate();

        $qualifiedStatusColumn = $orderTable . '.' . $statusColumn;
        $qualifiedClientIdColumn = $orderTable . '.' . $clientIdColumn;

        $query = $this->candidateOrderRepo->query()
            ->where($qualifiedClientIdColumn, $clientId);

        if (isset($params['payment_method_id']) && !isset($params['filters']['payment_method_id'])) {
            $params['filters']['payment_method_id'] = $params['payment_method_id'];
        }
        if (isset($params['limit']) && !isset($params['per_page'])) {
            $params['per_page'] = $params['limit'];
        }

        $result = $this->candidateOrderRepo->datatable(
            query: $query,
            params: $params,
            config: [
                'searchable' => [
                    $orderTable . '.' . $orderNumberColumn,
                    $orderTable . '.' . $clientOrderNumberColumn,
                    $orderTable . '.' . $invoiceNumberColumn,
                ],
                'status_column' => $qualifiedStatusColumn,
                'date_column' => $orderTable . '.' . $this->candidateOrderRepo->createdAt(),
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
                    $orderTable . '.' . $this->candidateOrderRepo->id(),
                    $orderTable . '.' . $orderNumberColumn,
                    $orderTable . '.' . $clientOrderNumberColumn,
                    $orderTable . '.' . $paymentStatusColumn,
                    $orderTable . '.' . $this->candidateOrderRepo->totalAmount(),
                    $orderTable . '.' . $orderDateColumn,
                    $orderTable . '.' . $this->candidateOrderRepo->createdAt(),
                ],
                'default_sort_by' => $orderTable . '.' . $this->candidateOrderRepo->createdAt(),
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

        $paymentMethodRows = $this->paymentMethodTypeRepo->query()
            ->where($this->paymentMethodTypeRepo->isActive(), 1)
            ->select([
                $this->paymentMethodTypeRepo->id(),
                $this->paymentMethodTypeRepo->methodName(),
                $this->paymentMethodTypeRepo->methodCode(),
                $this->paymentMethodTypeRepo->category(),
                $this->paymentMethodTypeRepo->icon(),
                $this->paymentMethodTypeRepo->description(),
                $this->paymentMethodTypeRepo->displayOrder(),
            ])
            ->orderBy($this->paymentMethodTypeRepo->displayOrder(), 'asc')
            ->get();

        $paymentMethods = $paymentMethodRows
            ->map(function ($method) {
                return [
                    'id' => (int) $method->{$this->paymentMethodTypeRepo->id()},
                    'method_name' => $method->{$this->paymentMethodTypeRepo->methodName()},
                    'method_code' => $method->{$this->paymentMethodTypeRepo->methodCode()},
                    'category' => $method->{$this->paymentMethodTypeRepo->category()},
                    'icon' => $method->{$this->paymentMethodTypeRepo->icon()},
                    'description' => $method->{$this->paymentMethodTypeRepo->description()},
                    'display_order' => (int) ($method->{$this->paymentMethodTypeRepo->displayOrder()} ?? 0),
                ];
            })
            ->values()
            ->all();

        $paymentMethodNameById = $paymentMethodRows
            ->mapWithKeys(function ($method) {
                return [
                    (int) $method->{$this->paymentMethodTypeRepo->id()} =>
                    $method->{$this->paymentMethodTypeRepo->methodName()},
                ];
            });

        if (is_array($result) && isset($result['list']) && is_array($result['list'])) {
            $orderList = collect($result['list'])
                ->map(static fn($item) => is_array($item) ? $item : $item->toArray());

            $billingConfigIds = $orderList
                ->pluck($this->candidateOrderRepo->billingConfigId())
                ->map(static fn($id) => (int) $id)
                ->filter(static fn($id) => $id > 0)
                ->unique()
                ->values()
                ->all();

            $gatewayConfigsById = $billingConfigIds === []
                ? collect()
                : $this->paymentGatewayConfigRepo->query()
                ->select([
                    $this->paymentGatewayConfigRepo->id(),
                    $this->paymentGatewayConfigRepo->gatewayId(),
                    $this->paymentGatewayConfigRepo->configName(),
                    $this->paymentGatewayConfigRepo->environment(),
                    $this->paymentGatewayConfigRepo->baseUrl(),
                    $this->paymentGatewayConfigRepo->enabledMethods(),
                    $this->paymentGatewayConfigRepo->currencies(),
                    $this->paymentGatewayConfigRepo->minAmount(),
                    $this->paymentGatewayConfigRepo->maxAmount(),
                    $this->paymentGatewayConfigRepo->transactionFeeType(),
                    $this->paymentGatewayConfigRepo->transactionFeeFixed(),
                    $this->paymentGatewayConfigRepo->transactionFeePercentage(),
                    $this->paymentGatewayConfigRepo->status(),

                ])
                ->with([
                    'gateway' => function ($query) {
                        $query->select([
                            $this->paymentGatewayRepo->id(),
                            $this->paymentGatewayRepo->gatewayName(),
                            $this->paymentGatewayRepo->gatewayCode(),
                            $this->paymentGatewayRepo->providerCompany(),
                            $this->paymentGatewayRepo->logo(),
                            $this->paymentGatewayRepo->website(),
                            $this->paymentGatewayRepo->supportedMethods(),
                            $this->paymentGatewayRepo->isActive(),
                            $this->paymentGatewayRepo->displayOrder(),
                        ]);
                    },
                ])
                ->whereIn($this->paymentGatewayConfigRepo->id(), $billingConfigIds)
                ->get()
                ->keyBy($this->paymentGatewayConfigRepo->id());

            $result['list'] = $orderList
                ->map(function (array $row) use ($paymentMethodNameById, $paymentMethodColumn, $gatewayConfigsById) {
                    $methodId = (int) ($row[$paymentMethodColumn] ?? 0);
                    $row['payment_method_name'] = $paymentMethodNameById->get($methodId);

                    $paymentStatus = strtolower(trim((string) ($row[$this->candidateOrderRepo->paymentStatus()] ?? '')));
                    if (!in_array($paymentStatus, ['paid', 'success', 'completed'], true)) {
                        $row['payment_gateway'] = null;
                        return $row;
                    }

                    $billingConfigId = (int) ($row[$this->candidateOrderRepo->billingConfigId()] ?? 0);
                    $gatewayConfig = $billingConfigId > 0 ? $gatewayConfigsById->get($billingConfigId) : null;

                    if (!$gatewayConfig) {
                        $row['payment_gateway'] = null;
                        return $row;
                    }

                    $gateway = $gatewayConfig->gateway;
                    $row['payment_gateway'] = [
                        'gateway_config_id' => (int) ($gatewayConfig->{$this->paymentGatewayConfigRepo->id()} ?? 0),
                        'gateway_id' => (int) ($gatewayConfig->{$this->paymentGatewayConfigRepo->gatewayId()} ?? 0),
                        'config_name' => $gatewayConfig->{$this->paymentGatewayConfigRepo->configName()},
                        'environment' => $gatewayConfig->{$this->paymentGatewayConfigRepo->environment()},
                        'base_url' => $gatewayConfig->{$this->paymentGatewayConfigRepo->baseUrl()},
                        'enabled_methods' => $gatewayConfig->{$this->paymentGatewayConfigRepo->enabledMethods()},
                        'currencies' => $gatewayConfig->{$this->paymentGatewayConfigRepo->currencies()},
                        'min_amount' => $gatewayConfig->{$this->paymentGatewayConfigRepo->minAmount()},
                        'max_amount' => $gatewayConfig->{$this->paymentGatewayConfigRepo->maxAmount()},
                        'transaction_fee_type' => $gatewayConfig->{$this->paymentGatewayConfigRepo->transactionFeeType()},
                        'transaction_fee_fixed' => $gatewayConfig->{$this->paymentGatewayConfigRepo->transactionFeeFixed()},
                        'transaction_fee_percentage' => $gatewayConfig->{$this->paymentGatewayConfigRepo->transactionFeePercentage()},
                        'is_active' => $gatewayConfig->{$this->paymentGatewayConfigRepo->status()} === 'active' ? 1 : 0,

                        'gateway' => $gateway ? [
                            'id' => (int) ($gateway->{$this->paymentGatewayRepo->id()} ?? 0),
                            'gateway_name' => $gateway->{$this->paymentGatewayRepo->gatewayName()},
                            'gateway_code' => $gateway->{$this->paymentGatewayRepo->gatewayCode()},
                            'provider_company' => $gateway->{$this->paymentGatewayRepo->providerCompany()},
                            'logo' => $gateway->{$this->paymentGatewayRepo->logo()},
                            'website' => $gateway->{$this->paymentGatewayRepo->website()},
                            'supported_methods' => $gateway->{$this->paymentGatewayRepo->supportedMethods()},
                            'is_active' => (int) ($gateway->{$this->paymentGatewayRepo->isActive()} ?? 0),
                            'display_order' => (int) ($gateway->{$this->paymentGatewayRepo->displayOrder()} ?? 0),
                        ] : null,
                    ];

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

        return $result;
    }

    public function createOrder(array $payload, int $clientId, ?object $user): array
    {
        $client = $this->clientRepo->query()
            ->where($this->clientRepo->id(), $clientId)
            ->first();

        $orderId = (int) ($payload['id'] ?? 0);
        $packageId = (int) ($payload['package_id'] ?? 0);
        $paymentMethodId = (int) ($payload['payment_method_id'] ?? 0);
        $paymentProviderId = (int) ($payload['payment_provider_id'] ?? 0);
        $isDraft = (bool) ($payload['save_draft'] ?? false);
        $hasCandidateIds = array_key_exists('candidate_ids', $payload);
        $candidateIds = collect($payload['candidate_ids'] ?? [])
            ->map(static fn($id) => (int) $id)
            ->filter(static fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $package = $this->packageRepo->query()
            ->where($this->packageRepo->id(), $packageId)
            ->where(function ($builder) {
                $builder->where($this->packageRepo->status(), 'active')
                    ->orWhere($this->packageRepo->status(), 1);
            })
            ->where(function ($builder) {
                $builder->where($this->packageRepo->isActive(), 'active')
                    ->orWhere($this->packageRepo->isActive(), 1);
            })
            ->where(function ($builder) use ($clientId) {
                $builder->where($this->packageRepo->clientId(), $clientId)
                    ->orWhere($this->packageRepo->clientId(), 0);
            })
            ->first();

        if (!$package) {
            throw new \Exception('Package not found.', 404);
        }

        if ($candidateIds !== []) {
            $selectedCandidates = $this->candidateRepo->query()
                ->whereIn($this->candidateRepo->id(), $candidateIds)
                ->where($this->candidateRepo->clientId(), $clientId)
                ->get();

            $validCandidateIds = $selectedCandidates
                ->pluck($this->candidateRepo->id())
                ->map(static fn($id) => (int) $id)
                ->values()
                ->all();

            $invalidCandidateIds = array_values(array_diff($candidateIds, $validCandidateIds));
            if ($invalidCandidateIds !== []) {
                throw new \Exception('Some candidates are invalid or not accessible.', 422);
            }
        }

        $gatewayConfig = null;
        if ($paymentProviderId > 0) {
            $gatewayConfig = $this->paymentGatewayConfigRepo->query()
                ->with([
                    'gateway' => function ($query) {
                        $query->where($this->paymentGatewayRepo->isActive(), 1);
                    }
                ])
                ->where($this->paymentGatewayConfigRepo->id(), $paymentProviderId)
                ->where($this->paymentGatewayConfigRepo->status(), 'active')
                ->first();

            if (!$gatewayConfig || !$gatewayConfig->gateway) {
                throw new \Exception('Invalid payment provider.', 422);
            }

            if ($paymentMethodId > 0) {
                $supportedMethodIds = $this->extractMethodTypeIds(
                    $gatewayConfig->gateway->{$this->paymentGatewayRepo->supportedMethods()} ?? null
                );
                $supportedMethodIds = collect($supportedMethodIds)
                    ->map(static fn($id) => (int) $id)
                    ->filter(static fn($id) => $id > 0)
                    ->unique()
                    ->values();

                if ($supportedMethodIds->isNotEmpty() && !$supportedMethodIds->contains($paymentMethodId)) {
                    throw new \Exception('Payment method is not supported by this provider.', 422);
                }
            }
        }

        $unitPrice = (float) (
            $package->{$this->packageRepo->finalPrice()}
            ?? $package->{$this->packageRepo->totalPrice()}
            ?? 0
        );

        $subtotal = $unitPrice * count($candidateIds);

        $gstConfig = $this->configurationRepo->query()->where($this->configurationRepo->configKey(), 'gst_percentage')->first();
        $taxPercentage = $gstConfig ? (float) $gstConfig->config_value : 18;
        $taxAmount = $subtotal * ($taxPercentage / 100);
        $totalAmount = $subtotal + $taxAmount;

        if ($orderId > 0) {
            $orderRow = $this->candidateOrderRepo->query()
                ->where($this->candidateOrderRepo->id(), $orderId)
                ->where($this->candidateOrderRepo->clientId(), $clientId)
                ->first();

            if (!$orderRow) {
                throw new \Exception('Order not found.', 404);
            }

            $existingStatus = (string) ($orderRow->{$this->candidateOrderRepo->status()} ?? '');
            $isExistingDraft = $existingStatus === OrderStatus::DRAFT->value;

            if ($hasCandidateIds && !$isExistingDraft) {
                throw new \Exception('Candidates cannot be changed after order is created.', 422);
            }

            $orderStatus = $isDraft ? OrderStatus::DRAFT->value : OrderStatus::PENDING->value;

            $updateData = [
                $this->candidateOrderRepo->packageId() => $package->{$this->packageRepo->id()},
                $this->candidateOrderRepo->status() => $orderStatus,
                $this->candidateOrderRepo->updatedBy() => $user?->id,
            ];

            if ($paymentMethodId > 0) {
                $updateData[$this->candidateOrderRepo->paymentMethod()] = (string) $paymentMethodId;
            }

            if ($gatewayConfig) {
                $updateData[$this->candidateOrderRepo->billingConfigId()] = $gatewayConfig->{$this->paymentGatewayConfigRepo->id()};
            }

            if ($hasCandidateIds) {
                $updateData[$this->candidateOrderRepo->subtotal()] = $subtotal;
                $updateData[$this->candidateOrderRepo->discountAmount()] = 0;
                $updateData[$this->candidateOrderRepo->taxAmount()] = $taxAmount;
                $updateData[$this->candidateOrderRepo->taxPercentage()] = $taxPercentage;
                $updateData[$this->candidateOrderRepo->totalAmount()] = $totalAmount;
            }

            $orderRow->update($updateData);

            if ($hasCandidateIds && $isExistingDraft) {
                $this->orderCandidateRepo->query()
                    ->where($this->orderCandidateRepo->orderId(), $orderRow->{$this->candidateOrderRepo->id()})
                    ->delete();

                foreach ($candidateIds as $candidateId) {
                    $this->orderCandidateRepo->create([
                        $this->orderCandidateRepo->orderId() => $orderRow->{$this->candidateOrderRepo->id()},
                        $this->orderCandidateRepo->candidateId() => $candidateId,
                        $this->orderCandidateRepo->subtotal() => $unitPrice,
                        $this->orderCandidateRepo->discountAmount() => 0,
                        $this->orderCandidateRepo->taxAmount() => $unitPrice * ($taxPercentage / 100),
                        $this->orderCandidateRepo->totalAmount() => $unitPrice + ($unitPrice * ($taxPercentage / 100)),
                        $this->orderCandidateRepo->status() => 'pending',
                        $this->orderCandidateRepo->createdBy() => $user?->id,
                    ]);
                }
            }

            return [
                'id' => $orderRow->{$this->candidateOrderRepo->id()},
                'order_number' => $orderRow->{$this->candidateOrderRepo->orderNumber()},
                'package_id' => $orderRow->{$this->candidateOrderRepo->packageId()},
                'payment_provider_id' => $gatewayConfig ? $gatewayConfig->{$this->paymentGatewayConfigRepo->id()} : null,
                'payment_provider_name' => $gatewayConfig?->gateway?->{$this->paymentGatewayRepo->gatewayName()},
                'payment_method_id' => $paymentMethodId > 0 ? $paymentMethodId : (int) ($orderRow->{$this->candidateOrderRepo->paymentMethod()} ?? 0),
                'billing_config_id' => $orderRow->{$this->candidateOrderRepo->billingConfigId()},
                'order_type' => $orderRow->{$this->candidateOrderRepo->orderType()},
                'subtotal' => (float) $orderRow->{$this->candidateOrderRepo->subtotal()},
                'total_amount' => (float) $orderRow->{$this->candidateOrderRepo->totalAmount()},
                'total_amount_in_paise' => (int) round((float) $orderRow->{$this->candidateOrderRepo->totalAmount()} * 100),
                'payment_status' => $orderRow->{$this->candidateOrderRepo->paymentStatus()},
                'status' => $orderRow->{$this->candidateOrderRepo->status()},
                'candidate_ids' => $candidateIds,
            ];
        }

        $created = DB::transaction(function () use ($package, $candidateIds, $clientId, $user, $unitPrice, $subtotal, $gatewayConfig, $paymentMethodId, $isDraft, $taxAmount, $taxPercentage, $totalAmount) {
            $orderNumber = $this->generateOrderNumber($clientId);

            $orderStatus = $isDraft ? OrderStatus::DRAFT->value : OrderStatus::PENDING->value;

            $order = $this->candidateOrderRepo->create([
                $this->candidateOrderRepo->orderNumber() => $orderNumber,
                $this->candidateOrderRepo->clientId() => $clientId,
                $this->candidateOrderRepo->packageId() => $package->{$this->packageRepo->id()},
                $this->candidateOrderRepo->orderType() => 'package',
                $this->candidateOrderRepo->subtotal() => $subtotal,
                $this->candidateOrderRepo->discountAmount() => 0,
                $this->candidateOrderRepo->taxAmount() => $taxAmount,
                $this->candidateOrderRepo->taxPercentage() => $taxPercentage,
                $this->candidateOrderRepo->totalAmount() => $totalAmount,
                $this->candidateOrderRepo->paymentStatus() => 'pending',
                $this->candidateOrderRepo->paymentMethod() => (string) $paymentMethodId,
                $this->candidateOrderRepo->billingConfigId() => $gatewayConfig ? $gatewayConfig->{$this->paymentGatewayConfigRepo->id()} : 0,
                $this->candidateOrderRepo->status() => $orderStatus,
                $this->candidateOrderRepo->createdBy() => $user?->id,
            ]);

            $candidateRows = [];
            foreach ($candidateIds as $candidateId) {
                $candidateRows[] = $this->orderCandidateRepo->create([
                    $this->orderCandidateRepo->orderId() => $order->{$this->candidateOrderRepo->id()},
                    $this->orderCandidateRepo->candidateId() => $candidateId,
                    $this->orderCandidateRepo->subtotal() => $unitPrice,
                    $this->orderCandidateRepo->discountAmount() => 0,
                    $this->orderCandidateRepo->taxAmount() => $unitPrice * ($taxPercentage / 100),
                    $this->orderCandidateRepo->totalAmount() => $unitPrice + ($unitPrice * ($taxPercentage / 100)),
                    $this->orderCandidateRepo->status() => 'pending',
                    $this->orderCandidateRepo->createdBy() => $user?->id,
                ]);
            }

            return [$order, $candidateRows];
        });

        [$order, $orderCandidateRows] = $created;

        $orderConfimationTemplate = $this->emailTemplateRepo->findActiveByCode(
            EmailTemplateCode::CLIENT_ORDER_CONFIRMATION->value
        );

        if ($orderConfimationTemplate) {
            $clientCompanyName = $client->{$this->clientRepo->companyName()};

            $rendered = $this->emailTemplateService->renderTemplate($orderConfimationTemplate, [
                'client_company_name' => $clientCompanyName,
                'client_order_id' => $order->{$this->candidateOrderRepo->orderNumber()} ?? null,
                'company_name' => (string) config('app.name') ?? env('APP_NAME'),
            ]);

            $this->emailQueueRepo->create([
                $this->emailQueueRepo->emailUid() => 'email_' . Str::uuid(),
                $this->emailQueueRepo->toEmail() => $client?->{$this->clientRepo->email()} ?? null,
                $this->emailQueueRepo->toName() => $clientCompanyName,
                $this->emailQueueRepo->subject() => (string) ($rendered['subject'] ?? ''),
                $this->emailQueueRepo->bodyHtml() => $rendered['body_html'] ?? null,
                $this->emailQueueRepo->bodyText() => $rendered['body_text'] ?? null,
                $this->emailQueueRepo->templateId() => $orderConfimationTemplate->{$this->emailTemplateRepo->id()},
                $this->emailQueueRepo->emailType() => (string) ($orderConfimationTemplate->{$this->emailTemplateRepo->emailType()} ?? 'client_order_confirmation'),
                $this->emailQueueRepo->priority() => (string) ($orderConfimationTemplate->{$this->emailTemplateRepo->defaultPriority()} ?? EmailPriority::NORMAL->value),
                $this->emailQueueRepo->clientId() => $clientId,
                $this->emailQueueRepo->candidateId() => 0,
                $this->emailQueueRepo->userId() => $user?->{$this->userRepo->id()},
                $this->emailQueueRepo->assignedServerId() => $orderConfimationTemplate->{$this->emailTemplateRepo->serverId()},
                $this->emailQueueRepo->status() => EmailQueueStatus::PENDING->value,
                $this->emailQueueRepo->attempts() => 0,
                $this->emailQueueRepo->maxAttempts() => 3,
                $this->emailQueueRepo->scheduledAt() => now(),
                $this->emailQueueRepo->expiresAt() => now()->addMinutes(30),
            ]);
        }

        try {
            // Generate local invoice
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad($order->{$this->candidateOrderRepo->id()}, 5, '0', STR_PAD_LEFT);
            $productKey = $package->{$this->packageRepo->packageName()} ?? 'Package order';

            $localInvoice = $this->invoiceRepo->create([
                $this->invoiceRepo->clientId() => $client->{$this->clientRepo->id()},
                $this->invoiceRepo->orderId() => $order->{$this->candidateOrderRepo->id()},
                $this->invoiceRepo->billingConfigId() => $order->{$this->candidateOrderRepo->billingConfigId()},
                $this->invoiceRepo->externalInvoiceId() => null,
                $this->invoiceRepo->externalInvoiceNumber() => null,
                $this->invoiceRepo->invoiceNumber() => $invoiceNumber,
                $this->invoiceRepo->invoiceDate() => date('Y-m-d'),
                $this->invoiceRepo->subtotal() => $subtotal,
                $this->invoiceRepo->totalAmount() => $totalAmount,
                $this->invoiceRepo->amountDue() => $totalAmount,
                $this->invoiceRepo->status() => 'sent',
                $this->invoiceRepo->paymentStatus() => 'unpaid',
                $this->invoiceRepo->syncStatus() => 'manual',
                $this->invoiceRepo->lastSyncAt() => now(),
                $this->invoiceRepo->createdBy() => $user?->id ?? null,
            ]);

            $this->invoiceItemRepo->create([
                $this->invoiceItemRepo->invoiceId() => $localInvoice->id,
                $this->invoiceItemRepo->itemType() => 'package',
                $this->invoiceItemRepo->description() => $productKey,
                $this->invoiceItemRepo->quantity() => count($candidateIds),
                $this->invoiceItemRepo->unitPrice() => $unitPrice,
                $this->invoiceItemRepo->totalPrice() => $totalAmount,
                $this->invoiceItemRepo->externalItemId() => null,
            ]);

            $order->update([
                $this->candidateOrderRepo->invoiceId() => $localInvoice->id,
                $this->candidateOrderRepo->invoiceNumber() => $invoiceNumber,
                $this->candidateOrderRepo->billingSyncStatus() => 'manual',
                $this->candidateOrderRepo->invoiceGeneratedAt() => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error("Failed to generate local invoice for order {$order->{$this->candidateOrderRepo->id()}}: " . $e->getMessage());
        }

        return [
            'id' => $order->{$this->candidateOrderRepo->id()},
            'order_number' => $order->{$this->candidateOrderRepo->orderNumber()},
            'package_id' => $order->{$this->candidateOrderRepo->packageId()},
            'payment_provider_id' => $paymentProviderId,
            'payment_provider_name' => $gatewayConfig ? $gatewayConfig->gateway->{$this->paymentGatewayRepo->gatewayName()} : null,
            'payment_method_id' => $paymentMethodId,
            'billing_config_id' => $order->{$this->candidateOrderRepo->billingConfigId()},
            'order_type' => $order->{$this->candidateOrderRepo->orderType()},
            'subtotal' => (float) $order->{$this->candidateOrderRepo->subtotal()},
            'total_amount' => (float) $order->{$this->candidateOrderRepo->totalAmount()},
            'total_amount_in_paise' => (int) round((float) $order->{$this->candidateOrderRepo->totalAmount()} * 100),
            'payment_status' => $order->{$this->candidateOrderRepo->paymentStatus()},
            'status' => $order->{$this->candidateOrderRepo->status()},
            'candidate_ids' => collect($orderCandidateRows)
                ->pluck($this->orderCandidateRepo->candidateId())
                ->map(static fn($id) => (int) $id)
                ->values()
                ->all(),
        ];
    }

    public function getOrder(int $orderId, int $clientId): array
    {
        $orderRow = $this->candidateOrderRepo->query()
            ->where($this->candidateOrderRepo->id(), $orderId)
            ->where($this->candidateOrderRepo->clientId(), $clientId)
            ->first();

        if (!$orderRow) {
            throw new \Exception('Order not found.', 404);
        }

        $orderData = $orderRow->toArray();
        $orderData['total_amount_in_paise'] = $orderData['total_amount'] * 100;

        $orderCandidateRows = $this->orderCandidateRepo->query()
            ->where($this->orderCandidateRepo->orderId(), $orderId)
            ->get();

        $candidateIds = $orderCandidateRows
            ->pluck($this->orderCandidateRepo->candidateId())
            ->map(static fn($id) => (int) $id)
            ->filter(static fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $candidatesById = $candidateIds === []
            ? collect()
            : $this->candidateRepo->query()
            ->whereIn($this->candidateRepo->id(), $candidateIds)
            ->get()
            ->keyBy($this->candidateRepo->id());

        $candidates = $orderCandidateRows
            ->map(function ($row) use ($candidatesById) {
                $candidateId = (int) ($row->{$this->orderCandidateRepo->candidateId()} ?? 0);
                $candidate = $candidatesById->get($candidateId);

                $candidateData = $row->{$this->orderCandidateRepo->candidateData()} ?? null;
                if (is_string($candidateData) && $candidateData !== '') {
                    $decoded = json_decode($candidateData, true);
                    $candidateData = is_array($decoded) ? $decoded : $candidateData;
                }

                return [
                    'id' => (int) ($row->{$this->orderCandidateRepo->id()} ?? 0),
                    'order_id' => (int) ($row->{$this->orderCandidateRepo->orderId()} ?? 0),
                    'candidate_id' => $candidateId,
                    'subtotal' => $row->{$this->orderCandidateRepo->subtotal()},
                    'discount_amount' => $row->{$this->orderCandidateRepo->discountAmount()},
                    'tax_amount' => $row->{$this->orderCandidateRepo->taxAmount()},
                    'total_amount' => $row->{$this->orderCandidateRepo->totalAmount()},
                    'status' => $row->{$this->orderCandidateRepo->status()},
                    'candidate' => $candidateData ?? ($candidate ? $candidate->toArray() : null),
                    'candidate_data' => $candidateData,
                ];
            })
            ->values()
            ->all();

        $paymentMethod = null;
        $paymentMethodId = (int) ($orderRow->{$this->candidateOrderRepo->paymentMethod()} ?? 0);
        if ($paymentMethodId > 0) {
            $methodRow = $this->paymentMethodTypeRepo->query()
                ->where($this->paymentMethodTypeRepo->id(), $paymentMethodId)
                ->select([
                    $this->paymentMethodTypeRepo->id(),
                    $this->paymentMethodTypeRepo->methodName(),
                    $this->paymentMethodTypeRepo->methodCode(),
                    $this->paymentMethodTypeRepo->category(),
                    $this->paymentMethodTypeRepo->icon(),
                    $this->paymentMethodTypeRepo->description(),
                    $this->paymentMethodTypeRepo->displayOrder(),
                ])
                ->first();

            if ($methodRow) {
                $paymentMethod = [
                    'id' => (int) $methodRow->{$this->paymentMethodTypeRepo->id()},
                    'method_name' => $methodRow->{$this->paymentMethodTypeRepo->methodName()},
                    'method_code' => $methodRow->{$this->paymentMethodTypeRepo->methodCode()},
                    'category' => $methodRow->{$this->paymentMethodTypeRepo->category()},
                    'icon' => $methodRow->{$this->paymentMethodTypeRepo->icon()},
                    'description' => $methodRow->{$this->paymentMethodTypeRepo->description()},
                    'display_order' => (int) ($methodRow->{$this->paymentMethodTypeRepo->displayOrder()} ?? 0),
                ];
            }
        }

        $paymentGateway = null;
        $gatewayConfigId = (int) ($orderRow->{$this->candidateOrderRepo->billingConfigId()} ?? 0);
        if ($gatewayConfigId > 0) {
            $gatewayConfig = $this->paymentGatewayConfigRepo->query()
                ->select([
                    $this->paymentGatewayConfigRepo->id(),
                    $this->paymentGatewayConfigRepo->gatewayId(),
                    $this->paymentGatewayConfigRepo->configName(),
                    $this->paymentGatewayConfigRepo->environment(),
                    $this->paymentGatewayConfigRepo->baseUrl(),
                    $this->paymentGatewayConfigRepo->enabledMethods(),
                    $this->paymentGatewayConfigRepo->currencies(),
                    $this->paymentGatewayConfigRepo->minAmount(),
                    $this->paymentGatewayConfigRepo->maxAmount(),
                    $this->paymentGatewayConfigRepo->transactionFeeType(),
                    $this->paymentGatewayConfigRepo->transactionFeeFixed(),
                    $this->paymentGatewayConfigRepo->transactionFeePercentage(),
                    $this->paymentGatewayConfigRepo->status(),

                ])
                ->with([
                    'gateway' => function ($query) {
                        $query->select([
                            $this->paymentGatewayRepo->id(),
                            $this->paymentGatewayRepo->gatewayName(),
                            $this->paymentGatewayRepo->gatewayCode(),
                            $this->paymentGatewayRepo->providerCompany(),
                            $this->paymentGatewayRepo->logo(),
                            $this->paymentGatewayRepo->website(),
                            $this->paymentGatewayRepo->supportedMethods(),
                            $this->paymentGatewayRepo->isActive(),
                            $this->paymentGatewayRepo->displayOrder(),
                        ]);
                    },
                ])
                ->where($this->paymentGatewayConfigRepo->id(), $gatewayConfigId)
                ->first();

            if ($gatewayConfig) {
                $gateway = $gatewayConfig->gateway;

                $paymentGateway = [
                    'gateway_config_id' => (int) ($gatewayConfig->{$this->paymentGatewayConfigRepo->id()} ?? 0),
                    'gateway_id' => (int) ($gatewayConfig->{$this->paymentGatewayConfigRepo->gatewayId()} ?? 0),
                    'config_name' => $gatewayConfig->{$this->paymentGatewayConfigRepo->configName()},
                    'environment' => $gatewayConfig->{$this->paymentGatewayConfigRepo->environment()},
                    'base_url' => $gatewayConfig->{$this->paymentGatewayConfigRepo->baseUrl()},
                    'enabled_methods' => $gatewayConfig->{$this->paymentGatewayConfigRepo->enabledMethods()},
                    'currencies' => $gatewayConfig->{$this->paymentGatewayConfigRepo->currencies()},
                    'min_amount' => $gatewayConfig->{$this->paymentGatewayConfigRepo->minAmount()},
                    'max_amount' => $gatewayConfig->{$this->paymentGatewayConfigRepo->maxAmount()},
                    'transaction_fee_type' => $gatewayConfig->{$this->paymentGatewayConfigRepo->transactionFeeType()},
                    'transaction_fee_fixed' => $gatewayConfig->{$this->paymentGatewayConfigRepo->transactionFeeFixed()},
                    'transaction_fee_percentage' => $gatewayConfig->{$this->paymentGatewayConfigRepo->transactionFeePercentage()},
                    'is_active' => $gatewayConfig->{$this->paymentGatewayConfigRepo->status()} === 'active' ? 1 : 0,

                    'gateway' => $gateway ? [
                        'id' => (int) ($gateway->{$this->paymentGatewayRepo->id()} ?? 0),
                        'gateway_name' => $gateway->{$this->paymentGatewayRepo->gatewayName()},
                        'gateway_code' => $gateway->{$this->paymentGatewayRepo->gatewayCode()},
                        'provider_company' => $gateway->{$this->paymentGatewayRepo->providerCompany()},
                        'logo' => $gateway->{$this->paymentGatewayRepo->logo()},
                        'website' => $gateway->{$this->paymentGatewayRepo->website()},
                        'supported_methods' => $gateway->{$this->paymentGatewayRepo->supportedMethods()},
                        'is_active' => (int) ($gateway->{$this->paymentGatewayRepo->isActive()} ?? 0),
                        'display_order' => (int) ($gateway->{$this->paymentGatewayRepo->displayOrder()} ?? 0),
                    ] : null,
                ];
            }
        }

        $transactions = $this->paymentTransactionRepo->query()
            ->where($this->paymentTransactionRepo->orderId(), $orderId)
            ->where($this->paymentTransactionRepo->clientId(), $clientId)
            ->orderByDesc($this->paymentTransactionRepo->id())
            ->get()
            ->map(static fn($row) => $row->toArray())
            ->values()
            ->all();

        if (!empty($candidates)) {
            $candidateIdsForServiceData = array_filter(array_map(function ($item) {
                return $item['candidate_id'] ?? null;
            }, $candidates));

            if (!empty($candidateIdsForServiceData)) {
                $candidatesMap = $this->candidateRepo->query()->whereIn('id', $candidateIdsForServiceData)
                    ->get()
                    ->keyBy('id');

                $candidateServices = $this->candidateServiceRepo->query()->whereIn('candidate_id', $candidateIdsForServiceData)
                    ->get();
                $candidateServiceIds = $candidateServices->pluck('id')->toArray();

                $candidateServiceData = [];
                if (!empty($candidateServiceIds)) {
                    $candidateServiceData = $this->candidateServiceDataRepo->query()->whereIn('candidate_service_id', $candidateServiceIds)
                        ->join('services_fields', 'candidate_service_data.field_id', '=', 'services_fields.id')
                        ->join('services', 'services_fields.service_id', '=', 'services.id')
                        ->select(
                            'candidate_service_data.*',
                            'services_fields.field_name',
                            'services_fields.field_label',
                            'services_fields.field_type',
                            'services.service_name',
                            'services.service_code',
                            'candidate_services.candidate_id'
                        )
                        ->join('candidate_services', 'candidate_service_data.candidate_service_id', '=', 'candidate_services.id')
                        ->get()
                        ->groupBy('candidate_id');
                }

                foreach ($candidates as &$candidateItem) {
                    $candidateId = $candidateItem['candidate_id'] ?? null;
                    $candidateDetails = $candidateId && isset($candidatesMap[$candidateId])
                        ? $candidatesMap[$candidateId]->toArray()
                        : null;

                    if ($candidateDetails && isset($candidateServiceData[$candidateId])) {
                        $candidateDetails['service_data'] = $candidateServiceData[$candidateId]->toArray();
                    } else if ($candidateDetails) {
                        $candidateDetails['service_data'] = [];
                    }

                    $candidateItem['candaite_details'] = $candidateDetails;
                    $candidateItem['candidate_details'] = $candidateDetails;
                }
            } else {
                foreach ($candidates as &$candidateItem) {
                    $candidateItem['candaite_details'] = null;
                    $candidateItem['candidate_details'] = null;
                }
            }
        }

        return [
            'order' => $orderData,
            'candidates' => $candidates,
            'payment_method' => $paymentMethod,
            'payment_gateway' => $paymentGateway,
            'transactions' => $transactions,
        ];
    }

    public function initiateOrderPayment(int $orderId, array $payload, int $clientId, ?object $user, string $ip, string $userAgent): array
    {
        $orderRow = $this->candidateOrderRepo->query()
            ->where($this->candidateOrderRepo->id(), $orderId)
            ->where($this->candidateOrderRepo->clientId(), $clientId)
            ->first();

        if (!$orderRow) {
            throw new \Exception('Order not found.', 404);
        }

        $paymentStatus = strtolower(trim((string) ($orderRow->{$this->candidateOrderRepo->paymentStatus()} ?? '')));
        if (in_array($paymentStatus, ['paid', 'success', 'completed'], true)) {
            throw new \Exception('Payment is already completed for this order.', 422);
        }

        $gatewayConfigId = (int) ($orderRow->{$this->candidateOrderRepo->billingConfigId()} ?? 0);
        if ($gatewayConfigId <= 0) {
            throw new \Exception('Payment gateway configuration not found for this order.', 422);
        }

        $gatewayConfig = $this->paymentGatewayConfigRepo->query()
            ->with(['gateway'])
            ->where($this->paymentGatewayConfigRepo->id(), $gatewayConfigId)
            ->where($this->paymentGatewayConfigRepo->status(), UserStatus::ACTIVE)
            ->first();

        if (!$gatewayConfig || !$gatewayConfig->gateway) {
            throw new \Exception('Invalid payment provider configuration.', 422);
        }

        $providerName = trim((string) ($payload['payment_provider_name'] ?? ''));
        $gatewayName = (string) ($gatewayConfig->gateway->{$this->paymentGatewayRepo->gatewayName()} ?? '');
        $gatewayCode = (string) ($gatewayConfig->gateway->{$this->paymentGatewayRepo->gatewayCode()} ?? '');

        if ($providerName !== '' && !$this->providerMatches($providerName, $gatewayName, $gatewayCode)) {
            throw new \Exception('Payment provider does not match the order gateway.', 422);
        }

        $orderAmount = (float) ($orderRow->{$this->candidateOrderRepo->totalAmount()} ?? 0);
        $payloadAmount = (float) ($payload['total_amount'] ?? 0);
        $amount = $payloadAmount > 0 ? $payloadAmount : $orderAmount;

        if ($amount <= 0) {
            throw new \Exception('Invalid total amount for payment.', 422);
        }

        if ($orderAmount > 0 && abs($amount - $orderAmount) > 0.01) {
            throw new \Exception('Total amount does not match the order amount.', 422);
        }

        $expectedPaise = (int) round($amount * 100);
        $payloadPaise = (int) ($payload['total_amount_in_paise'] ?? 0);
        if ($payloadPaise > 0 && $payloadPaise !== $expectedPaise) {
            throw new \Exception('Total amount in paise does not match the order amount.', 422);
        }

        $amountInPaise = $payloadPaise > 0 ? $payloadPaise : $expectedPaise;

        $currency = $this->resolveCurrency(
            $gatewayConfig->{$this->paymentGatewayConfigRepo->currencies()} ?? null
        ) ?? 'INR';

        $gatewayPayload = [
            'order_id' => $orderId,
            'order_number' => (string) ($orderRow->{$this->candidateOrderRepo->orderNumber()} ?? ''),
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
        } catch (\Throwable $e) {
            Log::error("Initiate payment for order $orderId error: " . $e->getMessage());
            throw new \Exception($e->getMessage(), $e->getCode() ?: 500);
        }

        $transactionPayload = [
            $this->paymentTransactionRepo->transactionUuid() => (string) Str::uuid(),
            $this->paymentTransactionRepo->clientId() => $clientId,
            $this->paymentTransactionRepo->orderId() => $orderId,
            $this->paymentTransactionRepo->invoiceId() => 0,
            $this->paymentTransactionRepo->gatewayConfigId() => $gatewayConfigId,
            $this->paymentTransactionRepo->amount() => $amount,
            $this->paymentTransactionRepo->currency() => $currency,
            $this->paymentTransactionRepo->paymentStatus() => 'initiated',
            $this->paymentTransactionRepo->status() => 'pending',
            $this->paymentTransactionRepo->initiatedAt() => now(),
            $this->paymentTransactionRepo->gatewayOrderId() => $gatewayResponse['gateway_order_id'] ?? null,
            $this->paymentTransactionRepo->gatewayRequest() => json_encode($gatewayPayload),
            $this->paymentTransactionRepo->gatewayResponse() => json_encode($gatewayResponse),
            $this->paymentTransactionRepo->ipAddress() => $ip,
            $this->paymentTransactionRepo->userAgent() => $userAgent,
            $this->paymentTransactionRepo->createdBy() => $user?->id,
        ];

        $paymentMethodId = (int) ($orderRow->{$this->candidateOrderRepo->paymentMethod()} ?? 0);
        if ($paymentMethodId > 0) {
            $transactionPayload[$this->paymentTransactionRepo->methodTypeId()] = $paymentMethodId;
        }

        $transaction = $this->paymentTransactionRepo->create($transactionPayload);

        return [
            'order_id' => $orderId,
            'transaction_uuid' => $transaction->{$this->paymentTransactionRepo->transactionUuid()} ?? null,
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
    }

    public function completeOrderPayment(int $orderId, array $payload, int $clientId, ?object $user): array
    {
        $orderRow = $this->candidateOrderRepo->query()
            ->where($this->candidateOrderRepo->id(), $orderId)
            ->where($this->candidateOrderRepo->clientId(), $clientId)
            ->first();

        if (!$orderRow) {
            throw new \Exception('Order not found.', 404);
        }

        $transactionUuid = trim((string) ($payload['transaction_uuid'] ?? ''));
        $gatewayPaymentId = trim((string) ($payload['payment_id'] ?? ''));
        $gatewayOrderId = trim((string) ($payload['order_id'] ?? ''));
        $provider = trim((string) ($payload['provider'] ?? ''));
        $signature = $payload['signature'] ?? null;
        $gatewayData = $payload['gateway_data'] ?? [];

        $transaction = $this->paymentTransactionRepo->query()
            ->where($this->paymentTransactionRepo->transactionUuid(), $transactionUuid)
            ->where($this->paymentTransactionRepo->orderId(), $orderId)
            ->where($this->paymentTransactionRepo->clientId(), $clientId)
            ->first();

        if (!$transaction && $gatewayOrderId !== '') {
            $transaction = $this->paymentTransactionRepo->query()
                ->where($this->paymentTransactionRepo->gatewayOrderId(), $gatewayOrderId)
                ->where($this->paymentTransactionRepo->orderId(), $orderId)
                ->where($this->paymentTransactionRepo->clientId(), $clientId)
                ->first();
        }

        if (!$transaction) {
            throw new \Exception('Payment transaction not found.', 404);
        }

        $currentStatus = strtolower(trim((string) ($transaction->{$this->paymentTransactionRepo->paymentStatus()} ?? '')));
        if (in_array($currentStatus, ['paid', 'success', 'completed'], true)) {
            return [
                'order_id' => $orderId,
                'transaction_uuid' => $transaction->{$this->paymentTransactionRepo->transactionUuid()},
            ];
        }

        $gatewayPayload = [
            'provider' => $provider,
            'payment_id' => $gatewayPaymentId,
            'order_id' => $gatewayOrderId,
            'signature' => $signature,
            'gateway_data' => $gatewayData,
        ];

        $localInvoice = $this->invoiceRepo->query()
            ->where($this->invoiceRepo->orderId(), $orderId)
            ->first();

        $transaction->update([
            $this->paymentTransactionRepo->gatewayOrderId() => $gatewayOrderId ?: $transaction->{$this->paymentTransactionRepo->gatewayOrderId()},
            $this->paymentTransactionRepo->gatewayPaymentId() => $gatewayPaymentId,
            $this->paymentTransactionRepo->paymentStatus() => 'success',
            $this->paymentTransactionRepo->status() => 'completed',
            $this->paymentTransactionRepo->successAt() => now(),
            $this->paymentTransactionRepo->gatewayResponse() => json_encode($gatewayPayload),
            $this->paymentTransactionRepo->paymentDetails() => json_encode($gatewayData),
            $this->paymentTransactionRepo->updatedBy() => $user?->id,
            $this->paymentTransactionRepo->invoiceId() => $localInvoice ? $localInvoice->id : 0,
        ]);

        $orderRow->update([
            $this->candidateOrderRepo->paymentStatus() => 'paid',
            $this->candidateOrderRepo->paymentReference() => $gatewayPaymentId,
            $this->candidateOrderRepo->status() => OrderStatus::PROCESSING->value,
            $this->candidateOrderRepo->processedAt() => now(),
            $this->candidateOrderRepo->updatedBy() => $user?->id,
        ]);

        try {
            if ($localInvoice) {
                $localInvoice->update([
                    $this->invoiceRepo->paymentStatus() => 'paid',
                    $this->invoiceRepo->status() => 'paid',
                ]);
            }
        } catch (\Throwable $e) {
            Log::error("Failed to process invoice payment for order {$orderId}: " . $e->getMessage());
        }

        return [
            'order_id' => $orderId,
            'order_status' => $orderRow->{$this->candidateOrderRepo->status()},
            'payment_status' => $orderRow->{$this->candidateOrderRepo->paymentStatus()},
            'transaction_uuid' => $transaction->{$this->paymentTransactionRepo->transactionUuid()},
            'gateway_payment_id' => $gatewayPaymentId,
            'gateway_order_id' => $gatewayOrderId,
        ];
    }

    protected function generateOrderNumber(int $clientId): string
    {
        do {
            $code = 'ORD-' . $clientId . '-' . Str::upper(Str::random(6));
            $exists = $this->candidateOrderRepo->query()
                ->where($this->candidateOrderRepo->orderNumber(), $code)
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
