<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\RoutePermission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SyncRolesAndPermissions extends Command
{
    protected $signature = 'rbac:sync {--assign-users : Assign role to existing users}';

    protected $description = 'Create route-based permissions, sync roles, and optionally assign users.';

    public function handle(): int
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $roles = [
            'admin' => Role::findOrCreate('admin', 'api'),
            'client' => Role::findOrCreate('client', 'api'),
        ];

        $permissionMatrix = $this->buildPermissionMatrix();

        foreach (array_keys($permissionMatrix) as $permissionName) {
            Permission::findOrCreate($permissionName, 'api');
        }

        $roles['admin']->syncPermissions(
            collect($permissionMatrix)
                ->filter(fn (array $audience): bool => in_array('admin', $audience, true))
                ->keys()
                ->all()
        );

        $roles['client']->syncPermissions(
            collect($permissionMatrix)
                ->filter(fn (array $audience): bool => in_array('client', $audience, true))
                ->keys()
                ->all()
        );

        $this->info('Permissions and roles synced successfully.');
        $this->line('Total permissions: '.count($permissionMatrix));

        if ($this->option('assign-users')) {
            $assigned = $this->assignUsers($roles);
            $this->line("Users updated: {$assigned}");
        } else {
            $this->line('Use --assign-users to assign roles to existing users.');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return self::SUCCESS;
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function buildPermissionMatrix(): array
    {
        $matrix = [];

        foreach (Route::getRoutes() as $route) {
            $uri = RoutePermission::normalizeUri($route->uri());
            $methods = array_values(array_diff($route->methods(), ['HEAD', 'OPTIONS']));

            if (Str::startsWith($uri, 'admin.')) {
                foreach ($methods as $method) {
                    $matrix[RoutePermission::fromUriAndMethod($uri, $method)] = ['admin'];
                }

                continue;
            }

            if (Str::startsWith($uri, 'client.')) {
                foreach ($methods as $method) {
                    $matrix[RoutePermission::fromUriAndMethod($uri, $method)] = ['client'];
                }

                continue;
            }

            if (Str::startsWith($uri, 'v1.auth.') && in_array('auth:api', $route->gatherMiddleware(), true)) {
                foreach ($methods as $method) {
                    $matrix[RoutePermission::fromUriAndMethod($uri, $method)] = ['admin', 'client'];
                }
            }
        }

        ksort($matrix);

        return $matrix;
    }

    private function assignUsers(array $roles): int
    {
        if (!Schema::hasTable('users')) {
            $this->warn('users table not found. Skipping user assignment.');
            return 0;
        }

        $hasIsAdmin = Schema::hasColumn('users', 'is_admin');
        $hasUserType = Schema::hasColumn('users', 'user_type');
        $count = 0;

        User::query()->chunkById(200, function ($users) use (&$count, $roles, $hasIsAdmin, $hasUserType): void {
            foreach ($users as $user) {
                $targetRole = $this->resolveRoleName($user, $hasIsAdmin, $hasUserType);
                $user->syncRoles([$roles[$targetRole]]);
                $count++;
            }
        });

        return $count;
    }

    private function resolveRoleName(User $user, bool $hasIsAdmin, bool $hasUserType): string
    {
        if ($hasIsAdmin && (bool) $user->getAttribute('is_admin')) {
            return 'admin';
        }

        if ($hasUserType) {
            $userType = Str::lower((string) $user->getAttribute('user_type'));
            if (in_array($userType, ['admin', 'administrator', 'super_admin'], true)) {
                return 'admin';
            }
        }

        return 'client';
    }
}
