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
        // Validate date inputs to prevent malicious date injection
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'transaction_type' => 'nullable|string|in:subscriptions,one_time,all',
            'status' => 'nullable|string|in:completed,failed,pending,all',
            'platform' => 'nullable|string|max:50',
        ]);

        $startDate = $validated['start_date']
            ? Carbon::parse($validated['start_date'])->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();

        $endDate = $validated['end_date']
            ? Carbon::parse($validated['end_date'])->endOfDay()
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
    public function serviceFilters()
    {
        $categories = \App\Models\ServiceCategory::where('status', 'active')->get()->map(function($cat) {
            return ['value' => $cat->id, 'label' => $cat->category_name];
        })->toArray();
        array_unshift($categories, ['value' => 'all', 'label' => 'All Categories']);

        $statuses = [
            ['value' => 'all', 'label' => 'All Statuses'],
            ['value' => 'pending', 'label' => 'Pending'],
            ['value' => 'in_progress', 'label' => 'In Progress'],
            ['value' => 'completed', 'label' => 'Completed'],
            ['value' => 'failed', 'label' => 'Failed'],
            ['value' => 'cancelled', 'label' => 'Cancelled'],
        ];

        return $this->success('Filters fetched successfully.', [
            'categories' => $categories,
            'statuses' => $statuses
        ]);
    }

    public function services(Request $request)
    {
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))->startOfDay() 
            : Carbon::now()->subDays(30)->startOfDay();
            
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date'))->endOfDay() 
            : Carbon::now()->endOfDay();

        $status = $request->input('status', 'all');
        $category = $request->input('category', 'all');

        $query = \App\Models\OrderItem::query()
            ->join('services', 'order_items.service_id', '=', 'services.id')
            ->select('order_items.*', 'services.service_category')
            ->whereBetween('order_items.created_at', [$startDate, $endDate]);

        if ($status !== 'all') {
            $query->where('order_items.processing_status', $status)
                  ->orWhere('order_items.status', $status);
        }

        if ($category !== 'all') {
            $query->where('services.service_category', $category);
        }

        $cloneQuery = clone $query;
        $totalServices = (clone $cloneQuery)->count();
        $activeServices = (clone $cloneQuery)->whereIn('order_items.processing_status', ['pending', 'in_progress'])->count();
        
        $totalCategories = (clone $cloneQuery)->distinct('services.service_category')->count('services.service_category');
        $totalRevenue = (clone $cloneQuery)->sum('order_items.total_price');

        $prefix = DB::connection()->getTablePrefix();

        // Generate chart data (monthly grouping)
        $chartDataQuery = (clone $cloneQuery)
            ->select(
                DB::raw("YEAR({$prefix}order_items.created_at) as year"),
                DB::raw("MONTH({$prefix}order_items.created_at) as month"),
                DB::raw("COUNT({$prefix}order_items.id) as total_count"),
                DB::raw("SUM({$prefix}order_items.total_price) as total_revenue")
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $categoriesList = [];
        $servicesCountData = [];
        $revenueData = [];

        $currentDate = $startDate->copy()->startOfMonth();
        $endMonth = $endDate->copy()->startOfMonth();
        
        while ($currentDate <= $endMonth) {
            $categoriesList[] = $currentDate->format('M');
            
            $monthData = $chartDataQuery->firstWhere(function ($item) use ($currentDate) {
                return $item->year == $currentDate->year && $item->month == $currentDate->month;
            });
            
            $servicesCountData[] = $monthData ? (int) $monthData->total_count : 0;
            $revenueData[] = $monthData ? (float) $monthData->total_revenue : 0;

            $currentDate->addMonth();
        }

        // Top services
        $topServices = (clone $query)
            ->select(
                'services.id',
                'services.service_name',
                DB::raw("COUNT({$prefix}order_items.id) as total_purchases"),
                DB::raw("SUM({$prefix}order_items.total_price) as total_revenue")
            )
            ->groupBy('services.id', 'services.service_name')
            ->orderBy('total_purchases', 'desc')
            ->limit(5)
            ->get();

        return $this->success('Services report fetched successfully.', [
            'stats' => [
                'totalServices' => ['value' => $totalServices, 'growShrink' => 0, 'comparePeriod' => 'vs selected range'],
                'activeServices' => ['value' => $activeServices, 'growShrink' => 0, 'comparePeriod' => 'vs selected range'],
                'totalCategories' => ['value' => $totalCategories, 'growShrink' => 0, 'comparePeriod' => 'vs selected range'],
                'totalRevenue' => ['value' => $totalRevenue, 'growShrink' => 0, 'comparePeriod' => 'vs selected range'],
            ],
            'chart' => [
                'categories' => $categoriesList,
                'series' => [
                    [
                        'name' => 'Purchased Volume',
                        'data' => $servicesCountData
                    ],
                    [
                        'name' => 'Revenue generated',
                        'data' => $revenueData
                    ]
                ]
            ],
            'top_services' => $topServices
        ]);
    }

    public function clients(Request $request)
    {
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))->startOfDay() 
            : Carbon::now()->subDays(30)->startOfDay();
            
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date'))->endOfDay() 
            : Carbon::now()->endOfDay();

        $status = $request->input('status', 'all');

        $query = \App\Models\Client::query()
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $cloneQuery = clone $query;
        $totalClients = (clone $cloneQuery)->count();
        $activeClients = (clone $cloneQuery)->where('status', 'active')->count();

        // Getting total candidates related to these clients
        $clientIds = (clone $cloneQuery)->pluck('id');
        $totalCandidates = \App\Models\Candidate::whereIn('client_id', $clientIds)->count();

        $prefix = DB::connection()->getTablePrefix();

        // Generate chart data (monthly grouping)
        $chartDataQuery = (clone $cloneQuery)
            ->select(
                DB::raw("YEAR({$prefix}clients.created_at) as year"),
                DB::raw("MONTH({$prefix}clients.created_at) as month"),
                DB::raw("COUNT({$prefix}clients.id) as total_count")
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $categories = [];
        $clientsCountData = [];

        $currentDate = $startDate->copy()->startOfMonth();
        $endMonth = $endDate->copy()->startOfMonth();
        
        while ($currentDate <= $endMonth) {
            $categories[] = $currentDate->format('M');
            
            $monthData = $chartDataQuery->firstWhere(function ($item) use ($currentDate) {
                return $item->year == $currentDate->year && $item->month == $currentDate->month;
            });
            
            $clientsCountData[] = $monthData ? (int) $monthData->total_count : 0;

            $currentDate->addMonth();
        }

        // Top clients
        $topClients = (clone $query)
            ->withCount('candidateOrders')
            ->orderBy('candidate_orders_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'company_name' => $client->company_name,
                    'email' => $client->email,
                    'status' => $client->status,
                    'orders_count' => $client->candidate_orders_count,
                    'date' => $client->created_at->timestamp,
                ];
            });

        return $this->success('Clients report fetched successfully.', [
            'stats' => [
                'totalClients' => ['value' => $totalClients, 'growShrink' => 0, 'comparePeriod' => 'vs selected range'],
                'activeClients' => ['value' => $activeClients, 'growShrink' => 0, 'comparePeriod' => 'vs selected range'],
                'totalCandidates' => ['value' => $totalCandidates, 'growShrink' => 0, 'comparePeriod' => 'vs selected range'],
            ],
            'chart' => [
                'categories' => $categories,
                'series' => [
                    [
                        'name' => 'New Clients',
                        'data' => $clientsCountData
                    ]
                ]
            ],
            'top_clients' => $topClients
        ]);
    }

    public function candidates(Request $request)
    {
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))->startOfDay() 
            : Carbon::now()->subDays(30)->startOfDay();
            
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date'))->endOfDay() 
            : Carbon::now()->endOfDay();

        $status = $request->input('status', 'all');

        $query = \App\Models\Candidate::query()
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $cloneQuery = clone $query;
        $totalCandidates = (clone $cloneQuery)->count();
        $verifiedCandidates = (clone $cloneQuery)->where('status', 'completed')->count(); // Adjust status based on actual statuses
        $pendingCandidates = (clone $cloneQuery)->whereIn('status', ['pending', 'in_progress'])->count();

        $prefix = DB::connection()->getTablePrefix();

        // Generate chart data (monthly grouping)
        $chartDataQuery = (clone $cloneQuery)
            ->select(
                DB::raw("YEAR({$prefix}candidates.created_at) as year"),
                DB::raw("MONTH({$prefix}candidates.created_at) as month"),
                DB::raw("COUNT({$prefix}candidates.id) as total_count")
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $categories = [];
        $candidatesCountData = [];

        $currentDate = $startDate->copy()->startOfMonth();
        $endMonth = $endDate->copy()->startOfMonth();
        
        while ($currentDate <= $endMonth) {
            $categories[] = $currentDate->format('M');
            
            $monthData = $chartDataQuery->firstWhere(function ($item) use ($currentDate) {
                return $item->year == $currentDate->year && $item->month == $currentDate->month;
            });
            
            $candidatesCountData[] = $monthData ? (int) $monthData->total_count : 0;

            $currentDate->addMonth();
        }

        // Recent candidates
        $recentCandidates = (clone $query)
            ->with('client')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($candidate) {
                return [
                    'id' => $candidate->id,
                    'name' => $candidate->first_name . ' ' . $candidate->last_name,
                    'email' => $candidate->email,
                    'client' => $candidate->client ? $candidate->client->company_name : 'Unknown',
                    'status' => $candidate->status,
                    'date' => $candidate->created_at->timestamp,
                ];
            });

        return $this->success('Candidates report fetched successfully.', [
            'stats' => [
                'totalCandidates' => ['value' => $totalCandidates, 'growShrink' => 0, 'comparePeriod' => 'vs selected range'],
                'verifiedCandidates' => ['value' => $verifiedCandidates, 'growShrink' => 0, 'comparePeriod' => 'vs selected range'],
                'pendingCandidates' => ['value' => $pendingCandidates, 'growShrink' => 0, 'comparePeriod' => 'vs selected range'],
            ],
            'chart' => [
                'categories' => $categories,
                'series' => [
                    [
                        'name' => 'New Candidates',
                        'data' => $candidatesCountData
                    ]
                ]
            ],
            'recent_candidates' => $recentCandidates
        ]);
    }
}
