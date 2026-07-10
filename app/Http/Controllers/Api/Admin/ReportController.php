<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Models\PaymentTransaction;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends BaseController
{
    use ApiResponse;

    public function revenue(Request $request)
    {
        // Default to last 30 days if dates are not provided
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))->startOfDay() 
            : Carbon::now()->subDays(30)->startOfDay();
            
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date'))->endOfDay() 
            : Carbon::now()->endOfDay();

        $transactionType = $request->input('transaction_type', 'all');
        $status = $request->input('status', 'all');
        $platform = $request->input('platform', 'all');

        $query = PaymentTransaction::query()
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($platform !== 'all') {
            // Need to join gatewayConfig or paymentGateway to filter by platform
            // Assuming platform refers to gateway_code in PaymentGateway
            $query->whereHas('gatewayConfig.gateway', function($q) use ($platform) {
                $q->where('gateway_code', $platform);
            });
        }

        // For transaction types (subscriptions vs one_time) - handle if applicable to your schema
        // This is a placeholder logic for transaction_type
        // if ($transactionType === 'subscriptions') {
        //     $query->whereNotNull('subscription_id');
        // }

        $cloneQuery = clone $query;
        $totalRevenue = (clone $cloneQuery)->where('status', 'completed')->sum('amount');
        
        // Mock logic for MRR or just subset of revenue
        $mrr = (clone $cloneQuery)->where('status', 'completed')->whereNotNull('invoice_id')->sum('amount') * 0.4; // Mock logic

        $refunds = (clone $cloneQuery)->where('status', 'refunded')->sum('amount');
        
        $netProfit = $totalRevenue - $refunds;

        // Generate chart data (monthly grouping)
        $chartDataQuery = (clone $cloneQuery)
            ->where('status', 'completed')
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $categories = [];
        $revenueData = [];
        $netProfitData = [];

        $currentDate = $startDate->copy()->startOfMonth();
        $endMonth = $endDate->copy()->startOfMonth();
        
        while ($currentDate <= $endMonth) {
            $categories[] = $currentDate->format('M'); // e.g., Jan
            
            $monthData = $chartDataQuery->firstWhere(function ($item) use ($currentDate) {
                return $item->year == $currentDate->year && $item->month == $currentDate->month;
            });
            
            $monthTotal = $monthData ? (float) $monthData->total : 0;
            
            $revenueData[] = $monthTotal;
            $netProfitData[] = $monthTotal * 0.8; // Mock net profit

            $currentDate->addMonth();
        }

        // Recent transactions
        $recentTransactions = (clone $query)
            ->with(['client', 'gatewayConfig.gateway', 'order', 'invoice'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($trx) {
                return [
                    'id' => $trx->gateway_payment_id ?? $trx->transaction_uuid ?? $trx->id,
                    'invoice_id' => $trx->invoice ? $trx->invoice->invoice_number : ($trx->invoice_id ?? 'N/A'),
                    'order_id' => $trx->order ? $trx->order->order_number : ($trx->order_id ?? 'N/A'),
                    'client' => $trx->client ? $trx->client->company_name : 'Unknown',
                    'gateway' => $trx->gatewayConfig && $trx->gatewayConfig->gateway ? $trx->gatewayConfig->gateway->gateway_name : 'Unknown',
                    'date' => $trx->created_at->timestamp,
                    'amount' => (float) $trx->amount,
                    'status' => $trx->status,
                ];
            });

        return $this->success('Revenue report fetched successfully.', [
            'stats' => [
                'totalRevenue' => ['value' => $totalRevenue, 'growShrink' => 0, 'comparePeriod' => 'vs selected range'],
                'mrr' => ['value' => $mrr, 'growShrink' => 0, 'comparePeriod' => 'vs selected range'],
                'netProfit' => ['value' => $netProfit, 'growShrink' => 0, 'comparePeriod' => 'vs selected range'],
                'refunds' => ['value' => $refunds, 'growShrink' => 0, 'comparePeriod' => 'vs selected range'],
            ],
            'chart' => [
                'categories' => $categories,
                'series' => [
                    [
                        'name' => 'Revenue',
                        'data' => $revenueData
                    ],
                    [
                        'name' => 'Net Profit',
                        'data' => $netProfitData
                    ]
                ]
            ],
            'recent_transactions' => $recentTransactions
        ]);
    }

    public function orders(Request $request)
    {
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))->startOfDay() 
            : Carbon::now()->subDays(30)->startOfDay();
            
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date'))->endOfDay() 
            : Carbon::now()->endOfDay();

        $status = $request->input('status', 'all');
        $paymentStatus = $request->input('payment_status', 'all');

        $query = \App\Models\CandidateOrder::query()
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($paymentStatus !== 'all') {
            $query->where('payment_status', $paymentStatus);
        }

        $cloneQuery = clone $query;
        $totalOrders = (clone $cloneQuery)->count();
        $completedOrders = (clone $cloneQuery)->where('status', \App\Enums\OrderStatus::COMPLETED->value ?? 'completed')->count();
        $totalValue = (clone $cloneQuery)->sum('total_amount');
        $avgOrderValue = $totalOrders > 0 ? $totalValue / $totalOrders : 0;

        // Generate chart data (monthly grouping)
        $chartDataQuery = (clone $cloneQuery)
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(id) as total_count'),
                DB::raw('SUM(total_amount) as total_amount')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $categories = [];
        $ordersCountData = [];
        $ordersValueData = [];

        $currentDate = $startDate->copy()->startOfMonth();
        $endMonth = $endDate->copy()->startOfMonth();
        
        while ($currentDate <= $endMonth) {
            $categories[] = $currentDate->format('M');
            
            $monthData = $chartDataQuery->firstWhere(function ($item) use ($currentDate) {
                return $item->year == $currentDate->year && $item->month == $currentDate->month;
            });
            
            $ordersCountData[] = $monthData ? (int) $monthData->total_count : 0;
            $ordersValueData[] = $monthData ? (float) $monthData->total_amount : 0;

            $currentDate->addMonth();
        }

        // Recent orders
        $recentOrders = (clone $query)
            ->with(['client'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'client' => $order->client ? $order->client->company_name : 'Unknown',
                    'date' => $order->created_at->timestamp,
                    'amount' => (float) $order->total_amount,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                ];
            });

        return $this->success('Orders report fetched successfully.', [
            'stats' => [
                'totalOrders' => ['value' => $totalOrders, 'growShrink' => 0, 'comparePeriod' => 'vs selected range'],
                'completedOrders' => ['value' => $completedOrders, 'growShrink' => 0, 'comparePeriod' => 'vs selected range'],
                'totalValue' => ['value' => $totalValue, 'growShrink' => 0, 'comparePeriod' => 'vs selected range'],
                'avgOrderValue' => ['value' => $avgOrderValue, 'growShrink' => 0, 'comparePeriod' => 'vs selected range'],
            ],
            'chart' => [
                'categories' => $categories,
                'series' => [
                    [
                        'name' => 'Total Orders',
                        'data' => $ordersCountData
                    ],
                    [
                        'name' => 'Total Value',
                        'data' => $ordersValueData
                    ]
                ]
            ],
            'recent_orders' => $recentOrders
        ]);
    }
}
