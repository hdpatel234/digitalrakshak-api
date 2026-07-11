<?php

namespace App\Services\EmailServerTesting\Testers;

use App\Models\EmailServer;
use App\Services\EmailServerTesting\EmailServerTesterInterface;

class DummyTester implements EmailServerTesterInterface
{
    public function test(EmailServer $server): array
    {
        $typeName = $server->serverType ? $server->serverType->type_name : 'Unknown';
        
        $logs = [
            "[INFO] Initiating connection test for server ID: {$server->id} ({$server->server_name})",
            "[WARNING] No specific tester implemented for server type '{$typeName}'.",
            "[INFO] Using DummyTester...",
            "[SUCCESS] Dummy connection test successful.",
            "[INFO] Connection test completed successfully."
        ];

        return [
            'status' => 'success',
            'logs' => $logs
        ];
    }
}
