<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Requests\Api\Admin\SystemEmailServer\StoreRequest;
use App\Http\Requests\Api\Admin\SystemEmailServer\UpdateRequest;
use App\Http\Requests\Api\Admin\SendTestSystemEmailRequest;
use App\Services\ApiService\Admin\SystemEmailServerService;
use App\Traits\ApiResponse;

class SystemEmailServerController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected SystemEmailServerService $systemEmailServerService
    ) {}

    /**
     * Display a listing of the email servers.
     */
    public function index(Request $request)
    {
        addInfoLog("Admin system email server list request");

        $servers = $this->systemEmailServerService->getServers($request->all());

        return $this->success('Email servers fetched successfully', $servers);
    }

    /**
     * Fetch active email server types.
     */
    public function types()
    {
        addInfoLog("Admin system email server types request");

        $types = $this->systemEmailServerService->getServerTypes();
        
        return $this->success('Server types fetched successfully', $types);
    }

    /**
     * Fetch available email server statuses.
     */
    public function statuses()
    {
        addInfoLog("Admin system email server statuses request");

        $statuses = $this->systemEmailServerService->getStatuses();
        
        return $this->success('Server statuses fetched successfully', $statuses);
    }

    /**
     * Fetch fields for a specific server type.
     */
    public function getServerTypeFields($id)
    {
        addInfoLog("Admin system email server type fields request, ID: {$id}");

        $fields = $this->systemEmailServerService->getServerTypeFields($id);

        return $this->success('Server type fields fetched successfully', $fields);
    }

    /**
     * Display the specified email server.
     */
    public function show($id)
    {
        addInfoLog("Admin system email server show request, ID: {$id}");

        try {
            $serverArray = $this->systemEmailServerService->showServer($id);
            return $this->success('Email server fetched successfully', $serverArray);
        } catch (\Exception $e) {
            return $this->error('Email server not found', 404);
        }
    }

    /**
     * Store a newly created email server in storage.
     */
    public function store(StoreRequest $request)
    {
        addInfoLog("Admin system email server create request");

        try {
            $server = $this->systemEmailServerService->storeServer($request->validated() + ['dynamic_values' => $request->dynamic_values]);
            return $this->success('Email server created successfully', $server);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Update the specified email server in storage.
     */
    public function update(UpdateRequest $request, $id)
    {
        addInfoLog("Admin system email server update request");

        try {
            $server = $this->systemEmailServerService->updateServer($id, $request->validated() + ['dynamic_values' => $request->dynamic_values]);
            return $this->success('Email server updated successfully', $server);
        } catch (\Exception $e) {
            $code = $e->getCode() === 404 ? 404 : 500;
            return $this->error($e->getMessage(), $code);
        }
    }

    public function destroy($id)
    {
        addInfoLog("Admin system email server delete request");

        try {
            $this->systemEmailServerService->deleteServer($id);
            return $this->success('Email server deleted successfully');
        } catch (\Exception $e) {
            $code = $e->getCode() === 404 ? 404 : 500;
            return $this->error($e->getMessage(), $code);
        }
    }

    /**
     * Test connection to the specified email server.
     */
    public function testConnection($id)
    {
        addInfoLog("Admin system email server test connection request");

        try {
            $result = $this->systemEmailServerService->testConnection($id);

            if ($result['status'] === 'success') {
                return $this->success('Connection successful', [
                    'last_tested' => now(),
                    'logs' => $result['logs'],
                    'status' => 'success'
                ]);
            } else {
                return $this->error('Connection failed', 500, [
                    'logs' => $result['logs'],
                    'status' => 'error'
                ]);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Email server not found', 404);
        } catch (\Exception $e) {
            return $this->error('Connection failed: ' . $e->getMessage(), 500, [
                'logs' => ["[ERROR] An unexpected error occurred: " . $e->getMessage()],
                'status' => 'error'
            ]);
        }
    }

    /**
     * Send a test email using the specified email server.
     */
    public function sendTestEmail(SendTestSystemEmailRequest $request, $id)
    {
        addInfoLog("Admin system email server send test email request");

        try {
            $this->systemEmailServerService->sendTestEmail($id, $request->validated()['email']);
            return $this->success('Test email sent successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Email server not found', 404);
        } catch (\Exception $e) {
            return $this->error('Failed to send test email: ' . $e->getMessage(), 500);
        }
    }
}
