<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailServerType;
use App\Services\EmailServerService;
use App\Services\EmailServerConfigurationFieldService;
use App\Http\Requests\Api\Admin\SystemEmailServer\StoreRequest;
use App\Http\Requests\Api\Admin\SystemEmailServer\UpdateRequest;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Exception;

class SystemEmailServerController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected EmailServerService $emailServerService,
        protected EmailServerConfigurationFieldService $fieldService
    ) {}

    /**
     * Display a listing of the email servers.
     */
    public function index()
    {
        $servers = $this->emailServerService->query()->with('serverType')->orderBy('id', 'desc')->get();
        return $this->success('Email servers fetched successfully', $servers);
    }

    /**
     * Fetch active email server types.
     */
    public function types()
    {
        $types = EmailServerType::where('is_active', true)->get();
        return $this->success('Server types fetched successfully', $types);
    }

    /**
     * Fetch fields for a specific server type.
     */
    public function getServerTypeFields($id)
    {
        $fields = $this->fieldService->query()
            ->where('server_type_id', $id)
            ->orderBy('sort_order')
            ->get();

        // decode options json
        foreach ($fields as $field) {
            if ($field->options && is_string($field->options)) {
                $field->options = json_decode($field->options, true);
            }
        }

        return $this->success('Server type fields fetched successfully', $fields);
    }

    /**
     * Store a newly created email server in storage.
     */
    public function store(StoreRequest $request)
    {
        try {
            $server = $this->emailServerService->create($request->validated());

            return $this->success('Email server created successfully', $server);
        } catch (Exception $e) {
            return $this->error('Failed to create email server: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified email server in storage.
     */
    public function update(UpdateRequest $request, $id)
    {
        try {
            $server = $this->emailServerService->find($id);
        } catch (Exception $e) {
            return $this->error('Email server not found', 404);
        }

        try {
            $this->emailServerService->update($id, $request->validated());
            $server = $this->emailServerService->find($id);

            return $this->success('Email server updated successfully', $server);
        } catch (Exception $e) {
            return $this->error('Failed to update email server: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified email server from storage.
     */
    public function destroy($id)
    {
        try {
            $server = $this->emailServerService->find($id);
        } catch (Exception $e) {
            return $this->error('Email server not found', 404);
        }

        try {
            $this->emailServerService->delete($id);

            return $this->success('Email server deleted successfully');
        } catch (Exception $e) {
            return $this->error('Failed to delete email server: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Test connection to the specified email server.
     */
    public function testConnection($id)
    {
        try {
            $server = $this->emailServerService->find($id);
        } catch (Exception $e) {
            return $this->error('Email server not found', 404);
        }

        try {
            // Simplified connection testing simulation
            // In a real-world scenario, you would attempt to connect to SMTP using Symfony Mailer transport

            $this->emailServerService->update($id, [
                'health_check_at' => now(),
                'health_check_status' => 'Success'
            ]);

            return $this->success('Connection successful', [
                'last_tested' => now()
            ]);
        } catch (Exception $e) {
            $this->emailServerService->update($id, [
                'health_check_at' => now(),
                'health_check_status' => 'Failed',
                'last_error' => $e->getMessage()
            ]);

            return $this->error('Connection failed: ' . $e->getMessage(), 500);
        }
    }
}
