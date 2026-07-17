<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CronJobController extends BaseController
{
    public function index()
    {
        $crons = \App\Models\CronJob::orderBy('id', 'desc')->get();
        return response()->json(['data' => $crons]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'schedule' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $cronJob = \App\Models\CronJob::findOrFail($id);

        $cronJob->update($request->only(['schedule', 'is_active']));

        return response()->json([
            'message' => 'Cron job updated successfully',
            'data' => $cronJob
        ]);
    }

    public function toggle(string $id)
    {
        $cronJob = \App\Models\CronJob::findOrFail($id);
        $cronJob->is_active = !$cronJob->is_active;
        $cronJob->save();

        return response()->json([
            'message' => 'Cron job status toggled successfully',
            'data' => $cronJob
        ]);
    }

    public function run(string $id)
    {
        $cronJob = \App\Models\CronJob::findOrFail($id);

        try {
            \Illuminate\Support\Facades\Artisan::call($cronJob->command);

            $cronJob->update([
                'last_run_at' => now(),
                'status' => 'completed',
            ]);

            return response()->json([
                'message' => 'Cron job executed successfully',
                'output' => \Illuminate\Support\Facades\Artisan::output()
            ]);
        } catch (\Exception $e) {
            $cronJob->update([
                'last_run_at' => now(),
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Cron job execution failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
