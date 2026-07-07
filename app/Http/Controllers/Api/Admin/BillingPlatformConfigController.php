<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\PaymentGateway;
use App\Models\PaymentGatewayConfig;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BillingPlatformConfigController extends BaseController
{
    /**
     * Display a listing of configs for a specific platform.
     */
    public function index($platform): JsonResponse
    {
        $gateway = PaymentGateway::find($platform);

        if (!$gateway) {
            return $this->error('Billing platform not found.', 404);
        }

        $configs = PaymentGatewayConfig::where(PaymentGatewayConfig::GATEWAY_ID, $gateway->id)->get();

        return $this->success(
            'Billing platform configurations retrieved successfully.',
            $configs
        );
    }

    /**
     * Store a newly created config.
     */
    public function store(Request $request, $platform): JsonResponse
    {
        $gateway = PaymentGateway::find($platform);

        if (!$gateway) {
            return $this->error('Billing platform not found.', 404);
        }

        $request->validate([
            PaymentGatewayConfig::CONFIG_NAME => 'required|string|max:255',
            PaymentGatewayConfig::ENVIRONMENT => 'required|in:sandbox,production',
        ]);

        $data = $request->all();
        $data[PaymentGatewayConfig::GATEWAY_ID] = $gateway->id;
        
        // If this is set as default or active, we might want to unset others.
        // For simplicity, we just save what is passed.
        
        $config = PaymentGatewayConfig::create($data);

        return $this->success(
            'Configuration created successfully.',
            $config
        );
    }

    /**
     * Update the specified config.
     */
    public function update(Request $request, $platform, $configId): JsonResponse
    {
        $gateway = PaymentGateway::find($platform);

        if (!$gateway) {
            return $this->error('Billing platform not found.', 404);
        }

        $config = PaymentGatewayConfig::where(PaymentGatewayConfig::GATEWAY_ID, $gateway->id)
                                      ->where('id', $configId)
                                      ->first();

        if (!$config) {
            return $this->error('Configuration not found.', 404);
        }

        $request->validate([
            PaymentGatewayConfig::CONFIG_NAME => 'sometimes|required|string|max:255',
            PaymentGatewayConfig::ENVIRONMENT => 'sometimes|required|in:sandbox,production',
        ]);

        $config->update($request->all());

        return $this->success('Configuration updated successfully.', $config);
    }

    /**
     * Remove the specified config.
     */
    public function destroy($platform, $configId): JsonResponse
    {
        $config = PaymentGatewayConfig::where(PaymentGatewayConfig::GATEWAY_ID, $platform)
                                      ->where('id', $configId)
                                      ->first();

        if (!$config) {
            return $this->error('Configuration not found.', 404);
        }

        $config->delete();

        return $this->success('Configuration deleted successfully.');
    }
}
