<?php

namespace App\Console\Commands;

use App\Enums\UserType;
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
            UserType::SUPER_ADMIN->value => Role::findOrCreate(UserType::SUPER_ADMIN->value, 'api'),
            UserType::ADMIN->value => Role::findOrCreate(UserType::ADMIN->value, 'api'),
            UserType::CLIENT_ADMIN->value => Role::findOrCreate(UserType::CLIENT_ADMIN->value, 'api'),
            UserType::CLIENT_USER->value => Role::findOrCreate(UserType::CLIENT_USER->value, 'api'),
        ];

        $permissionMatrix = $this->buildPermissionMatrix();
        $allPermissions = array_keys($permissionMatrix);
        $adminPermissions = collect($permissionMatrix)
            ->filter(fn (array $audience): bool => in_array(UserType::ADMIN->value, $audience, true))
            ->keys()
            ->all();
        $clientPermissions = collect($permissionMatrix)
            ->filter(fn (array $audience): bool => in_array(UserType::CLIENT_ADMIN->value, $audience, true))
            ->keys()
            ->all();
        $commonPermissions = collect($permissionMatrix)
            ->filter(fn (array $audience): bool => in_array('common', $audience, true))
            ->keys()
            ->all();

        foreach ($allPermissions as $permissionName) {
            Permission::findOrCreate($permissionName, 'api');
        }

        // Super admin gets complete route access.
        $roles[UserType::SUPER_ADMIN->value]->syncPermissions($allPermissions);

        // Admin gets admin and common permissions.
        $roles[UserType::ADMIN->value]->syncPermissions(array_values(array_unique(array_merge(
            $adminPermissions,
            $commonPermissions
        ))));

        // Client admin gets client and common permissions.
        $roles[UserType::CLIENT_ADMIN->value]->syncPermissions(array_values(array_unique(array_merge(
            $clientPermissions,
            $commonPermissions
        ))));

        // Client user is intentionally minimal by default; it can be extended per user.
        $roles[UserType::CLIENT_USER->value]->syncPermissions($commonPermissions);

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
                    $matrix[RoutePermission::fromUriAndMethod($uri, $method)] = [UserType::ADMIN->value];
                }

                continue;
            }

            if (Str::startsWith($uri, 'client.')) {
                foreach ($methods as $method) {
                    $matrix[RoutePermission::fromUriAndMethod($uri, $method)] = [UserType::CLIENT_ADMIN->value];
                }

                continue;
            }

            if (Str::startsWith($uri, 'v1.auth.') && in_array('auth:api', $route->gatherMiddleware(), true)) {
                foreach ($methods as $method) {
                    $matrix[RoutePermission::fromUriAndMethod($uri, $method)] = ['common'];
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

        $hasUserType = Schema::hasColumn('users', 'user_type');
        $count = 0;

        User::query()->chunkById(200, function ($users) use (&$count, $roles, $hasUserType): void {
            foreach ($users as $user) {
                $targetRole = $this->resolveRoleName($user, $hasUserType);
                $user->syncRoles([$roles[$targetRole]]);
                $count++;
            }
        });

        return $count;
    }

    private function resolveRoleName(User $user, bool $hasUserType): string
    {
        if ($hasUserType) {
            $userTypeAttribute = $user->getAttribute(User::USER_TYPE);
            $userType = $userTypeAttribute instanceof UserType
                ? $userTypeAttribute->value
                : Str::lower((string) $userTypeAttribute);

            if (in_array($userType, User::supportedUserTypes(), true)) {
                return $userType;
            }

            if (in_array($userType, ['administrator', 'admin'], true)) {
                return UserType::ADMIN_USER->value;
            }

            if (in_array($userType, ['client'], true)) {
                return UserType::CLIENT_USER->value;
            }
        }

        return UserType::CLIENT_USER->value;
    }
}
