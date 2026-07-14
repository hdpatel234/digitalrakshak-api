<?php

namespace App\Services\ApiService\Client;

use App\Enums\EmailPriority;
use App\Enums\EmailQueueStatus;
use App\Enums\EmailTemplateCode;
use App\Enums\OrderStatus;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Services\BaseService;
use App\Services\CandidateOrderService;
use App\Services\CandidateService;
use App\Services\ClientService;
use App\Services\EmailQueueService;
use App\Services\EmailTemplateService;
use App\Services\InvoiceService;
use App\Services\OrderCandidateService;
use App\Services\PackageService;
use App\Services\PaymentGateway\PaymentGatewayDriverFactory;
use App\Services\PaymentGatewayConfigService;
use App\Services\PaymentGatewayService;
use App\Services\PaymentMethodTypeService;
use App\Services\PaymentTransactionService;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderService extends BaseService
{
    public function __construct(
        protected ClientService $clientService,
        protected CandidateOrderService $candidateOrderService,
        protected OrderCandidateService $orderCandidateService,
        protected PackageService $packageService,
        protected CandidateService $candidateService,
        protected PaymentGatewayDriverFactory $paymentGatewayDriverFactory,
        protected PaymentGatewayConfigService $paymentGatewayConfigService,
        protected PaymentGatewayService $paymentGatewayService,
        protected PaymentMethodTypeService $paymentMethodTypeService,
        protected PaymentTransactionService $paymentTransactionService,
        protected EmailTemplateService $emailTemplateService,
        protected EmailQueueService $emailQueueService,
        protected UserService $userService,
        protected InvoiceService $invoiceService
    ) {}

    public function getOrders(array $params, int $clientId): array
    {
        $orderTable = $this->candidateOrderService->query()->getModel()->getTable();
        $statusColumn = $this->candidateOrderService->status();
        $clientIdColumn = $this->candidateOrderService->clientId();
        $orderNumberColumn = $this->candidateOrderService->orderNumber();
        $clientOrderNumberColumn = $this->candidateOrderService->clientOrderNumber();
        $invoiceNumberColumn = $this->candidateOrderService->invoiceNumber();
        $paymentStatusColumn = $this->candidateOrderService->paymentStatus();
        $paymentMethodColumn = $this->candidateOrderService->paymentMethod();
        $orderDateColumn = $this->candidateOrderService->orderDate();

        $qualifiedStatusColumn = $orderTable . '.' . $statusColumn;
        $qualifiedClientIdColumn = $orderTable . '.' . $clientIdColumn;

        $query = $this->candidateOrderService->query()
            ->where($qualifiedClientIdColumn, $clientId);

        if (isset($params['payment_method_id']) && !isset($params['filters']['payment_method_id'])) {
            $params['filters']['payment_method_id'] = $params['payment_method_id'];
        }
        if (isset($params['limit']) && !isset($params['per_page'])) {
            $params['per_page'] = $params['limit'];
        }

        $result = $this->candidateOrderService->datatable(
            query: $query,
            params: $params,
            config: [
                'searchable' => [
                    $orderTable . '.' . $orderNumberColumn,
                    $orderTable . '.' . $clientOrderNumberColumn,
                    $orderTable . '.' . $invoiceNumberColumn,
                ],
                'status_column' => $qualifiedStatusColumn,
                'date_column' => $orderTable . '.' . $this->candidateOrderService->createdAt(),
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
                    $orderTable . '.' . $this->candidateOrderService->id(),
                    $orderTable . '.' . $orderNumberColumn,
                    $orderTable . '.' . $clientOrderNumberColumn,
                    $orderTable . '.' . $paymentStatusColumn,
                    $orderTable . '.' . $this->candidateOrderService->totalAmount(),
                    $orderTable . '.' . $orderDateColumn,
                    $orderTable . '.' . $this->candidateOrderService->createdAt(),
                ],
                'default_sort_by' => $orderTable . '.' . $this->candidateOrderService->createdAt(),
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
            $orderList = collect($result['list'])
                ->map(static fn($item) => is_array($item) ? $item : $item->toArray());

            $billingConfigIds = $orderList
                ->pluck($this->candidateOrderService->billingConfigId())
                ->map(static fn($id) => (int) $id)
                ->filter(static fn($id) => $id > 0)
                ->unique()
                ->values()
                ->all();

            $gatewayConfigsById = $billingConfigIds === []
                ? collect()
                : $this->paymentGatewayConfigService->query()
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
                ->whereIn($this->paymentGatewayConfigService->id(), $billingConfigIds)
                ->get()
                ->keyBy($this->paymentGatewayConfigService->id());

            $result['list'] = $orderList
                ->map(function (array $row) use ($paymentMethodNameById, $paymentMethodColumn, $gatewayConfigsById) {
                    $methodId = (int) ($row[$paymentMethodColumn] ?? 0);
                    $row['payment_method_name'] = $paymentMethodNameById->get($methodId);

                    $paymentStatus = strtolower(trim((string) ($row[$this->candidateOrderService->paymentStatus()] ?? '')));
                    if (!in_array($paymentStatus, ['paid', 'success', 'completed'], true)) {
                        $row['payment_gateway'] = null;
                        return $row;
                    }

                    $billingConfigId = (int) ($row[$this->candidateOrderService->billingConfigId()] ?? 0);
                    $gatewayConfig = $billingConfigId > 0 ? $gatewayConfigsById->get($billingConfigId) : null;

                    if (!$gatewayConfig) {
                        $row['payment_gateway'] = null;
                        return $row;
                    }

                    $gateway = $gatewayConfig->gateway;
                    $row['payment_gateway'] = [
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
                        'is_active' => $gatewayConfig->{$this->paymentGatewayConfigService->isActive()} === 'active' ? 1 : 0,

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
        $client = $this->clientService->query()
            ->where($this->clientService->id(), $clientId)
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
            throw new \Exception('Package not found.', 404);
        }

        if ($candidateIds !== []) {
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
                throw new \Exception('Some candidates are invalid or not accessible.', 422);
            }
        }

        $gatewayConfig = null;
        if ($paymentProviderId > 0) {
            $gatewayConfig = $this->paymentGatewayConfigService->query()
                ->with([
                    'gateway' => function ($query) {
                        $query->where($this->paymentGatewayService->isActive(), 1);
                    }
                ])
                ->where($this->paymentGatewayConfigService->id(), $paymentProviderId)
                ->where($this->paymentGatewayConfigService->isActive(), 'active')
                ->first();

            if (!$gatewayConfig || !$gatewayConfig->gateway) {
                throw new \Exception('Invalid payment provider.', 422);
            }

            if ($paymentMethodId > 0) {
                $supportedMethodIds = $this->extractMethodTypeIds(
                    $gatewayConfig->gateway->{$this->paymentGatewayService->supportedMethods()} ?? null
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
            $package->{$this->packageService->finalPrice()}
            ?? $package->{$this->packageService->totalPrice()}
            ?? 0
        );

        $subtotal = $unitPrice * count($candidateIds);
        
        $gstConfig = \App\Models\Configuration::where('config_key', 'gst_percentage')->first();
        $taxPercentage = $gstConfig ? (float) $gstConfig->config_value : 18;
        $taxAmount = $subtotal * ($taxPercentage / 100);
        $totalAmount = $subtotal + $taxAmount;

        if ($orderId > 0) {
            $orderRow = $this->candidateOrderService->query()
                ->where($this->candidateOrderService->id(), $orderId)
                ->where($this->candidateOrderService->clientId(), $clientId)
                ->first();

            if (!$orderRow) {
                throw new \Exception('Order not found.', 404);
            }

            $existingStatus = (string) ($orderRow->{$this->candidateOrderService->status()} ?? '');
            $isExistingDraft = $existingStatus === OrderStatus::DRAFT->value;

            if ($hasCandidateIds && !$isExistingDraft) {
                throw new \Exception('Candidates cannot be changed after order is created.', 422);
            }

            $orderStatus = $isDraft ? OrderStatus::DRAFT->value : OrderStatus::PENDING->value;

            $updateData = [
                $this->candidateOrderService->packageId() => $package->{$this->packageService->id()},
                $this->candidateOrderService->status() => $orderStatus,
                $this->candidateOrderService->updatedBy() => $user?->id,
            ];

            if ($paymentMethodId > 0) {
                $updateData[$this->candidateOrderService->paymentMethod()] = (string) $paymentMethodId;
            }

            if ($gatewayConfig) {
                $updateData[$this->candidateOrderService->billingConfigId()] = $gatewayConfig->{$this->paymentGatewayConfigService->id()};
            }

            if ($hasCandidateIds) {
                $updateData[$this->candidateOrderService->subtotal()] = $subtotal;
                $updateData[$this->candidateOrderService->discountAmount()] = 0;
                $updateData[$this->candidateOrderService->taxAmount()] = $taxAmount;
                $updateData[$this->candidateOrderService->taxPercentage()] = $taxPercentage;
                $updateData[$this->candidateOrderService->totalAmount()] = $totalAmount;
            }

            $orderRow->update($updateData);

            if ($hasCandidateIds && $isExistingDraft) {
                $this->orderCandidateService->query()
                    ->where($this->orderCandidateService->orderId(), $orderRow->{$this->candidateOrderService->id()})
                    ->delete();

                foreach ($candidateIds as $candidateId) {
                    $this->orderCandidateService->create([
                        $this->orderCandidateService->orderId() => $orderRow->{$this->candidateOrderService->id()},
                        $this->orderCandidateService->candidateId() => $candidateId,
                        $this->orderCandidateService->subtotal() => $unitPrice,
                        $this->orderCandidateService->discountAmount() => 0,
                        $this->orderCandidateService->taxAmount() => $unitPrice * ($taxPercentage / 100),
                        $this->orderCandidateService->totalAmount() => $unitPrice + ($unitPrice * ($taxPercentage / 100)),
                        $this->orderCandidateService->status() => 'pending',
                        $this->orderCandidateService->createdBy() => $user?->id,
                    ]);
                }
            }

            return [
                'id' => $orderRow->{$this->candidateOrderService->id()},
                'order_number' => $orderRow->{$this->candidateOrderService->orderNumber()},
                'package_id' => $orderRow->{$this->candidateOrderService->packageId()},
                'payment_provider_id' => $gatewayConfig ? $gatewayConfig->{$this->paymentGatewayConfigService->id()} : null,
                'payment_provider_name' => $gatewayConfig?->gateway?->{$this->paymentGatewayService->gatewayName()},
                'payment_method_id' => $paymentMethodId > 0 ? $paymentMethodId : (int) ($orderRow->{$this->candidateOrderService->paymentMethod()} ?? 0),
                'billing_config_id' => $orderRow->{$this->candidateOrderService->billingConfigId()},
                'order_type' => $orderRow->{$this->candidateOrderService->orderType()},
                'subtotal' => (float) $orderRow->{$this->candidateOrderService->subtotal()},
                'total_amount' => (float) $orderRow->{$this->candidateOrderService->totalAmount()},
                'total_amount_in_paise' => (int) round((float) $orderRow->{$this->candidateOrderService->totalAmount()} * 100),
                'payment_status' => $orderRow->{$this->candidateOrderService->paymentStatus()},
                'status' => $orderRow->{$this->candidateOrderService->status()},
                'candidate_ids' => $candidateIds,
            ];
        }

        $created = DB::transaction(function () use ($package, $candidateIds, $clientId, $user, $unitPrice, $subtotal, $gatewayConfig, $paymentMethodId, $isDraft, $taxAmount, $taxPercentage, $totalAmount) {
            $orderNumber = $this->generateOrderNumber($clientId);

            $orderStatus = $isDraft ? OrderStatus::DRAFT->value : OrderStatus::PENDING->value;

            $order = $this->candidateOrderService->create([
                $this->candidateOrderService->orderNumber() => $orderNumber,
                $this->candidateOrderService->clientId() => $clientId,
                $this->candidateOrderService->packageId() => $package->{$this->packageService->id()},
                $this->candidateOrderService->orderType() => 'package',
                $this->candidateOrderService->subtotal() => $subtotal,
                $this->candidateOrderService->discountAmount() => 0,
                $this->candidateOrderService->taxAmount() => $taxAmount,
                $this->candidateOrderService->taxPercentage() => $taxPercentage,
                $this->candidateOrderService->totalAmount() => $totalAmount,
                $this->candidateOrderService->paymentStatus() => 'pending',
                $this->candidateOrderService->paymentMethod() => (string) $paymentMethodId,
                $this->candidateOrderService->billingConfigId() => $gatewayConfig ? $gatewayConfig->{$this->paymentGatewayConfigService->id()} : 0,
                $this->candidateOrderService->status() => $orderStatus,
                $this->candidateOrderService->createdBy() => $user?->id,
            ]);

            $candidateRows = [];
            foreach ($candidateIds as $candidateId) {
                $candidateRows[] = $this->orderCandidateService->create([
                    $this->orderCandidateService->orderId() => $order->{$this->candidateOrderService->id()},
                    $this->orderCandidateService->candidateId() => $candidateId,
                    $this->orderCandidateService->subtotal() => $unitPrice,
                    $this->orderCandidateService->discountAmount() => 0,
                    $this->orderCandidateService->taxAmount() => $unitPrice * ($taxPercentage / 100),
                    $this->orderCandidateService->totalAmount() => $unitPrice + ($unitPrice * ($taxPercentage / 100)),
                    $this->orderCandidateService->status() => 'pending',
                    $this->orderCandidateService->createdBy() => $user?->id,
                ]);
            }

            return [$order, $candidateRows];
        });

        [$order, $orderCandidateRows] = $created;

        $orderConfimationTemplate = $this->emailTemplateService->findActiveByCode(
            EmailTemplateCode::CLIENT_ORDER_CONFIRMATION->value
        );

        if ($orderConfimationTemplate) {
            $clientCompanyName = $client->{$this->clientService->companyName()};

            $rendered = $this->emailTemplateService->renderTemplate($orderConfimationTemplate, [
                'client_company_name' => $clientCompanyName,
                'client_order_id' => $order->{$this->candidateOrderService->orderNumber()} ?? null,
                'company_name' => (string) config('app.name') ?? env('APP_NAME'),
            ]);

            $this->emailQueueService->create([
                $this->emailQueueService->emailUid() => 'email_' . Str::uuid(),
                $this->emailQueueService->toEmail() => $client?->{$this->clientService->email()} ?? null,
                $this->emailQueueService->toName() => $clientCompanyName,
                $this->emailQueueService->subject() => (string) ($rendered['subject'] ?? ''),
                $this->emailQueueService->bodyHtml() => $rendered['body_html'] ?? null,
                $this->emailQueueService->bodyText() => $rendered['body_text'] ?? null,
                $this->emailQueueService->templateId() => $orderConfimationTemplate->{$this->emailTemplateService->id()},
                $this->emailQueueService->emailType() => (string) ($orderConfimationTemplate->{$this->emailTemplateService->emailType()} ?? 'client_order_confirmation'),
                $this->emailQueueService->priority() => (string) ($orderConfimationTemplate->{$this->emailTemplateService->defaultPriority()} ?? EmailPriority::NORMAL->value),
                $this->emailQueueService->clientId() => $clientId,
                $this->emailQueueService->candidateId() => 0,
                $this->emailQueueService->userId() => $user?->{$this->userService->id()},
                $this->emailQueueService->assignedServerId() => $orderConfimationTemplate->{$this->emailTemplateService->serverId()},
                $this->emailQueueService->status() => EmailQueueStatus::PENDING->value,
                $this->emailQueueService->attempts() => 0,
                $this->emailQueueService->maxAttempts() => 3,
                $this->emailQueueService->scheduledAt() => now(),
                $this->emailQueueService->expiresAt() => now()->addMinutes(30),
            ]);
        }

        try {
            // Generate local invoice
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad($order->{$this->candidateOrderService->id()}, 5, '0', STR_PAD_LEFT);
            $productKey = $package->{$this->packageService->packageName()} ?? 'Package order';

            $localInvoice = Invoice::create([
                Invoice::CLIENT_ID => $client->{$this->clientService->id()},
                Invoice::ORDER_ID => $order->{$this->candidateOrderService->id()},
                Invoice::BILLING_CONFIG_ID => $order->{$this->candidateOrderService->billingConfigId()},
                Invoice::EXTERNAL_INVOICE_ID => null,
                Invoice::EXTERNAL_INVOICE_NUMBER => null,
                Invoice::INVOICE_NUMBER => $invoiceNumber,
                Invoice::INVOICE_DATE => date('Y-m-d'),
                Invoice::SUBTOTAL => $subtotal,
                Invoice::TOTAL_AMOUNT => $totalAmount,
                Invoice::AMOUNT_DUE => $totalAmount,
                Invoice::STATUS => 'sent',
                Invoice::PAYMENT_STATUS => 'unpaid',
                Invoice::SYNC_STATUS => 'manual',
                Invoice::LAST_SYNC_AT => now(),
                Invoice::CREATED_BY => $user?->id ?? null,
            ]);

            InvoiceItem::create([
                InvoiceItem::INVOICE_ID => $localInvoice->id,
                InvoiceItem::ITEM_TYPE => 'package',
                InvoiceItem::DESCRIPTION => $productKey,
                InvoiceItem::QUANTITY => count($candidateIds),
                InvoiceItem::UNIT_PRICE => $unitPrice,
                InvoiceItem::TOTAL_PRICE => $totalAmount,
                InvoiceItem::EXTERNAL_ITEM_ID => null,
            ]);

            $order->update([
                $this->candidateOrderService->invoiceId() => $localInvoice->id,
                $this->candidateOrderService->invoiceNumber() => $invoiceNumber,
                $this->candidateOrderService->billingSyncStatus() => 'manual',
                $this->candidateOrderService->invoiceGeneratedAt() => now(),
            ]);

        } catch (\Throwable $e) {
            Log::error("Failed to generate local invoice for order {$order->{$this->candidateOrderService->id()}}: " . $e->getMessage());
        }

        return [
            'id' => $order->{$this->candidateOrderService->id()},
            'order_number' => $order->{$this->candidateOrderService->orderNumber()},
            'package_id' => $order->{$this->candidateOrderService->packageId()},
            'payment_provider_id' => $paymentProviderId,
            'payment_provider_name' => $gatewayConfig ? $gatewayConfig->gateway->{$this->paymentGatewayService->gatewayName()} : null,
            'payment_method_id' => $paymentMethodId,
            'billing_config_id' => $order->{$this->candidateOrderService->billingConfigId()},
            'order_type' => $order->{$this->candidateOrderService->orderType()},
            'subtotal' => (float) $order->{$this->candidateOrderService->subtotal()},
            'total_amount' => (float) $order->{$this->candidateOrderService->totalAmount()},
            'total_amount_in_paise' => (int) round((float) $order->{$this->candidateOrderService->totalAmount()} * 100),
            'payment_status' => $order->{$this->candidateOrderService->paymentStatus()},
            'status' => $order->{$this->candidateOrderService->status()},
            'candidate_ids' => collect($orderCandidateRows)
                ->pluck($this->orderCandidateService->candidateId())
                ->map(static fn($id) => (int) $id)
                ->values()
                ->all(),
        ];
    }

    public function getOrder(int $orderId, int $clientId): array
    {
        $orderRow = $this->candidateOrderService->query()
            ->where($this->candidateOrderService->id(), $orderId)
            ->where($this->candidateOrderService->clientId(), $clientId)
            ->first();

        if (!$orderRow) {
            throw new \Exception('Order not found.', 404);
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
        $paymentMethodId = (int) ($orderRow->{$this->candidateOrderService->paymentMethod()} ?? 0);
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
        $gatewayConfigId = (int) ($orderRow->{$this->candidateOrderService->billingConfigId()} ?? 0);
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
                    'is_active' => $gatewayConfig->{$this->paymentGatewayConfigService->isActive()} === 'active' ? 1 : 0,

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
        $orderRow = $this->candidateOrderService->query()
            ->where($this->candidateOrderService->id(), $orderId)
            ->where($this->candidateOrderService->clientId(), $clientId)
            ->first();

        if (!$orderRow) {
            throw new \Exception('Order not found.', 404);
        }

        $paymentStatus = strtolower(trim((string) ($orderRow->{$this->candidateOrderService->paymentStatus()} ?? '')));
        if (in_array($paymentStatus, ['paid', 'success', 'completed'], true)) {
            throw new \Exception('Payment is already completed for this order.', 422);
        }

        $gatewayConfigId = (int) ($orderRow->{$this->candidateOrderService->billingConfigId()} ?? 0);
        if ($gatewayConfigId <= 0) {
            throw new \Exception('Payment gateway configuration not found for this order.', 422);
        }

        $gatewayConfig = $this->paymentGatewayConfigService->query()
            ->with(['gateway'])
            ->where($this->paymentGatewayConfigService->id(), $gatewayConfigId)
            ->where($this->paymentGatewayConfigService->isActive(), 1)
            ->first();

        if (!$gatewayConfig || !$gatewayConfig->gateway) {
            throw new \Exception('Invalid payment provider configuration.', 422);
        }

        $providerName = trim((string) ($payload['payment_provider_name'] ?? ''));
        $gatewayName = (string) ($gatewayConfig->gateway->{$this->paymentGatewayService->gatewayName()} ?? '');
        $gatewayCode = (string) ($gatewayConfig->gateway->{$this->paymentGatewayService->gatewayCode()} ?? '');

        if ($providerName !== '' && !$this->providerMatches($providerName, $gatewayName, $gatewayCode)) {
            throw new \Exception('Payment provider does not match the order gateway.', 422);
        }

        $orderAmount = (float) ($orderRow->{$this->candidateOrderService->totalAmount()} ?? 0);
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
            $gatewayConfig->{$this->paymentGatewayConfigService->currencies()} ?? null
        ) ?? 'INR';

        $gatewayPayload = [
            'order_id' => $orderId,
            'order_number' => (string) ($orderRow->{$this->candidateOrderService->orderNumber()} ?? ''),
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
            $this->paymentTransactionService->transactionUuid() => (string) Str::uuid(),
            $this->paymentTransactionService->clientId() => $clientId,
            $this->paymentTransactionService->orderId() => $orderId,
            $this->paymentTransactionService->invoiceId() => 0,
            $this->paymentTransactionService->gatewayConfigId() => $gatewayConfigId,
            $this->paymentTransactionService->amount() => $amount,
            $this->paymentTransactionService->currency() => $currency,
            $this->paymentTransactionService->paymentStatus() => 'initiated',
            $this->paymentTransactionService->status() => 'pending',
            $this->paymentTransactionService->initiatedAt() => now(),
            $this->paymentTransactionService->gatewayOrderId() => $gatewayResponse['gateway_order_id'] ?? null,
            $this->paymentTransactionService->gatewayRequest() => json_encode($gatewayPayload),
            $this->paymentTransactionService->gatewayResponse() => json_encode($gatewayResponse),
            $this->paymentTransactionService->ipAddress() => $ip,
            $this->paymentTransactionService->userAgent() => $userAgent,
            $this->paymentTransactionService->createdBy() => $user?->id,
        ];

        $paymentMethodId = (int) ($orderRow->{$this->candidateOrderService->paymentMethod()} ?? 0);
        if ($paymentMethodId > 0) {
            $transactionPayload[$this->paymentTransactionService->methodTypeId()] = $paymentMethodId;
        }

        $transaction = $this->paymentTransactionService->create($transactionPayload);

        return [
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
    }

    public function completeOrderPayment(int $orderId, array $payload, int $clientId, ?object $user): array
    {
        $orderRow = $this->candidateOrderService->query()
            ->where($this->candidateOrderService->id(), $orderId)
            ->where($this->candidateOrderService->clientId(), $clientId)
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

        $transaction = $this->paymentTransactionService->query()
            ->where($this->paymentTransactionService->transactionUuid(), $transactionUuid)
            ->where($this->paymentTransactionService->orderId(), $orderId)
            ->where($this->paymentTransactionService->clientId(), $clientId)
            ->first();

        if (!$transaction && $gatewayOrderId !== '') {
            $transaction = $this->paymentTransactionService->query()
                ->where($this->paymentTransactionService->gatewayOrderId(), $gatewayOrderId)
                ->where($this->paymentTransactionService->orderId(), $orderId)
                ->where($this->paymentTransactionService->clientId(), $clientId)
                ->first();
        }

        if (!$transaction) {
            throw new \Exception('Payment transaction not found.', 404);
        }

        $currentStatus = strtolower(trim((string) ($transaction->{$this->paymentTransactionService->paymentStatus()} ?? '')));
        if (in_array($currentStatus, ['paid', 'success', 'completed'], true)) {
            return [
                'order_id' => $orderId,
                'transaction_uuid' => $transaction->{$this->paymentTransactionService->transactionUuid()},
            ];
        }

        $gatewayPayload = [
            'provider' => $provider,
            'payment_id' => $gatewayPaymentId,
            'order_id' => $gatewayOrderId,
            'signature' => $signature,
            'gateway_data' => $gatewayData,
        ];

        $localInvoice = $this->invoiceService->query()
            ->where($this->invoiceService->orderId(), $orderId)
            ->first();

        $transaction->update([
            $this->paymentTransactionService->gatewayOrderId() => $gatewayOrderId ?: $transaction->{$this->paymentTransactionService->gatewayOrderId()},
            $this->paymentTransactionService->gatewayPaymentId() => $gatewayPaymentId,
            $this->paymentTransactionService->paymentStatus() => 'success',
            $this->paymentTransactionService->status() => 'completed',
            $this->paymentTransactionService->successAt() => now(),
            $this->paymentTransactionService->gatewayResponse() => json_encode($gatewayPayload),
            $this->paymentTransactionService->paymentDetails() => json_encode($gatewayData),
            $this->paymentTransactionService->updatedBy() => $user?->id,
            $this->paymentTransactionService->invoiceId() => $localInvoice ? $localInvoice->id : 0,
        ]);

        $orderRow->update([
            $this->candidateOrderService->paymentStatus() => 'paid',
            $this->candidateOrderService->paymentReference() => $gatewayPaymentId,
            $this->candidateOrderService->status() => OrderStatus::PROCESSING->value,
            $this->candidateOrderService->processedAt() => now(),
            $this->candidateOrderService->updatedBy() => $user?->id,
        ]);

        try {
            if ($localInvoice) {
                $localInvoice->update([
                    $this->invoiceService->paymentStatus() => 'paid',
                    $this->invoiceService->status() => 'paid',
                ]);
            }
        } catch (\Throwable $e) {
            Log::error("Failed to process invoice payment for order {$orderId}: " . $e->getMessage());
        }

        return [
            'order_id' => $orderId,
            'order_status' => $orderRow->{$this->candidateOrderService->status()},
            'payment_status' => $orderRow->{$this->candidateOrderService->paymentStatus()},
            'transaction_uuid' => $transaction->{$this->paymentTransactionService->transactionUuid()},
            'gateway_payment_id' => $gatewayPaymentId,
            'gateway_order_id' => $gatewayOrderId,
        ];
    }

    protected function generateOrderNumber(int $clientId): string
    {
        do {
            $code = 'ORD-' . $clientId . '-' . Str::upper(Str::random(6));
            $exists = $this->candidateOrderService->query()
                ->where($this->candidateOrderService->orderNumber(), $code)
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
