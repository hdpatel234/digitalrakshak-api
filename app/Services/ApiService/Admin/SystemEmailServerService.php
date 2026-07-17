<?php

namespace App\Services\ApiService\Admin;

use App\Models\EmailServerType;
use App\Models\EmailServerConfigurationField;
use App\Models\EmailServerConfigurationValue;
use App\Services\EmailServerService;
use App\Services\EmailServerConfigurationFieldService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Exception;

class SystemEmailServerService
{
    public function __construct(
        protected EmailServerService $emailServerService,
        protected EmailServerConfigurationFieldService $fieldService
    ) {}

    public function getServers(array $data)
    {
        $query = $this->emailServerService->query()->with('serverType');

        if (isset($data['search']) && !empty($data['search'])) {
            $search = $data['search'];
            $query->where('server_name', 'like', "%{$search}%");
        }

        if (isset($data['type']) && !empty($data['type']) && $data['type'] !== 'all') {
            $query->where('server_type_id', $data['type']);
        }

        if (isset($data['status']) && !empty($data['status']) && $data['status'] !== 'all') {
            $query->where('status', $data['status']);
        }

        $query->orderBy('id', 'desc');

        if (isset($data['limit'])) {
            return $query->paginate($data['limit']);
        }

        return $query->get();
    }

    public function getServerTypes()
    {
        return EmailServerType::where('is_active', true)->get();
    }

    public function getStatuses()
    {
        return [
            ['status' => 'active'],
            ['status' => 'inactive'],
            ['status' => 'failing']
        ];
    }

    public function getServerTypeFields($id)
    {
        $fields = $this->fieldService->query()
            ->where('server_type_id', $id)
            ->orderBy('sort_order')
            ->get();

        foreach ($fields as $field) {
            if ($field->options && is_string($field->options)) {
                $field->options = json_decode($field->options, true);
            }
        }

        return $fields;
    }

    public function showServer($id)
    {
        $server = $this->emailServerService->query()->with('serverType')->findOrFail($id);
        $configValues = EmailServerConfigurationValue::where('email_server_id', $id)->get();
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

        $serverArray = $server->toArray();
        $serverArray['dynamic_values'] = $dynamicValues;

        return $serverArray;
    }

    public function storeServer(array $data)
    {
        DB::beginTransaction();
        try {
            $dynamicValues = $data['dynamic_values'] ?? null;
            unset($data['dynamic_values']);
            $server = $this->emailServerService->create($data);

            if (is_array($dynamicValues)) {
                $fields = EmailServerConfigurationField::where('server_type_id', $server->server_type_id)->get();
                $valuesToInsert = [];
                foreach ($dynamicValues as $key => $value) {
                    $field = $fields->where('field_name', $key)->first();
                    if ($field) {
                        $val = $value;
                        if ($field->is_encrypted) {
                            $val = encrypt($value);
                        }
                        $valuesToInsert[] = [
                            'email_server_id' => $server->id,
                            'configuration_field_id' => $field->id,
                            'field_value' => $val,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                if (!empty($valuesToInsert)) {
                    EmailServerConfigurationValue::insert($valuesToInsert);
                }
            }

            DB::commit();
            return $server;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to create email server: ' . $e->getMessage());
        }
    }

    public function updateServer($id, array $data)
    {
        $server = $this->emailServerService->find($id);
        if (!$server) {
            throw new Exception('Email server not found', 404);
        }

        DB::beginTransaction();
        try {
            $dynamicValues = $data['dynamic_values'] ?? null;
            unset($data['dynamic_values']);
            $this->emailServerService->update($id, $data);

            if (is_array($dynamicValues)) {
                $fields = EmailServerConfigurationField::where('server_type_id', $server->server_type_id)->get();
                
                EmailServerConfigurationValue::where('email_server_id', $id)->delete();
                
                $valuesToInsert = [];
                foreach ($dynamicValues as $key => $value) {
                    $field = $fields->where('field_name', $key)->first();
                    if ($field) {
                        $val = $value;
                        if ($field->is_encrypted) {
                            $val = encrypt($value);
                        }
                        $valuesToInsert[] = [
                            'email_server_id' => $id,
                            'configuration_field_id' => $field->id,
                            'field_value' => $val,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                if (!empty($valuesToInsert)) {
                    EmailServerConfigurationValue::insert($valuesToInsert);
                }
            }
            
            DB::commit();
            return $this->emailServerService->find($id);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to update email server: ' . $e->getMessage());
        }
    }

    public function deleteServer($id)
    {
        $server = $this->emailServerService->find($id);
        if (!$server) {
            throw new Exception('Email server not found', 404);
        }

        DB::beginTransaction();
        try {
            EmailServerConfigurationValue::where('email_server_id', $id)->delete();
            $this->emailServerService->delete($id);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to delete email server: ' . $e->getMessage());
        }
    }

    public function testConnection($id)
    {
        $server = $this->emailServerService->query()->with('serverType')->findOrFail($id);

        try {
            $tester = \App\Services\EmailServerTesting\EmailServerTesterFactory::make($server->serverType?->type_code);
            $result = $tester->test($server);

            $healthStatus = $result['status'] === 'success' ? 'Success' : 'Failed';
            $status = $result['status'] === 'success' ? 'active' : 'failing';

            $this->emailServerService->update($id, [
                'health_check_at' => now(),
                'health_check_status' => $healthStatus,
                'status' => $status,
                'last_error' => $result['status'] === 'error' ? end($result['logs']) : null
            ]);

            return $result;
        } catch (Exception $e) {
            $this->emailServerService->update($id, [
                'health_check_at' => now(),
                'health_check_status' => 'Failed',
                'status' => 'failing',
                'last_error' => $e->getMessage()
            ]);

            throw new Exception($e->getMessage());
        }
    }

    public function sendTestEmail($id, string $email)
    {
        $server = $this->emailServerService->query()->with('serverType')->findOrFail($id);

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

        // Temporarily configure the mailer
        $mailerName = 'test_mailer_' . $server->id;
        Config::set("mail.mailers.{$mailerName}", [
            'transport' => 'smtp',
            'host' => $dynamicValues['host'] ?? '',
            'port' => $dynamicValues['port'] ?? 587,
            'encryption' => $dynamicValues['encryption'] ?? 'tls',
            'username' => $dynamicValues['username'] ?? '',
            'password' => $dynamicValues['password'] ?? '',
            'timeout' => null,
            'auth_mode' => null,
        ]);

        $fromAddress = $dynamicValues['from_address'] ?? $dynamicValues['username'] ?? 'no-reply@digitalrakshak.com';
        $fromName = $dynamicValues['from_name'] ?? 'Digital Rakshak';

        Mail::mailer($mailerName)->raw('This is a test email to verify your email server configuration.', function ($message) use ($email, $fromAddress, $fromName) {
            $message->from($fromAddress, $fromName);
            $message->to($email);
            $message->subject('Test Email - Digital Rakshak');
        });

        return true;
    }
}
