<?php

namespace App\Services\ApiService\Client;

use App\Services\BaseService;
use App\Repositories\PaymentGatewayConfigRepository;
use App\Repositories\PaymentGatewayRepository;
use App\Repositories\PaymentMethodTypeRepository;
use App\Repositories\PaymentTransactionRepository;
use Illuminate\Support\Collection;

class BillingService extends BaseService
{
    public function __construct(
        protected PaymentGatewayConfigRepository $paymentGatewayConfigRepo,
        protected PaymentGatewayRepository $paymentGatewayRepo,
        protected PaymentMethodTypeRepository $paymentMethodTypeRepo,
        protected PaymentTransactionRepository $paymentTransactionRepo
    ) {}

    public function getPaymentGateways(): array
    {
        $gatewayRows = $this->paymentGatewayRepo->query()
            ->select([
                $this->paymentGatewayRepo->id(),
                $this->paymentGatewayRepo->gatewayName(),
                $this->paymentGatewayRepo->gatewayCode(),
                $this->paymentGatewayRepo->providerCompany(),
                $this->paymentGatewayRepo->logo(),
                $this->paymentGatewayRepo->website(),
                $this->paymentGatewayRepo->supportedMethods(),
                $this->paymentGatewayRepo->isActive(),
                $this->paymentGatewayRepo->isDefault(),
                $this->paymentGatewayRepo->displayOrder(),
            ])
            ->with([
                'gatewayConfigs' => function ($query) {
                    $query->select([
                        $this->paymentGatewayConfigRepo->id(),
                        $this->paymentGatewayConfigRepo->gatewayId(),
                        $this->paymentGatewayConfigRepo->configName(),
                        $this->paymentGatewayConfigRepo->environment(),
                        $this->paymentGatewayConfigRepo->baseUrl(),
                        $this->paymentGatewayConfigRepo->isActive(),

                    ])->where($this->paymentGatewayConfigRepo->isActive(), 'active');
                },
            ])
            ->where($this->paymentGatewayRepo->isActive(), 1)
            ->whereHas('gatewayConfigs', function ($query) {
                $query->where($this->paymentGatewayConfigRepo->isActive(), 'active');
            })
            ->orderBy($this->paymentGatewayRepo->displayOrder(), 'asc')
            ->get();

        if ($gatewayRows->isEmpty()) {
            return [];
        }

        $allMethodTypeIds = collect();
        foreach ($gatewayRows as $gateway) {
            $allMethodTypeIds = $allMethodTypeIds->merge(
                $this->extractMethodTypeIds($gateway->{$this->paymentGatewayRepo->supportedMethods()} ?? null)
            );
        }

        $methodTypesById = $allMethodTypeIds
            ->map(static fn($id) => (int) $id)
            ->filter(static fn($id) => $id > 0)
            ->unique()
            ->pipe(function ($ids) {
                if ($ids->isEmpty()) {
                    return collect();
                }

                return $this->paymentMethodTypeRepo->query()
                    ->whereIn($this->paymentMethodTypeRepo->id(), $ids->values()->all())
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
                    ->get()
                    ->keyBy($this->paymentMethodTypeRepo->id());
            });

        $response = collect();
        foreach ($gatewayRows as $gateway) {
            $supportedMethodTypeIds = $this->extractMethodTypeIds(
                $gateway->{$this->paymentGatewayRepo->supportedMethods()} ?? null
            );

            $supportedMethods = collect($supportedMethodTypeIds)
                ->map(static fn($id) => (int) $id)
                ->filter(static fn($id) => $id > 0)
                ->unique()
                ->map(function ($methodTypeId) use ($methodTypesById) {
                    $methodType = $methodTypesById->get($methodTypeId);
                    if (!$methodType) {
                        return null;
                    }

                    return [
                        'id' => (int) $methodType->{$this->paymentMethodTypeRepo->id()},
                        'method_name' => $methodType->{$this->paymentMethodTypeRepo->methodName()},
                        'method_code' => $methodType->{$this->paymentMethodTypeRepo->methodCode()},
                        'category' => $methodType->{$this->paymentMethodTypeRepo->category()},
                        'icon' => $methodType->{$this->paymentMethodTypeRepo->icon()},
                        'description' => $methodType->{$this->paymentMethodTypeRepo->description()},
                        'display_order' => $methodType->{$this->paymentMethodTypeRepo->displayOrder()},
                    ];
                })
                ->filter()
                ->values()
                ->all();

            foreach ($gateway->gatewayConfigs as $gatewayConfig) {
                $response->push([
                    'client_gateway_id' => 0,
                    'gateway_config_id' => (int) $gatewayConfig->{$this->paymentGatewayConfigRepo->id()},
                    'gateway_id' => (int) $gateway->{$this->paymentGatewayRepo->id()},
                    'display_name' => $gateway->{$this->paymentGatewayRepo->gatewayName()},
                    'gateway_name' => $gateway->{$this->paymentGatewayRepo->gatewayName()},
                    'gateway_code' => $gateway->{$this->paymentGatewayRepo->gatewayCode()},
                    'provider_company' => $gateway->{$this->paymentGatewayRepo->providerCompany()},
                    'logo' => $gateway->{$this->paymentGatewayRepo->logo()},
                    'website' => $gateway->{$this->paymentGatewayRepo->website()},

                    'display_order' => (int) ($gateway->{$this->paymentGatewayRepo->displayOrder()} ?? 0),
                    'supported_methods' => $supportedMethods,
                ]);
            }
        }

        return $response->values()->all();
    }

