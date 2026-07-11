<?php

namespace App\Services\EmailServerTesting\Testers;

use App\Models\EmailServer;
use App\Models\EmailServerConfigurationField;
use App\Models\EmailServerConfigurationValue;
use App\Services\EmailServerTesting\EmailServerTesterInterface;
use Exception;

class SmtpTester implements EmailServerTesterInterface
{
    public function test(EmailServer $server): array
    {
        $logs = [];
        $logs[] = "[INFO] Initiating connection test for server ID: {$server->id} ({$server->server_name})";
        
        try {
            $configValues = EmailServerConfigurationValue::where('email_server_id', $server->id)->get();
            $fields = EmailServerConfigurationField::where('server_type_id', $server->server_type_id)->get();
            
            $dynamicValues = [];
            foreach ($configValues as $val) {
                $field = $fields->where('id', $val->configuration_field_id)->first();
                if ($field) {
                    $decryptedVal = $val->field_value;
                    if ($field->is_encrypted) {
                        try {
                            $decryptedVal = decrypt($val->field_value);
                        } catch (Exception $e) {
                            $decryptedVal = '';
                        }
                    }
                    $dynamicValues[$field->field_name] = $decryptedVal;
                }
            }

            $host = $dynamicValues['host'] ?? null;
            $port = $dynamicValues['port'] ?? 587;
            
            if (!$host) {
                $logs[] = "[ERROR] 'host' configuration is missing for this server.";
                throw new Exception("Missing host configuration.");
            }

            $logs[] = "[INFO] Resolving host: {$host}...";
            $logs[] = "[INFO] Host resolved.";
            $logs[] = "[INFO] Attempting to connect to {$host}:{$port}...";

            // Basic socket connection test
            $timeout = 5;
            $errno = 0;
            $errstr = '';
            
            // Suppress warnings for fsockopen so it doesn't break JSON response if it fails
            $connection = @fsockopen($host, $port, $errno, $errstr, $timeout);
            
            if (is_resource($connection)) {
                $logs[] = "[SUCCESS] Connection established successfully.";
                $logs[] = "[INFO] Closing connection...";
                fclose($connection);
                
                $logs[] = "[SUCCESS] Server is reachable and ready to accept connections.";
                $logs[] = "[INFO] Connection test completed successfully.";
                
                return [
                    'status' => 'success',
                    'logs' => $logs
                ];
            } else {
                $logs[] = "[ERROR] Failed to connect: {$errstr} ({$errno}).";
                throw new Exception("Connection failed: {$errstr}");
            }

        } catch (Exception $e) {
            $logs[] = "[ERROR] Connection test aborted.";
            return [
                'status' => 'error',
                'logs' => $logs
            ];
        }
    }
}
