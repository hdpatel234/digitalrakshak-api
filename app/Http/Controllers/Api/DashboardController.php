<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\Package;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // For demonstration, we'll return structured data that the Next.js frontend expects.
        // In a real scenario, you would calculate these metrics using actual Models and queries.
        
        $totalCandidates = Candidate::count() ?? 1250;
        $activePackagesCount = Package::where('is_active', true)->count() ?? 12;

        return response()->json([
            'status' => true,
            'data' => [
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
                'active_packages' => Package::where('is_active', true)->take(5)->get()->map(function($pkg) {
                    return [
                        'id' => $pkg->id,
                        'name' => $pkg->name,
                        'price' => $pkg->price,
                        'expires_at' => '2026-12-31' // Placeholder
                    ];
                }),
                'latest_candidates' => Candidate::latest()->take(5)->get()->map(function($cand) {
                    return [
                        'id' => $cand->id,
                        'name' => $cand->first_name . ' ' . $cand->last_name,
                        'status' => 'Pending',
                        'created_at' => $cand->created_at->format('Y-m-d H:i:s')
                    ];
                })
            ]
        ]);
    }
}