    public function getPaymentMethods(): array
    {
        $gatewayRows = $this->paymentGatewayRepo->query()
            ->select([
                $this->paymentGatewayRepo->id(),
                $this->paymentGatewayRepo->supportedMethods(),
                $this->paymentGatewayRepo->isActive(),
            ])
            ->with([
                'gatewayConfigs' => function ($query) {
                    $query->select([
                        $this->paymentGatewayConfigRepo->id(),
                        $this->paymentGatewayConfigRepo->gatewayId(),
                        $this->paymentGatewayConfigRepo->minAmount(),
                        $this->paymentGatewayConfigRepo->maxAmount(),
                        $this->paymentGatewayConfigRepo->isActive(),
                    ])->where($this->paymentGatewayConfigRepo->isActive(), 'active');
                },
            ])
            ->where($this->paymentGatewayRepo->isActive(), 1)
            ->whereHas('gatewayConfigs', function ($query) {
                $query->where($this->paymentGatewayConfigRepo->isActive(), 'active');
            })
            ->get();

        if ($gatewayRows->isEmpty()) {
            return [];
        }

        $methodTypeIds = collect();
        foreach ($gatewayRows as $gateway) {
            $methodTypeIds = $methodTypeIds->merge(
                $this->extractMethodTypeIds($gateway->{$this->paymentGatewayRepo->supportedMethods()} ?? null)
            );
        }

        $methodTypeIds = $methodTypeIds
            ->map(static fn($id) => (int) $id)
            ->filter(static fn($id) => $id > 0)
            ->unique()
            ->values();

        if ($methodTypeIds->isEmpty()) {
            return [];
        }

        $methodTypesById = $this->paymentMethodTypeRepo->query()
            ->whereIn($this->paymentMethodTypeRepo->id(), $methodTypeIds->all())
            ->where($this->paymentMethodTypeRepo->isActive(), 1)
            ->select([
                $this->paymentMethodTypeRepo->id(),
                $this->paymentMethodTypeRepo->methodName(),
                $this->paymentMethodTypeRepo->methodCode(),
                $this->paymentMethodTypeRepo->category(),
                $this->paymentMethodTypeRepo->icon(),
                $this->paymentMethodTypeRepo->description(),
                $this->paymentMethodTypeRepo->configurationSchema(),
                $this->paymentMethodTypeRepo->displayOrder(),
            ])
            ->orderBy($this->paymentMethodTypeRepo->displayOrder(), 'asc')
            ->get()
            ->keyBy($this->paymentMethodTypeRepo->id());

        $response = collect();
        foreach ($gatewayRows as $gateway) {
            $supportedMethodTypeIds = collect(
                $this->extractMethodTypeIds($gateway->{$this->paymentGatewayRepo->supportedMethods()} ?? null)
            )
                ->map(static fn($id) => (int) $id)
                ->filter(static fn($id) => $id > 0)
                ->unique()
                ->values();

            foreach ($gateway->gatewayConfigs as $gatewayConfig) {
                foreach ($supportedMethodTypeIds as $methodTypeId) {
                    $methodType = $methodTypesById->get($methodTypeId);
                    if (!$methodType) {
                        continue;
                    }

                    $response->push([
                        'client_payment_method_id' => 0,
                        'method_type_id' => (int) $methodType->{$this->paymentMethodTypeRepo->id()},
                        'gateway_config_id' => (int) $gatewayConfig->{$this->paymentGatewayConfigRepo->id()},
                        'gateway_id' => (int) $gateway->{$this->paymentGatewayRepo->id()},
                        'display_name' => $methodType->{$this->paymentMethodTypeRepo->methodName()},
                        'description' => $methodType->{$this->paymentMethodTypeRepo->description()},
                        'icon' => $methodType->{$this->paymentMethodTypeRepo->icon()},
                        'display_order' => (int) ($methodType->{$this->paymentMethodTypeRepo->displayOrder()} ?? 0),
                        'is_default' => 0,
                        'min_amount' => $gatewayConfig->{$this->paymentGatewayConfigRepo->minAmount()},
                        'max_amount' => $gatewayConfig->{$this->paymentGatewayConfigRepo->maxAmount()},
                        'instructions' => null,
                        'method_type' => [
                            'id' => (int) $methodType->{$this->paymentMethodTypeRepo->id()},
                            'method_name' => $methodType->{$this->paymentMethodTypeRepo->methodName()},
                            'method_code' => $methodType->{$this->paymentMethodTypeRepo->methodCode()},
                            'category' => $methodType->{$this->paymentMethodTypeRepo->category()},
                            'icon' => $methodType->{$this->paymentMethodTypeRepo->icon()},
                            'description' => $methodType->{$this->paymentMethodTypeRepo->description()},
                            'configuration_schema' => $methodType->{$this->paymentMethodTypeRepo->configurationSchema()},
                            'display_order' => (int) ($methodType->{$this->paymentMethodTypeRepo->displayOrder()} ?? 0),
                        ],
                    ]);
                }
            }
        }

        return $response->values()->all();
    }

