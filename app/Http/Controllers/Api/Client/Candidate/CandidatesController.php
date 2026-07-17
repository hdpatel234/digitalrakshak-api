<?php

namespace App\Http\Controllers\Api\Client\Candidate;

use App\Http\Controllers\Api\Client\BaseController;
use App\Http\Requests\Api\Client\Candidate\StoreCandidateImportRequest;
use App\Http\Requests\Api\Client\Candidate\StoreCandidateRequest;
use App\Services\ApiService\Client\CandidateService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class CandidatesController extends BaseController
{
    use ApiResponse;

    public function __construct(protected CandidateService $candidateService) {}

    public function index(Request $request)
    {
        addInfoLog("Client candidate list request");

        $result = $this->candidateService->getCandidates(
            $request->all(),
            $request->user('api') ?? $request->user()
        );

        return $this->success('message', $result);
    }

    public function store(StoreCandidateRequest $request)
    {
        addInfoLog("Client candidate create request");

        $user = Auth::user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $created = $this->candidateService->storeCandidate(
                $request->validated(),
                $clientId,
                $request->ip_address
            );

            return $this->success('Candidate added successfully.', $created, 201);
        } catch (ValidationException $e) {
            return $this->validationError($e->errors(), 'Validation failed.');
        } catch (\Throwable $e) {
            Log::error('Candidate create failed', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return $this->error('Failed to create candidate.', 500);
        }
    }

    public function show(Request $request, $id)
    {
        addInfoLog("Client candidate show request, ID: {$id}");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $candidate = \App\Models\Candidate::with(['packages', 'candidateServices.service', 'candidateInvitations'])
            ->where('id', $id)
            ->where('client_id', $clientId)
            ->first();

        if (!$candidate) {
            return $this->error('Candidate not found.', 404);
        }

        return $this->success('Candidate fetched successfully.', $candidate);
    }
    public function importSample(Request $request): Response
    {
        addInfoLog("Client candidate sheet sample request");

        $format = strtolower((string) $request->query('format', 'csv'));
        if (!in_array($format, ['csv', 'xlsx'], true)) {
            return $this->error('Invalid format. Allowed formats: csv, xlsx.', 422);
        }

        $headers = [
            'firstName',
            'lastName',
            'email',
            'dialCode',
            'phoneNumber',
            'country',
            'state',
            'city',
            'address',
            'postcode',
            'managerEmails',
        ];

        $sampleRows = [[
            'John',
            'Doe',
            'john.doe@example.com',
            '+91',
            '9876543210',
            'India',
            'Maharashtra',
            'Mumbai',
            'Sector 1',
            '110001',
            'manager1@example.com,manager2@example.com',
        ]];

        if ($format === 'csv') {
            $filename = 'candidate-import-sample.csv';
            return $this->downloadResponse(function () use ($headers, $sampleRows): void {
                $stream = fopen('php://output', 'wb');
                fputcsv($stream, $headers);
                foreach ($sampleRows as $row) {
                    fputcsv($stream, $row);
                }
                fclose($stream);
            }, $filename, [
                'Content-Type' => 'text/csv',
            ]);
        }

        $filename = 'candidate-import-sample.xlsx';
        $binary = $this->candidateService->buildSimpleXlsx($headers, $sampleRows);

        return $this->downloadResponse(
            static function () use ($binary): void {
                echo $binary;
            },
            $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    public function import(StoreCandidateImportRequest $request): JsonResponse
    {
        addInfoLog("Client candidate import request");

        $user = Auth::user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $result = $this->candidateService->importCandidates($request, $clientId, $user);

        return $this->success('Candidate import queued successfully.', $result, 201);
    }

    public function imports(Request $request): JsonResponse
    {
        addInfoLog("Client candidate import list request");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $imports = $this->candidateService->getImportHistory($clientId);

        return $this->success('Candidate imports fetched successfully.', $imports);
    }
    public function destroy($id)
    {
        addInfoLog("Client candidate delete request, ID: {$id}");

        $user = Auth::user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $candidate = \App\Models\Candidate::where('id', $id)
            ->where('client_id', $clientId)
            ->first();

        if (!$candidate) {
            return $this->error('Candidate not found.', 404);
        }

        try {
            $candidate->delete();
            return $this->success('Candidate deleted successfully.', null, 200);
        } catch (\Throwable $e) {
            Log::error('Candidate delete failed', [
                'error' => $e->getMessage(),
                'candidate_id' => $id,
            ]);
            return $this->error('Failed to delete candidate.', 500);
        }
    }
    public function bulkDelete(Request $request)
    {
        addInfoLog("Client candidate bulk delete request");

        $user = Auth::user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $candidateIds = $request->input('candidate_ids', []);

        if (empty($candidateIds) || !is_array($candidateIds)) {
            return $this->error('No valid candidate IDs provided.', 422);
        }

        try {
            \App\Models\Candidate::whereIn('id', $candidateIds)
                ->where('client_id', $clientId)
                ->delete();

            return $this->success('Candidates deleted successfully.', null, 200);
        } catch (\Throwable $e) {
            Log::error('Candidate bulk delete failed', [
                'error' => $e->getMessage(),
                'candidate_ids' => $candidateIds,
            ]);
            return $this->error('Failed to delete candidates.', 500);
        }
    }

    public function export(Request $request)
    {
        addInfoLog("Client candidate export request");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $format = strtolower((string) $request->query('format', 'csv'));
        if (!in_array($format, ['csv', 'xlsx'], true)) {
            return $this->error('Invalid format. Allowed formats: csv, xlsx.', 422);
        }

        $params = $request->all();
        $params['limit'] = 5000;
        $params['page'] = 1;

        $result = $this->candidateService->getCandidates($params, $user);
        
        $candidates = $result['items']['list'] ?? $result['list'] ?? [];
        if (isset($result['list']) && is_array($result['list'])) {
            $candidates = $result['list'];
        }

        $headers = [
            'ID',
            'First Name',
            'Last Name',
            'Email',
            'Phone Number',
            'Country',
            'State',
            'City',
            'Status',
            'Created At'
        ];

        $rows = [];
        foreach ($candidates as $candidate) {
            $rows[] = [
                $candidate['id'] ?? '',
                $candidate['first_name'] ?? '',
                $candidate['last_name'] ?? '',
                $candidate['email'] ?? '',
                $candidate['phone_number'] ?? '',
                $candidate['country'] ?? '',
                $candidate['state'] ?? '',
                $candidate['city'] ?? '',
                $candidate['status'] ?? '',
                $candidate['created_at'] ?? ''
            ];
        }

        if ($format === 'csv') {
            $filename = 'candidates-export-' . date('Y-m-d') . '.csv';
            return $this->downloadResponse(function () use ($headers, $rows): void {
                $stream = fopen('php://output', 'wb');
                fputcsv($stream, $headers);
                foreach ($rows as $row) {
                    fputcsv($stream, $row);
                }
                fclose($stream);
            }, $filename, [
                'Content-Type' => 'text/csv',
            ]);
        }

        $filename = 'candidates-export-' . date('Y-m-d') . '.xlsx';
        $binary = $this->candidateService->buildSimpleXlsx($headers, $rows);

        return $this->downloadResponse(
            static function () use ($binary): void {
                echo $binary;
            },
            $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    public function downloadReport(Request $request, $id)
    {
        addInfoLog("Client candidate download report request, ID: {$id}");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $candidate = \App\Models\Candidate::where('id', $id)
            ->where('client_id', $clientId)
            ->first();

        if (!$candidate) {
            return $this->error('Candidate not found.', 404);
        }

        // Find the latest report for this candidate
        $files = \Illuminate\Support\Facades\Storage::disk('local')->files('reports');
        $candidateFiles = array_filter($files, function($file) use ($candidate) {
            return str_starts_with($file, 'reports/Candidate_' . $candidate->id . '_');
        });

        if (empty($candidateFiles)) {
            return $this->error('Report not found for this candidate.', 404);
        }

        // Sort by last modified
        usort($candidateFiles, function($a, $b) {
            return \Illuminate\Support\Facades\Storage::disk('local')->lastModified($b) - \Illuminate\Support\Facades\Storage::disk('local')->lastModified($a);
        });

        $latestReport = $candidateFiles[0];

        return \Illuminate\Support\Facades\Storage::disk('local')->download($latestReport);
    }
}
