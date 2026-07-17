<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\Client\BaseController;
use App\Services\ApiService\Client\SearchService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends BaseController
{
    use ApiResponse;

    public function __construct(protected SearchService $searchService) {}

    public function index(Request $request): JsonResponse
    {
        addInfoLog("Client search list request");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $queryStr = $request->input('query', '');
        
        $results = $this->searchService->performSearch($queryStr, $clientId);

        return $this->success('Search results fetched successfully.', $results);
    }
}
