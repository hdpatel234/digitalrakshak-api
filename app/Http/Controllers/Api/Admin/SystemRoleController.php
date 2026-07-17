<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Requests\Api\Admin\StoreSystemRoleRequest;
use App\Http\Requests\Api\Admin\UpdateSystemRoleRequest;
use App\Services\ApiService\Admin\SystemRoleService;
use App\Traits\ApiResponse;

class SystemRoleController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected SystemRoleService $systemRoleService
    ) {}

    public function index(Request $request)
    {
        addInfoLog("Admin system role list request");

        $data = $this->systemRoleService->getRoles($request->all());

        return $this->success('Roles fetched successfully', $data);
    }

    public function show($id)
    {
        addInfoLog("Admin system role show request, ID: {$id}");

        try {
            $data = $this->systemRoleService->showRole($id);
            return $this->success('Role fetched successfully', $data);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Role not found', 404);
        }
    }

    public function stats()
    {
        addInfoLog("Admin system role stats request");

        $data = $this->systemRoleService->getStats();

        return $this->success('Role stats fetched successfully', $data);
    }
    
    public function permissions()
    {
        addInfoLog("Admin system role permissions request");

        $data = $this->systemRoleService->getPermissions();

        return $this->success('Permissions fetched successfully', $data);
    }

    public function store(StoreSystemRoleRequest $request)
    {
        addInfoLog("Admin system role create request");

        try {
            $role = $this->systemRoleService->storeRole($request->validated());
            return $this->success('Role created successfully', $role, 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function update(UpdateSystemRoleRequest $request, $id)
    {
        addInfoLog("Admin system role update request");

        try {
            $role = $this->systemRoleService->updateRole($id, $request->validated());
            return $this->success('Role updated successfully', $role);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Role not found', 404);
        } catch (\Exception $e) {
            $code = $e->getCode() === 403 ? 403 : 500;
            return $this->error($e->getMessage(), $code);
        }
    }

    public function destroy($id)
    {
        addInfoLog("Admin system role delete request");

        try {
            $this->systemRoleService->deleteRole($id);
            return $this->success('Role deleted successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Role not found', 404);
        } catch (\Exception $e) {
            $code = in_array($e->getCode(), [400, 403]) ? $e->getCode() : 500;
            return $this->error($e->getMessage(), $code);
        }
    }
}
