<?php

namespace App\Services\ApiService\Admin;

use App\Repositories\ClientRepository;
use App\Repositories\CandidateRepository;
use App\Repositories\OrderRepository;
use Illuminate\Support\Carbon;

class DashboardService
{
    public function __construct(
        protected ClientRepository $clientRepo,
        protected CandidateRepository $candidateRepo,
        protected OrderRepository $orderRepo
    ) {}

    public function getOverview()
    {
        $now = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        // Basic Counts
        $totalClients = $this->clientRepo->count();
        $totalCandidates = $this->candidateRepo->count();
        $totalOrders = $this->orderRepo->count();

        // Using total_amount for revenue
        $totalRevenue = $this->orderRepo->getTotalRevenue(\App\Enums\PaymentStatus::PAID->value);

        // Basic comparison (mocked percentages for now, can be calculated dynamically based on created_at if needed)
        $statisticData = [
            'totalClients' => [
                'value' => $totalClients,
                'growShrink' => 12.5,
                'comparePeriod' => 'from last month',
            ],
            'totalCandidates' => [
                'value' => $totalCandidates,
                'growShrink' => 8.4,
                'comparePeriod' => 'from last month',
            ],
            'totalOrders' => [
                'value' => $totalOrders,
                'growShrink' => -2.1,
                'comparePeriod' => 'from last month',
            ],
            'totalRevenue' => [
                'value' => (float) $totalRevenue,
                'growShrink' => 15.3,
                'comparePeriod' => 'from last month',
            ],
        ];

        // Trends (Real DB queries)
        $months = [];
        $verificationData = [];
        $clientGrowthData = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = $month->format('M');

            // Real counts for the specific month
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $verificationData[] = $this->orderRepo->countBetweenDates($start, $end);
            $clientGrowthData[] = $this->clientRepo->countBetweenDates($start, $end);
        }

        $verificationTrend = [
            'series' => [
                [
                    'name' => 'Verifications',
                    'data' => $verificationData,
                ],
            ],
            'categories' => $months,
        ];

        $clientGrowth = [
            'series' => [
                [
                    'name' => 'New Clients',
                    'data' => $clientGrowthData,
                ],
            ],
            'categories' => $months,
        ];

        // Top Services (Mocked for now as we might need complex joins for OrderItem -> Service)
        $topServices = [
            'series' => [45, 25, 20, 10],
            'labels' => ['Criminal Check', 'Employment Verification', 'Education Check', 'Address Verification'],
        ];

        // Recent Orders
        $recentOrdersRecords = $this->orderRepo->getRecentOrders(5);

        $recentOrders = $recentOrdersRecords->map(function ($order) {
            $candidateName = $order->candidates->first() ? ($order->candidates->first()->first_name . ' ' . $order->candidates->first()->last_name) : 'Unknown';
            $clientName = $order->client ? ($order->client->company_name ?? $order->client->first_name) : 'Unknown';
            return [
                'id' => $order->{$this->orderRepo->orderNumber()} ?? 'ORD-' . $order->{$this->orderRepo->id()},
                'client' => $clientName,
                'candidate' => $candidateName,
                'status' => $order->{$this->orderRepo->status()} ?? 'Processing',
                'date' => $order->{$this->orderRepo->createdAt()}->format('Y-m-d'),
                'amount' => (float) $order->{$this->orderRepo->totalAmount()},
            ];
        });

        return [
            'statisticData' => $statisticData,
            'verificationTrend' => $verificationTrend,
            'clientGrowth' => $clientGrowth,
            'topServices' => $topServices,
            'recentOrders' => $recentOrders,
        ];
    }
}
