<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\EmailServer;
use App\Models\EmailQueue;

class SystemEmailController extends Controller
{
    public function overview(Request $request)
    {
        $stats = [
            'total_sent' => EmailLog::whereIn('status', ['sent', 'delivered'])->count(),
            'total_templates' => EmailTemplate::count(),
            'total_servers' => EmailServer::count(),
            'total_queued' => EmailQueue::where('status', 'pending')->count(),
        ];

        $recent_logs = EmailLog::latest()->take(5)->get()->map(function($log) {
            return [
                'id' => $log->id,
                'recipient_name' => '', // Using empty string as there's no name field
                'recipient_email' => $log->to_email,
                'subject' => $log->subject,
                'status' => $log->status,
                'created_at' => $log->created_at,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Overview data fetched successfully',
            'data' => [
                'stats' => $stats,
                'recent_logs' => $recent_logs,
            ]
        ]);
    }
    public function templates(Request $request)
    {
        $limit = $request->get('limit', 10);
        $templates = EmailTemplate::paginate($limit);

        $stats = [
            'total' => EmailTemplate::count(),
            'active' => EmailTemplate::where('is_active', 1)->count(),
            'inactive' => EmailTemplate::where('is_active', 0)->count(),
        ];

        return response()->json([
            'status' => true,
            'message' => 'Templates fetched successfully',
            'data' => [
                'templates' => $templates,
                'stats' => $stats
            ]
        ]);
    }
}