    public function getPaymentGatewaysByMethod(int $methodTypeId): array
    {
        $gatewayRows = $this->paymentGatewayRepo->query()
            ->select([
                $this->paymentGatewayRepo->id(),
                $this->paymentGatewayRepo->gatewayName(),
                $this->paymentGatewayRepo->gatewayCode(),
                $this->paymentGatewayRepo->providerCompany(),
                $this->paymentGatewayRepo->logo(),
                $this->paymentGatewayRepo->website(),
                $this->paymentGatewayRepo->supportedMethods(),
                $this->paymentGatewayRepo->isActive(),
                $this->paymentGatewayRepo->displayOrder(),
            ])
            ->with([
                'gatewayConfigs' => function ($query) {
                    $query->select([
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
                        $this->paymentGatewayConfigRepo->isActive(),

                    ])->where($this->paymentGatewayConfigRepo->isActive(), 'active');
                },
            ])
            ->where($this->paymentGatewayRepo->isActive(), 1)
            ->whereHas('gatewayConfigs', function ($query) {
                $query->where($this->paymentGatewayConfigRepo->isActive(), 'active');
            })
            ->orderBy($this->paymentGatewayRepo->displayOrder(), 'asc')
            ->get();

        if ($gatewayRows->isEmpty()) {
            return [];
        }

        $response = collect();
        foreach ($gatewayRows as $gateway) {
            $supportedMethodTypeIds = collect(
                $this->extractMethodTypeIds($gateway->{$this->paymentGatewayRepo->supportedMethods()} ?? null)
            )
                ->map(static fn($id) => (int) $id)
                ->filter(static fn($id) => $id > 0)
                ->unique()
                ->values();

            if (!$supportedMethodTypeIds->contains($methodTypeId)) {
                continue;
            }

            foreach ($gateway->gatewayConfigs as $gatewayConfig) {
                $response->push([
                    'client_payment_method_id' => 0,
                    'gateway_config_id' => (int) $gatewayConfig->{$this->paymentGatewayConfigRepo->id()},
                    'gateway_id' => (int) $gateway->{$this->paymentGatewayRepo->id()},
                    'display_name' => $gateway->{$this->paymentGatewayRepo->gatewayName()},
                    'is_default' => (int) ($gateway->{$this->paymentGatewayRepo->isDefault()} ?? 0),
                    'display_order' => (int) ($gateway->{$this->paymentGatewayRepo->displayOrder()} ?? 0),
                    'gateway_config' => [
                        'id' => (int) $gatewayConfig->{$this->paymentGatewayConfigRepo->id()},
                        'gateway_id' => (int) $gatewayConfig->{$this->paymentGatewayConfigRepo->gatewayId()},
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
                        'is_active' => $gatewayConfig->{$this->paymentGatewayConfigRepo->isActive()} === 'active' ? 1 : 0,

                    ],
                    'gateway' => [
                        'id' => (int) $gateway->{$this->paymentGatewayRepo->id()},
                        'gateway_name' => $gateway->{$this->paymentGatewayRepo->gatewayName()},
                        'gateway_code' => $gateway->{$this->paymentGatewayRepo->gatewayCode()},
                        'provider_company' => $gateway->{$this->paymentGatewayRepo->providerCompany()},
                        'logo' => $gateway->{$this->paymentGatewayRepo->logo()},
                        'website' => $gateway->{$this->paymentGatewayRepo->website()},
                        'supported_methods' => $gateway->{$this->paymentGatewayRepo->supportedMethods()},
                    ],
                ]);
            }
        }

        return $response->values()->all();
    }

    public function getTransactions(array $params)
    {
        $query = $this->paymentTransactionRepo->query()
            ->where($this->paymentTransactionRepo->paymentStatus(), 'success')
            ->with(['client', 'order', 'invoice', 'gatewayConfig', 'methodType']);

        return $this->paymentTransactionRepo->datatable($query, $params);
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
