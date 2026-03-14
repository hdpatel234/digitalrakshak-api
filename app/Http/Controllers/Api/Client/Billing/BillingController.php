<?php

namespace App\Http\Controllers\Api\Client\Billing;

use App\Http\Controllers\Api\Client\BaseController;
use App\Services\PaymentGatewayConfigService;
use App\Services\PaymentGatewayService;
use App\Services\PaymentMethodTypeService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class BillingController extends BaseController
{
    use ApiResponse;
    protected PaymentGatewayConfigService $paymentGatewayConfigService;
    protected PaymentGatewayService $paymentGatewayService;
    protected PaymentMethodTypeService $paymentMethodTypeService;

    public function __construct(
        PaymentGatewayConfigService $paymentGatewayConfigService,
        PaymentGatewayService $paymentGatewayService,
        PaymentMethodTypeService $paymentMethodTypeService
    ) {
        $this->paymentGatewayConfigService = $paymentGatewayConfigService;
        $this->paymentGatewayService = $paymentGatewayService;
        $this->paymentMethodTypeService = $paymentMethodTypeService;
    }

    public function paymentGateways(Request $request)
    {
        $gatewayRows = $this->paymentGatewayService->query()
            ->select([
                $this->paymentGatewayService->id(),
                $this->paymentGatewayService->gatewayName(),
                $this->paymentGatewayService->gatewayCode(),
                $this->paymentGatewayService->providerCompany(),
                $this->paymentGatewayService->logo(),
                $this->paymentGatewayService->website(),
                $this->paymentGatewayService->supportedMethods(),
                $this->paymentGatewayService->isActive(),
                $this->paymentGatewayService->isDefault(),
                $this->paymentGatewayService->displayOrder(),
            ])
            ->with([
                'gatewayConfigs' => function ($query) {
                    $query->select([
                        $this->paymentGatewayConfigService->id(),
                        $this->paymentGatewayConfigService->gatewayId(),
                        $this->paymentGatewayConfigService->configName(),
                        $this->paymentGatewayConfigService->environment(),
                        $this->paymentGatewayConfigService->baseUrl(),
                        $this->paymentGatewayConfigService->isActive(),
                        $this->paymentGatewayConfigService->isDefault(),
                        $this->paymentGatewayConfigService->isSandbox(),
                    ])->where($this->paymentGatewayConfigService->isActive(), 1);
                },
            ])
            ->where($this->paymentGatewayService->isActive(), 1)
            ->whereHas('gatewayConfigs', function ($query) {
                $query->where($this->paymentGatewayConfigService->isActive(), 1);
            })
            ->orderBy($this->paymentGatewayService->displayOrder(), 'asc')
            ->get();

        if ($gatewayRows->isEmpty()) {
            return $this->success('Client payment gateways fetched successfully.', []);
        }

        $allMethodTypeIds = collect();
        foreach ($gatewayRows as $gateway) {
            $allMethodTypeIds = $allMethodTypeIds->merge(
                $this->extractMethodTypeIds($gateway->{$this->paymentGatewayService->supportedMethods()} ?? null)
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

                return $this->paymentMethodTypeService->query()
                    ->whereIn($this->paymentMethodTypeService->id(), $ids->values()->all())
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
                    ->get()
                    ->keyBy($this->paymentMethodTypeService->id());
            });

        $response = collect();
        foreach ($gatewayRows as $gateway) {
            $supportedMethodTypeIds = $this->extractMethodTypeIds(
                $gateway->{$this->paymentGatewayService->supportedMethods()} ?? null
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
                        'id' => (int) $methodType->{$this->paymentMethodTypeService->id()},
                        'method_name' => $methodType->{$this->paymentMethodTypeService->methodName()},
                        'method_code' => $methodType->{$this->paymentMethodTypeService->methodCode()},
                        'category' => $methodType->{$this->paymentMethodTypeService->category()},
                        'icon' => $methodType->{$this->paymentMethodTypeService->icon()},
                        'description' => $methodType->{$this->paymentMethodTypeService->description()},
                        'display_order' => $methodType->{$this->paymentMethodTypeService->displayOrder()},
                    ];
                })
                ->filter()
                ->values()
                ->all();

            foreach ($gateway->gatewayConfigs as $gatewayConfig) {
                $response->push([
                    'client_gateway_id' => 0,
                    'gateway_config_id' => (int) $gatewayConfig->{$this->paymentGatewayConfigService->id()},
                    'gateway_id' => (int) $gateway->{$this->paymentGatewayService->id()},
                    'display_name' => $gateway->{$this->paymentGatewayService->gatewayName()},
                    'gateway_name' => $gateway->{$this->paymentGatewayService->gatewayName()},
                    'gateway_code' => $gateway->{$this->paymentGatewayService->gatewayCode()},
                    'provider_company' => $gateway->{$this->paymentGatewayService->providerCompany()},
                    'logo' => $gateway->{$this->paymentGatewayService->logo()},
                    'website' => $gateway->{$this->paymentGatewayService->website()},
                    'is_default' => (int) ($gatewayConfig->{$this->paymentGatewayConfigService->isDefault()} ?? 0),
                    'display_order' => (int) ($gateway->{$this->paymentGatewayService->displayOrder()} ?? 0),
                    'supported_methods' => $supportedMethods,
                ]);
            }
        }

        return $this->success('Client payment gateways fetched successfully.', $response->values()->all());
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

    public function paymentMethods(Request $request)
    {
        $gatewayRows = $this->paymentGatewayService->query()
            ->select([
                $this->paymentGatewayService->id(),
                $this->paymentGatewayService->supportedMethods(),
                $this->paymentGatewayService->isActive(),
            ])
            ->with([
                'gatewayConfigs' => function ($query) {
                    $query->select([
                        $this->paymentGatewayConfigService->id(),
                        $this->paymentGatewayConfigService->gatewayId(),
                        $this->paymentGatewayConfigService->minAmount(),
                        $this->paymentGatewayConfigService->maxAmount(),
                        $this->paymentGatewayConfigService->isActive(),
                    ])->where($this->paymentGatewayConfigService->isActive(), 1);
                },
            ])
            ->where($this->paymentGatewayService->isActive(), 1)
            ->whereHas('gatewayConfigs', function ($query) {
                $query->where($this->paymentGatewayConfigService->isActive(), 1);
            })
            ->get();

        if ($gatewayRows->isEmpty()) {
            return $this->success('Client payment methods fetched successfully.', []);
        }

        $methodTypeIds = collect();
        foreach ($gatewayRows as $gateway) {
            $methodTypeIds = $methodTypeIds->merge(
                $this->extractMethodTypeIds($gateway->{$this->paymentGatewayService->supportedMethods()} ?? null)
            );
        }

        $methodTypeIds = $methodTypeIds
            ->map(static fn($id) => (int) $id)
            ->filter(static fn($id) => $id > 0)
            ->unique()
            ->values();

        if ($methodTypeIds->isEmpty()) {
            return $this->success('Client payment methods fetched successfully.', []);
        }

        $methodTypesById = $this->paymentMethodTypeService->query()
            ->whereIn($this->paymentMethodTypeService->id(), $methodTypeIds->all())
            ->where($this->paymentMethodTypeService->isActive(), 1)
            ->select([
                $this->paymentMethodTypeService->id(),
                $this->paymentMethodTypeService->methodName(),
                $this->paymentMethodTypeService->methodCode(),
                $this->paymentMethodTypeService->category(),
                $this->paymentMethodTypeService->icon(),
                $this->paymentMethodTypeService->description(),
                $this->paymentMethodTypeService->configurationSchema(),
                $this->paymentMethodTypeService->displayOrder(),
            ])
            ->orderBy($this->paymentMethodTypeService->displayOrder(), 'asc')
            ->get()
            ->keyBy($this->paymentMethodTypeService->id());

        $response = collect();
        foreach ($gatewayRows as $gateway) {
            $supportedMethodTypeIds = collect(
                $this->extractMethodTypeIds($gateway->{$this->paymentGatewayService->supportedMethods()} ?? null)
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
                        'method_type_id' => (int) $methodType->{$this->paymentMethodTypeService->id()},
                        'gateway_config_id' => (int) $gatewayConfig->{$this->paymentGatewayConfigService->id()},
                        'gateway_id' => (int) $gateway->{$this->paymentGatewayService->id()},
                        'display_name' => $methodType->{$this->paymentMethodTypeService->methodName()},
                        'description' => $methodType->{$this->paymentMethodTypeService->description()},
                        'icon' => $methodType->{$this->paymentMethodTypeService->icon()},
                        'display_order' => (int) ($methodType->{$this->paymentMethodTypeService->displayOrder()} ?? 0),
                        'is_default' => 0,
                        'min_amount' => $gatewayConfig->{$this->paymentGatewayConfigService->minAmount()},
                        'max_amount' => $gatewayConfig->{$this->paymentGatewayConfigService->maxAmount()},
                        'instructions' => null,
                        'method_type' => [
                            'id' => (int) $methodType->{$this->paymentMethodTypeService->id()},
                            'method_name' => $methodType->{$this->paymentMethodTypeService->methodName()},
                            'method_code' => $methodType->{$this->paymentMethodTypeService->methodCode()},
                            'category' => $methodType->{$this->paymentMethodTypeService->category()},
                            'icon' => $methodType->{$this->paymentMethodTypeService->icon()},
                            'description' => $methodType->{$this->paymentMethodTypeService->description()},
                            'configuration_schema' => $methodType->{$this->paymentMethodTypeService->configurationSchema()},
                            'display_order' => (int) ($methodType->{$this->paymentMethodTypeService->displayOrder()} ?? 0),
                        ],
                    ]);
                }
            }
        }

        return $this->success('Client payment methods fetched successfully.', $response->values()->all());
    }

    public function paymentGatewaysByMethod(Request $request, $payment_method)
    {
        $methodTypeId = (int) $payment_method;

        if ($methodTypeId <= 0) {
            return $this->error('Invalid payment method.', 422);
        }

        $gatewayRows = $this->paymentGatewayService->query()
            ->select([
                $this->paymentGatewayService->id(),
                $this->paymentGatewayService->gatewayName(),
                $this->paymentGatewayService->gatewayCode(),
                $this->paymentGatewayService->providerCompany(),
                $this->paymentGatewayService->logo(),
                $this->paymentGatewayService->website(),
                $this->paymentGatewayService->supportedMethods(),
                $this->paymentGatewayService->isActive(),
                $this->paymentGatewayService->displayOrder(),
            ])
            ->with([
                'gatewayConfigs' => function ($query) {
                    $query->select([
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
                    ])->where($this->paymentGatewayConfigService->isActive(), 1);
                },
            ])
            ->where($this->paymentGatewayService->isActive(), 1)
            ->whereHas('gatewayConfigs', function ($query) {
                $query->where($this->paymentGatewayConfigService->isActive(), 1);
            })
            ->orderBy($this->paymentGatewayService->displayOrder(), 'asc')
            ->get();

        if ($gatewayRows->isEmpty()) {
            return $this->success('Client payment gateways fetched successfully.', []);
        }

        $response = collect();
        foreach ($gatewayRows as $gateway) {
            $supportedMethodTypeIds = collect(
                $this->extractMethodTypeIds($gateway->{$this->paymentGatewayService->supportedMethods()} ?? null)
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
                    'gateway_config_id' => (int) $gatewayConfig->{$this->paymentGatewayConfigService->id()},
                    'gateway_id' => (int) $gateway->{$this->paymentGatewayService->id()},
                    'display_name' => $gateway->{$this->paymentGatewayService->gatewayName()},
                    'is_default' => (int) ($gatewayConfig->{$this->paymentGatewayConfigService->isDefault()} ?? 0),
                    'display_order' => (int) ($gateway->{$this->paymentGatewayService->displayOrder()} ?? 0),
                    'gateway_config' => [
                        'id' => (int) $gatewayConfig->{$this->paymentGatewayConfigService->id()},
                        'gateway_id' => (int) $gatewayConfig->{$this->paymentGatewayConfigService->gatewayId()},
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
                    ],
                    'gateway' => [
                        'id' => (int) $gateway->{$this->paymentGatewayService->id()},
                        'gateway_name' => $gateway->{$this->paymentGatewayService->gatewayName()},
                        'gateway_code' => $gateway->{$this->paymentGatewayService->gatewayCode()},
                        'provider_company' => $gateway->{$this->paymentGatewayService->providerCompany()},
                        'logo' => $gateway->{$this->paymentGatewayService->logo()},
                        'website' => $gateway->{$this->paymentGatewayService->website()},
                        'supported_methods' => $gateway->{$this->paymentGatewayService->supportedMethods()},
                    ],
                ]);
            }
        }

        return $this->success('Client payment gateways fetched successfully.', $response->values()->all());
    }
}
