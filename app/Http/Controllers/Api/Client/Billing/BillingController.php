<?php

namespace App\Http\Controllers\Api\Client\Billing;

use App\Http\Controllers\Api\Client\BaseController;
use App\Services\ClientPaymentGatewayService;
use App\Services\ClientPaymentMethodService;
use App\Services\PaymentGatewayConfigService;
use App\Services\PaymentGatewayService;
use App\Services\PaymentMethodTypeService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class BillingController extends BaseController
{
    use ApiResponse;

    protected ClientPaymentMethodService $service;
    protected ClientPaymentGatewayService $clientPaymentGatewayService;
    protected PaymentGatewayConfigService $paymentGatewayConfigService;
    protected PaymentGatewayService $paymentGatewayService;
    protected PaymentMethodTypeService $paymentMethodTypeService;

    public function __construct(
        ClientPaymentMethodService $service,
        ClientPaymentGatewayService $clientPaymentGatewayService,
        PaymentGatewayConfigService $paymentGatewayConfigService,
        PaymentGatewayService $paymentGatewayService,
        PaymentMethodTypeService $paymentMethodTypeService
    )
    {
        $this->service = $service;
        $this->clientPaymentGatewayService = $clientPaymentGatewayService;
        $this->paymentGatewayConfigService = $paymentGatewayConfigService;
        $this->paymentGatewayService = $paymentGatewayService;
        $this->paymentMethodTypeService = $paymentMethodTypeService;
    }

    public function paymentGateways(Request $request)
    {
        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $clientGatewayRows = $this->clientPaymentGatewayService->query()
            ->select([
                $this->clientPaymentGatewayService->id(),
                $this->clientPaymentGatewayService->gatewayConfigId(),
                $this->clientPaymentGatewayService->displayName(),
                $this->clientPaymentGatewayService->displayOrder(),
                $this->clientPaymentGatewayService->isDefault(),
                $this->clientPaymentGatewayService->isEnabled(),
            ])
            ->with([
                'gatewayConfig' => function ($query) {
                    $query->select([
                        $this->paymentGatewayConfigService->id(),
                        $this->paymentGatewayConfigService->gatewayId(),
                        $this->paymentGatewayConfigService->isActive(),
                    ])->where($this->paymentGatewayConfigService->isActive(), 1);
                },
                'gatewayConfig.gateway' => function ($query) {
                    $query->select([
                        $this->paymentGatewayService->id(),
                        $this->paymentGatewayService->gatewayName(),
                        $this->paymentGatewayService->gatewayCode(),
                        $this->paymentGatewayService->providerCompany(),
                        $this->paymentGatewayService->logo(),
                        $this->paymentGatewayService->website(),
                        $this->paymentGatewayService->supportedMethods(),
                        $this->paymentGatewayService->isActive(),
                    ])->where($this->paymentGatewayService->isActive(), 1);
                },
            ])
            ->where($this->clientPaymentGatewayService->clientId(), $clientId)
            ->where($this->clientPaymentGatewayService->isEnabled(), 1)
            ->whereHas('gatewayConfig', function ($query) {
                $query->where($this->paymentGatewayConfigService->isActive(), 1);
            })
            ->whereHas('gatewayConfig.gateway', function ($query) {
                $query->where($this->paymentGatewayService->isActive(), 1);
            })
            ->orderBy($this->clientPaymentGatewayService->displayOrder(), 'asc')
            ->get();

        if ($clientGatewayRows->isEmpty()) {
            return $this->success('Client payment gateways fetched successfully.', []);
        }

        $allMethodTypeIds = collect();
        foreach ($clientGatewayRows as $clientGateway) {
            $gateway = $clientGateway->gatewayConfig?->gateway;
            if (!$gateway) {
                continue;
            }

            $allMethodTypeIds = $allMethodTypeIds->merge(
                $this->extractMethodTypeIds($gateway->{$this->paymentGatewayService->supportedMethods()} ?? null)
            );
        }

        $methodTypesById = $allMethodTypeIds
            ->map(static fn ($id) => (int) $id)
            ->filter(static fn ($id) => $id > 0)
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

        $response = $clientGatewayRows
            ->map(function ($clientGateway) use ($methodTypesById) {
                $gatewayConfig = $clientGateway->gatewayConfig;
                if (!$gatewayConfig) {
                    return null;
                }

                $configId = (int) ($gatewayConfig->{$this->paymentGatewayConfigService->id()} ?? 0);
                $gatewayId = (int) ($gatewayConfig->{$this->paymentGatewayConfigService->gatewayId()} ?? 0);
                $gateway = $gatewayConfig->gateway;
                if (!$gateway) {
                    return null;
                }

                $supportedMethodTypeIds = $this->extractMethodTypeIds(
                    $gateway->{$this->paymentGatewayService->supportedMethods()} ?? null
                );

                $supportedMethods = collect($supportedMethodTypeIds)
                    ->map(static fn ($id) => (int) $id)
                    ->filter(static fn ($id) => $id > 0)
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

                return [
                    'client_gateway_id' => (int) $clientGateway->{$this->clientPaymentGatewayService->id()},
                    'gateway_config_id' => $configId,
                    'gateway_id' => $gatewayId,
                    'display_name' => $clientGateway->{$this->clientPaymentGatewayService->displayName()}
                        ?: $gateway->{$this->paymentGatewayService->gatewayName()},
                    'gateway_name' => $gateway->{$this->paymentGatewayService->gatewayName()},
                    'gateway_code' => $gateway->{$this->paymentGatewayService->gatewayCode()},
                    'provider_company' => $gateway->{$this->paymentGatewayService->providerCompany()},
                    'logo' => $gateway->{$this->paymentGatewayService->logo()},
                    'website' => $gateway->{$this->paymentGatewayService->website()},
                    'is_default' => (int) ($clientGateway->{$this->clientPaymentGatewayService->isDefault()} ?? 0),
                    'display_order' => (int) ($clientGateway->{$this->clientPaymentGatewayService->displayOrder()} ?? 0),
                    'supported_methods' => $supportedMethods,
                ];
            })
            ->filter()
            ->values()
            ->all();

        return $this->success('Client payment gateways fetched successfully.', $response);
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
