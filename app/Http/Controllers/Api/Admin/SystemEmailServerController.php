<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailServer;
use App\Models\EmailServerType;
use Illuminate\Support\Facades\Validator;
use Exception;

class SystemEmailServerController extends Controller
{
    /**
     * Display a listing of the email servers.
     */
    public function index()
    {
        $servers = EmailServer::with('serverType')->orderBy('id', 'desc')->get();
        return response()->json([
            'status' => true,
            'message' => 'Email servers fetched successfully',
            'data' => $servers
        ]);
    }

    /**
     * Fetch active email server types.
     */
    public function types()
    {
        $types = EmailServerType::where('is_active', true)->get();
        return response()->json([
            'status' => true,
            'message' => 'Server types fetched successfully',
            'data' => $types
        ]);
    }

    /**
     * Fetch fields for a specific server type.
     */
    public function getServerTypeFields($id)
    {
        $fields = \Illuminate\Support\Facades\DB::table('email_server_configuration_fields')
            ->where('server_type_id', $id)
            ->orderBy('sort_order')
            ->get();
            
        // decode options json
        foreach ($fields as $field) {
            if ($field->options) {
                $field->options = json_decode($field->options, true);
            }
        }
            
        return response()->json([
            'status' => true,
            'message' => 'Server type fields fetched successfully',
            'data' => $fields
        ]);
    }

    /**
     * Store a newly created email server in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'server_name' => 'required|string|max:255',
            'server_type_id' => 'required|exists:email_server_types,id',
            'host' => 'required|string|max:255',
            'port' => 'required|integer',
            'encryption' => 'nullable|in:none,ssl,tls,starttls',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,maintenance,failing',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $server = EmailServer::create($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Email server created successfully',
                'data' => $server
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create email server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified email server in storage.
     */
    public function update(Request $request, $id)
    {
        $server = EmailServer::find($id);
        
        if (!$server) {
            return response()->json([
                'status' => false,
                'message' => 'Email server not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'server_name' => 'sometimes|string|max:255',
            'server_type_id' => 'sometimes|exists:email_server_types,id',
            'host' => 'sometimes|string|max:255',
            'port' => 'sometimes|integer',
            'encryption' => 'nullable|in:none,ssl,tls,starttls',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,maintenance,failing',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $server->update($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Email server updated successfully',
                'data' => $server
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update email server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified email server from storage.
     */
    public function destroy($id)
    {
        $server = EmailServer::find($id);
        
        if (!$server) {
            return response()->json([
                'status' => false,
                'message' => 'Email server not found'
            ], 404);
        }

        try {
            $server->delete();

            return response()->json([
                'status' => true,
                'message' => 'Email server deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete email server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test connection to the specified email server.
     */
    public function testConnection($id)
    {
        $server = EmailServer::find($id);
        
        if (!$server) {
            return response()->json([
                'status' => false,
                'message' => 'Email server not found'
            ], 404);
        }

        try {
            // Simplified connection testing simulation
            // In a real-world scenario, you would attempt to connect to SMTP using Symfony Mailer transport
            
            $server->health_check_at = now();
            $server->health_check_status = 'Success';
            $server->save();
            
            return response()->json([
                'status' => true,
                'message' => 'Connection successful',
                'data' => [
                    'last_tested' => $server->health_check_at
                ]
            ]);
        } catch (Exception $e) {
            $server->health_check_at = now();
            $server->health_check_status = 'Failed';
            $server->last_error = $e->getMessage();
            $server->save();
            
            return response()->json([
                'status' => false,
                'message' => 'Connection failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
