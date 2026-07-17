<?php

namespace App\Services\ApiService\Admin;

use App\Models\Client;
use App\Models\Candidate;
use App\Models\CandidateOrder;
use Illuminate\Support\Carbon;

class DashboardService
{
    public function getOverview()
    {
        $now = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        // Basic Counts
        $totalClients = Client::count();
        $totalCandidates = Candidate::count();
        $totalOrders = CandidateOrder::count();
        
        // Using total_amount for revenue
        $totalRevenue = CandidateOrder::where('payment_status', \App\Enums\PaymentStatus::PAID->value)->sum('total_amount');

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
            
            $verificationData[] = CandidateOrder::whereBetween('created_at', [$start, $end])->count();
            $clientGrowthData[] = Client::whereBetween('created_at', [$start, $end])->count();
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
        $recentOrdersRecords = CandidateOrder::with(['client', 'candidates'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentOrders = $recentOrdersRecords->map(function ($order) {
            $candidateName = $order->candidates->first() ? ($order->candidates->first()->first_name . ' ' . $order->candidates->first()->last_name) : 'Unknown';
            $clientName = $order->client ? ($order->client->company_name ?? $order->client->first_name) : 'Unknown';
            return [
                'id' => $order->order_number ?? 'ORD-'.$order->id,
                'client' => $clientName,
                'candidate' => $candidateName,
                'status' => $order->status ?? 'Processing',
                'date' => $order->created_at->format('Y-m-d'),
                'amount' => (float) $order->total_amount,
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
