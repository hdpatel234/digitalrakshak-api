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
            'admin.dashboard.view',
            'admin.operations.clients.view',
            'admin.operations.clients.create',
            'admin.operations.clients.edit',
            'admin.operations.clients.delete',
            'admin.operations.clients.pricing',
            'admin.operations.services.view',
            'admin.operations.services.create',
            'admin.operations.services.edit',
            'admin.operations.services.delete',
            'admin.operations.services_fields.view',
            'admin.operations.processing_rules.view',
            'admin.operations.service_dependencies.view',
            'admin.operations.service_provider.view',
            'admin.operations.service_provider.create',
            'admin.operations.service_provider.edit',
            'admin.billing.invoices.view',
            'admin.billing.payment_gateways.view',
            'admin.billing.transactions.view',
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
