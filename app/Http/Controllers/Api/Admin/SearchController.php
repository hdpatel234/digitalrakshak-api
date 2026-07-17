<?php

namespace App\Http\Controllers\Api\Admin;

use App\Services\ApiService\Admin\SearchService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SearchController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected SearchService $searchService
    ) {}

    public function search(Request $request): JsonResponse
    {
        addInfoLog("Admin search request");

        $query = $request->input('query');
        
        $results = $this->searchService->search($query ?? '');

        return $this->success('Search results fetched successfully.', $results);
    }
}
