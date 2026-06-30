<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\Client\BaseController;
use App\Models\Candidate;
use App\Models\CandidateInvitation;
use App\Models\Package;
use App\Models\Service;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends BaseController
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $queryStr = $request->input('query', '');
        
        if (empty($queryStr)) {
            return $this->success('Search results fetched successfully.', []);
        }

        $results = [];

        // 1. Search Candidates
        $candidates = Candidate::where('client_id', $clientId)
            ->where(function ($q) use ($queryStr) {
                $q->where('first_name', 'like', "%{$queryStr}%")
                  ->orWhere('last_name', 'like', "%{$queryStr}%")
                  ->orWhere('email', 'like', "%{$queryStr}%")
                  ->orWhere('phone', 'like', "%{$queryStr}%");
            })
            ->limit(5)
            ->get();

        if ($candidates->isNotEmpty()) {
            $candidateData = $candidates->map(function ($c) {
                $fullName = trim("{$c->first_name} {$c->last_name}");
                return [
                    'key' => "candidate-{$c->id}",
                    'path' => "/candidates/details/{$c->id}",
                    'title' => $fullName . ($c->email ? " ({$c->email})" : ""),
                    'icon' => 'customers',
                    'category' => 'Candidates',
                    'categoryTitle' => 'Candidates',
                ];
            })->toArray();

            $results[] = [
                'title' => 'Candidates',
                'data' => $candidateData,
            ];
        }

        // 2. Search Invitations
        $invitations = CandidateInvitation::where('client_id', $clientId)
            ->where(function ($query) use ($queryStr) {
                $query->whereHas('candidate', function ($q) use ($queryStr) {
                    $q->where('email', 'like', "%{$queryStr}%")
                      ->orWhere('first_name', 'like', "%{$queryStr}%")
                      ->orWhere('last_name', 'like', "%{$queryStr}%");
                })
                ->orWhere('invitation_token', 'like', "%{$queryStr}%");
            })
            ->with('candidate')
            ->limit(5)
            ->get();

        if ($invitations->isNotEmpty()) {
            $invitationData = $invitations->map(function ($inv) {
                $candidateName = $inv->candidate ? trim("{$inv->candidate->first_name} {$inv->candidate->last_name}") : "Unknown";
                return [
                    'key' => "invitation-{$inv->id}",
                    'path' => "/invitations/all", // Correct invitation list route
                    'title' => "Invitation for {$candidateName} (Token: {$inv->invitation_token})",
                    'icon' => 'invitations',
                    'category' => 'Invitations',
                    'categoryTitle' => 'Invitations',
                ];
            })->toArray();

            $results[] = [
                'title' => 'Invitations',
                'data' => $invitationData,
            ];
        }

        // 3. Search Packages
        $packages = Package::where('client_id', $clientId)
            ->where('package_name', 'like', "%{$queryStr}%")
            ->limit(5)
            ->get();

        if ($packages->isNotEmpty()) {
            $packageData = $packages->map(function ($p) {
                return [
                    'key' => "package-{$p->id}",
                    'path' => "/packages/list", // Route to packages list
                    'title' => $p->package_name,
                    'icon' => 'products',
                    'category' => 'Packages',
                    'categoryTitle' => 'Packages',
                ];
            })->toArray();

            $results[] = [
                'title' => 'Packages',
                'data' => $packageData,
            ];
        }

        // 4. Search Services
        $services = Service::where('service_name', 'like', "%{$queryStr}%")
            ->limit(5)
            ->get();

        if ($services->isNotEmpty()) {
            $serviceData = $services->map(function ($s) {
                return [
                    'key' => "service-{$s->id}",
                    'path' => "/services/list", // Route to services list
                    'title' => $s->service_name,
                    'icon' => 'services',
                    'category' => 'Services',
                    'categoryTitle' => 'Services',
                ];
            })->toArray();

            $results[] = [
                'title' => 'Services',
                'data' => $serviceData,
            ];
        }

        return $this->success('Search results fetched successfully.', $results);
    }
}
