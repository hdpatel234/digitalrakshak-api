<?php

namespace App\Services\ApiService\Client;

use App\Repositories\CandidateRepository;
use App\Repositories\PackageRepository;
use App\Repositories\PackageServiceRepository;

class DashboardService
{
    public function __construct(
        protected CandidateRepository $candidateRepo,
        protected PackageRepository $packageRepo,
        protected PackageServiceRepository $packageServiceRepo
    ) {}

    public function getDashboardData(int $clientId)
    {
        $totalCandidates = $this->candidateRepo->query()->where($this->candidateRepo->clientId(), $clientId)->count() ?? 1250;
        $activePackagesCount = $this->packageRepo->query()->where($this->packageRepo->isActive(), true)->where($this->packageRepo->clientId(), $clientId)->count() ?? 12;

        return [
            'stats' => [
                'total_verifications' => $totalCandidates,
                'in_progress' => 45,
                'completed' => 1105,
                'flagged' => 12
            ],
            'verification_trend' => [
                ['month' => 'Jan', 'verifications' => 120],
                ['month' => 'Feb', 'verifications' => 150],
                ['month' => 'Mar', 'verifications' => 200],
                ['month' => 'Apr', 'verifications' => 180],
                ['month' => 'May', 'verifications' => 250],
                ['month' => 'Jun', 'verifications' => 280],
            ],
            'recent_activities' => [
                [
                    'id' => 1,
                    'description' => 'John Doe verification completed',
                    'timestamp' => '2 hours ago',
                    'type' => 'success'
                ],
                [
                    'id' => 2,
                    'description' => 'New package "Gold" purchased',
                    'timestamp' => '5 hours ago',
                    'type' => 'info'
                ],
                [
                    'id' => 3,
                    'description' => 'Jane Smith flagged for review',
                    'timestamp' => '1 day ago',
                    'type' => 'warning'
                ]
            ],
            'service_usage' => [
                ['service' => 'Identity Check', 'usage' => 500],
                ['service' => 'Criminal Record', 'usage' => 300],
                ['service' => 'Education Check', 'usage' => 250],
                ['service' => 'Employment Check', 'usage' => 400],
            ],
            'month_spend' => [
                'total' => 12500,
                'currency' => 'INR',
                'breakdown' => [
                    ['category' => 'Identity Checks', 'amount' => 5000],
                    ['category' => 'Criminal Records', 'amount' => 3000],
                    ['category' => 'Other Services', 'amount' => 4500],
                ]
            ],
            'active_packages' => $this->packageRepo->query()->where($this->packageRepo->isActive(), true)
                ->where(function ($query) use ($clientId) {
                    $query->where($this->packageRepo->type(), 'admin')
                        ->orWhere($this->packageRepo->clientId(), $clientId);
                })
                ->get()
                ->map(function ($pkg) {
                    $servicesCount = $this->packageServiceRepo->query()->where($this->packageServiceRepo->packageId(), $pkg->{$this->packageRepo->id()})->count();
                    return [
                        'id' => $pkg->{$this->packageRepo->id()},
                        'name' => $pkg->{$this->packageRepo->packageName()} ?? $pkg->name ?? 'Unknown Package',
                        'price' => $pkg->{$this->packageRepo->finalPrice()} ?? $pkg->total_price ?? 0,
                        'services_count' => $servicesCount,
                        'type' => $pkg->{$this->packageRepo->type()},
                        'expires_at' => '2026-12-31' // Placeholder
                    ];
                }),
            'latest_candidates' => $this->candidateRepo->query()->where($this->candidateRepo->clientId(), $clientId)->withCount('packages')->latest()->take(5)->get()->map(function ($cand) {
                return [
                    'id' => $cand->{$this->candidateRepo->id()},
                    'name' => $cand->{$this->candidateRepo->firstName()} . ' ' . $cand->{$this->candidateRepo->lastName()},
                    'email' => $cand->{$this->candidateRepo->email()},
                    'packages_count' => $cand->packages_count,
                    'status' => $cand->{$this->candidateRepo->status()} ?? 'Pending',
                    'created_at' => $cand->{$this->candidateRepo->createdAt()} ? $cand->{$this->candidateRepo->createdAt()}->format('Y-m-d H:i:s') : null
                ];
            })
        ];
    }
}
