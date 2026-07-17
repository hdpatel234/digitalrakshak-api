<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\Api\Admin\StoreSystemEmailTemplateRequest;
use App\Http\Requests\Api\Admin\UpdateSystemEmailTemplateRequest;
use App\Services\ApiService\Admin\SystemEmailService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class SystemEmailController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected SystemEmailService $systemEmailService
    ) {}

    public function overview(Request $request)
    {
        addInfoLog("Admin system email overview request");

        $data = $this->systemEmailService->getOverview();

        return $this->success('Overview data fetched successfully', $data);
    }

    public function templates(Request $request)
    {
        addInfoLog("Admin system email templates list request");

        $data = $this->systemEmailService->getTemplates($request->all());

        return $this->success('Templates fetched successfully', $data);
    }

    public function storeTemplate(StoreSystemEmailTemplateRequest $request)
    {
        addInfoLog("Admin system email template create request");

        $template = $this->systemEmailService->storeTemplate($request->validated());

        return $this->success('Email template created successfully', $template, 201);
    }

    public function updateTemplate(UpdateSystemEmailTemplateRequest $request, $id)
    {
        addInfoLog("Admin system email template update request");

        try {
            $template = $this->systemEmailService->updateTemplate((int) $id, $request->validated());
            return $this->success('Email template updated successfully', $template);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Email template not found', 404);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
