<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\Api\Admin\RevenueReportRequest;
use App\Services\ApiService\Admin\ReportService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ReportController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected ReportService $reportService
    ) {}

    public function revenue(RevenueReportRequest $request)
    {
        addInfoLog("Admin report revenue request");

        $data = $this->reportService->getRevenueReport($request->validated());

        return $this->success('Revenue report fetched successfully.', $data);
    }

    public function orders(Request $request)
    {
        addInfoLog("Admin report orders request");

        $data = $this->reportService->getOrdersReport($request->all());

        return $this->success('Orders report fetched successfully.', $data);
    }

    public function serviceFilters()
    {
        addInfoLog("Admin report service filters request");

        $data = $this->reportService->getServiceFilters();

        return $this->success('Filters fetched successfully.', $data);
    }

    public function services(Request $request)
    {
        addInfoLog("Admin report services request");

        $data = $this->reportService->getServicesReport($request->all());

        return $this->success('Services report fetched successfully.', $data);
    }

    public function clients(Request $request)
    {
        addInfoLog("Admin report clients request");

        $data = $this->reportService->getClientsReport($request->all());

        return $this->success('Clients report fetched successfully.', $data);
    }

    public function candidates(Request $request)
    {
        addInfoLog("Admin report candidates request");

        $data = $this->reportService->getCandidatesReport($request->all());

        return $this->success('Candidates report fetched successfully.', $data);
    }
}
