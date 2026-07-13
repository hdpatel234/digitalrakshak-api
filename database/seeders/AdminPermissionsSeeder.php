<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class AdminPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function up(): void
    {
        $permissions = [
            'admin_dashboard_view',
            
            // Operations
            'admin_operations_client_management',
            'admin_operations_service_management',
            'admin_operations_service_providers',
            'admin_operations_package_management',
            'admin_operations_order_management',
            'admin_operations_candidate_overview',

            // Billing, Reports, Support
            'admin_billing_view',
            'admin_reports_view',
            'admin_support_view',

            // System Admin
            'admin_system_admin_users',
            'admin_system_roles_permissions',
            'admin_system_email_management',
            'admin_system_audit_logs',
            'admin_system_queue_monitor',
            'admin_system_failed_jobs',
            'admin_system_cron_jobs',
            'admin_system_webhook_logs',
            'admin_system_api_logs',

            // Settings
            'admin_settings_api_keys',
            'admin_settings_webhooks',
        ];

        DB::beginTransaction();
        try {
            $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'api'], ['is_admin_role' => true, 'is_system' => true]);

            foreach ($permissions as $perm) {
                $permission = Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'api'], ['group' => 'Admin']);
                $superAdmin->givePermissionTo($permission);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function run(): void
    {
        $this->up();
    }
}
