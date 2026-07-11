<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailServerType;
use App\Services\EmailServerService;
use App\Services\EmailServerConfigurationFieldService;
use App\Http\Requests\Api\Admin\SystemEmailServer\StoreRequest;
use App\Http\Requests\Api\Admin\SystemEmailServer\UpdateRequest;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\EmailServerConfigurationField;
use App\Models\EmailServerConfigurationValue;
use Exception;

class SystemEmailServerController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected EmailServerService $emailServerService,
        protected EmailServerConfigurationFieldService $fieldService
    ) {}

    /**
     * Display a listing of the email servers.
     */
    public function index()
    {
        $servers = $this->emailServerService->query()->with('serverType')->orderBy('id', 'desc')->get();
        return $this->success('Email servers fetched successfully', $servers);
    }

    /**
     * Fetch active email server types.
     */
    public function types()
    {
        $types = EmailServerType::where('is_active', true)->get();
        return $this->success('Server types fetched successfully', $types);
    }

    /**
     * Fetch fields for a specific server type.
     */
    public function getServerTypeFields($id)
    {
        $fields = $this->fieldService->query()
            ->where('server_type_id', $id)
            ->orderBy('sort_order')
            ->get();

        // decode options json
        foreach ($fields as $field) {
            if ($field->options && is_string($field->options)) {
                $field->options = json_decode($field->options, true);
            }
        }

        return $this->success('Server type fields fetched successfully', $fields);
    }

    /**
     * Display the specified email server.
     */
    public function show($id)
    {
        try {
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

            return $this->success('Email server fetched successfully', $serverArray);
        } catch (Exception $e) {
            return $this->error('Email server not found', 404);
        }
    }

    /**
     * Store a newly created email server in storage.
     */
    public function store(StoreRequest $request)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            unset($validated['dynamic_values']);
            $server = $this->emailServerService->create($validated);

            if ($request->has('dynamic_values') && is_array($request->dynamic_values)) {
                $fields = EmailServerConfigurationField::where('server_type_id', $server->server_type_id)->get();
                $valuesToInsert = [];
                foreach ($request->dynamic_values as $key => $value) {
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
            return $this->success('Email server created successfully', $server);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error('Failed to create email server: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified email server in storage.
     */
    public function update(UpdateRequest $request, $id)
    {
        try {
            $server = $this->emailServerService->find($id);
        } catch (Exception $e) {
            return $this->error('Email server not found', 404);
        }

        try {
            DB::beginTransaction();
            $validated = $request->validated();
            unset($validated['dynamic_values']);
            $this->emailServerService->update($id, $validated);

            if ($request->has('dynamic_values') && is_array($request->dynamic_values)) {
                $fields = EmailServerConfigurationField::where('server_type_id', $server->server_type_id)->get();
                
                // We can just delete old values and insert new ones, or update existing. Delete/Insert is easier.
                EmailServerConfigurationValue::where('email_server_id', $id)->delete();
                
                $valuesToInsert = [];
                foreach ($request->dynamic_values as $key => $value) {
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

            $server = $this->emailServerService->find($id);
            return $this->success('Email server updated successfully', $server);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error('Failed to update email server: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $server = $this->emailServerService->find($id);
        } catch (Exception $e) {
            return $this->error('Email server not found', 404);
        }

        try {
            DB::beginTransaction();
            
            // Soft delete configuration values
            EmailServerConfigurationValue::where('email_server_id', $id)->delete();
            
            // Soft delete the server
            $this->emailServerService->delete($id);

            DB::commit();
            return $this->success('Email server deleted successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error('Failed to delete email server: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Test connection to the specified email server.
     */
    public function testConnection($id)
    {
        try {
            $server = $this->emailServerService->query()->with('serverType')->findOrFail($id);
        } catch (Exception $e) {
            return $this->error('Email server not found', 404);
        }

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

            if ($result['status'] === 'success') {
                return $this->success('Connection successful', [
                    'last_tested' => now(),
                    'logs' => $result['logs'],
                    'status' => 'success'
                ]);
            } else {
                return $this->error('Connection failed', 500, [
                    'logs' => $result['logs'],
                    'status' => 'error'
                ]);
            }
        } catch (Exception $e) {
            $this->emailServerService->update($id, [
                'health_check_at' => now(),
                'health_check_status' => 'Failed',
                'status' => 'failing',
                'last_error' => $e->getMessage()
            ]);

            return $this->error('Connection failed: ' . $e->getMessage(), 500, [
                'logs' => ["[ERROR] An unexpected error occurred: " . $e->getMessage()],
                'status' => 'error'
            ]);
        }
    }
    /**
     * Send a test email using the specified email server.
     */
    public function sendTestEmail(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return $this->error('Validation Error', 422, $validator->errors());
        }

        try {
            $server = $this->emailServerService->query()->with('serverType')->findOrFail($id);
        } catch (Exception $e) {
            return $this->error('Email server not found', 404);
        }

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

            // Temporarily configure the mailer
            $mailerName = 'test_mailer_' . $server->id;
            \Illuminate\Support\Facades\Config::set("mail.mailers.{$mailerName}", [
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

            \Illuminate\Support\Facades\Mail::mailer($mailerName)->raw('This is a test email to verify your email server configuration.', function ($message) use ($request, $fromAddress, $fromName) {
                $message->from($fromAddress, $fromName);
                $message->to($request->email);
                $message->subject('Test Email - Digital Rakshak');
            });

            return $this->success('Test email sent successfully');
        } catch (Exception $e) {
            return $this->error('Failed to send test email: ' . $e->getMessage(), 500);
        }
    }
}
