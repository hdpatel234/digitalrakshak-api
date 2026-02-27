<?php
namespace App\Http\Controllers\Api\Client\Candidate;

use App\Http\Controllers\Api\Client\BaseController;
use App\Services\CandidateService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CandidatesController extends BaseController
{
    use ApiResponse;
    protected CandidateService $candidateService;

    public function __construct(CandidateService $candidateService)
    {
        $this->candidateService = $candidateService;
    }

    public function index(Request $request)
    {
        $query = $this->candidateService->query();

        $user = $request->user('api') ?? $request->user();
        if ($user && isset($user->client_id) && $user->client_id !== null) {
            $query->where($this->candidateService->clientId(), $user->client_id);
        }

        $result = $this->candidateService->datatable(
            query: $query,
            params: $request->all(),
            config: [
                'searchable' => [
                    $this->candidateService->firstName(),
                    $this->candidateService->lastName(),
                    $this->candidateService->email(),
                    $this->candidateService->phone(),
                    $this->candidateService->alternatePhone(),
                    $this->candidateService->city(),
                    $this->candidateService->state(),
                    $this->candidateService->country(),
                    $this->candidateService->source(),
                ],
                'status_column' => $this->candidateService->status(),
                'date_column' => $this->candidateService->createdAt(),
                'allowed_filters' => [
                    'client_id' => $this->candidateService->clientId(),
                    'source' => $this->candidateService->source(),
                    'gender' => $this->candidateService->gender(),
                    'country' => $this->candidateService->country(),
                    'state' => $this->candidateService->state(),
                    'city' => $this->candidateService->city(),
                ],
                'allowed_sorts' => [
                    $this->candidateService->id(),
                    $this->candidateService->firstName(),
                    $this->candidateService->lastName(),
                    $this->candidateService->email(),
                    $this->candidateService->status(),
                    $this->candidateService->source(),
                    $this->candidateService->createdAt(),
                    $this->candidateService->updatedAt(),
                ],
                'default_sort_by' => $this->candidateService->createdAt(),
                'default_sort_direction' => 'desc',
                'default_per_page' => 10,
                'max_per_page' => 100,
            ]
        );

        return $this->success('message', $result);
    }
}
