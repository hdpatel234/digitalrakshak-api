<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BillingPlatformController extends BaseController
{
    /**
     * Display a listing of the billing platforms.
     */
    public function index(Request $request): JsonResponse
    {
        $query = PaymentGateway::query();

        // Optional search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where(PaymentGateway::GATEWAY_NAME, 'like', "%{$search}%")
                  ->orWhere(PaymentGateway::PROVIDER_COMPANY, 'like', "%{$search}%");
            });
        }

        // Optional active filter
        if ($request->has('is_active')) {
            $query->where(PaymentGateway::IS_ACTIVE, $request->input('is_active'));
        }

        $perPage = $request->input('per_page', 15);
        
        $platforms = $query->orderBy(PaymentGateway::DISPLAY_ORDER, 'asc')
                           ->orderBy(PaymentGateway::GATEWAY_NAME, 'asc')
                           ->paginate($perPage);

        return $this->success(
            'Billing platforms retrieved successfully.',
            $platforms
        );
    }

    /**
     * Display the specified billing platform.
     */
    public function show($platform): JsonResponse
    {
        $gateway = PaymentGateway::find($platform);

        if (!$gateway) {
            return $this->error('Billing platform not found.', 404);
        }

        return $this->success(
            'Billing platform retrieved successfully.',
            $gateway
        );
    }

    /**
     * Store a newly created billing platform.
     */
    public function store(Request $request): JsonResponse
    {
        return $this->error('Not implemented yet.', 501);
    }

    /**
     * Update the specified billing platform.
     */
    public function update(Request $request, $platform): JsonResponse
    {
        $gateway = PaymentGateway::find($platform);

        if (!$gateway) {
            return $this->error('Billing platform not found.', 404);
        }

        if ($request->has('configuration_schema')) {
            $gateway->configuration_schema = $request->input('configuration_schema');
            $gateway->save();
        }

        return $this->success('Billing platform updated successfully.', $gateway);
    }

    /**
     * Toggle the active status of the billing platform.
     */
    public function toggleStatus($platform): JsonResponse
    {
        $gateway = PaymentGateway::find($platform);

        if (!$gateway) {
            return $this->error('Billing platform not found.', 404);
        }

        $gateway->is_active = $gateway->is_active == 1 ? 0 : 1;
        
        // If marked inactive, remove default status
        if ($gateway->is_active == 0) {
            $gateway->is_default = 0;
        }

        $gateway->save();

        return $this->success(
            'Billing platform status updated successfully.',
            $gateway
        );
    }

    /**
     * Toggle the default status of the billing platform.
     */
    public function toggleDefault($platform): JsonResponse
    {
        $gateway = PaymentGateway::find($platform);

        if (!$gateway) {
            return $this->error('Billing platform not found.', 404);
        }

        if ($gateway->is_active == 0) {
            return $this->error('Cannot set an inactive gateway as default.', 400);
        }

        // Set all other platforms to not default
        PaymentGateway::where('id', '!=', $gateway->id)->update(['is_default' => 0]);

        // Set this platform as default
        $gateway->is_default = 1;
        $gateway->save();

        return $this->success(
            'Billing platform set as default successfully.',
            $gateway
        );
    }

    /**
     * Remove the specified billing platform.
     */
    public function destroy($platform): JsonResponse
    {
        return $this->error('Not implemented yet.', 501);
    }
}
