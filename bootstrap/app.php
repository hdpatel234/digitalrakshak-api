<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use App\Http\Middleware\EnsureRoutePermission;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\SetUserPreferredLocale;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: [
            __DIR__.'/../routes/api-auth.php',
            __DIR__.'/../routes/api-admin.php',
            __DIR__.'/../routes/api-client.php',
        ],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('api', [
            SetUserPreferredLocale::class,
        ]);

        $middleware->alias([
            'auth' => Authenticate::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'permission.route' => EnsureRoutePermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        \Sentry\Laravel\Integration::handles($exceptions);

        $exceptions->dontReport([
            \Illuminate\Validation\ValidationException::class,
            \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
            \Illuminate\Auth\AuthenticationException::class,
            \Illuminate\Http\Exceptions\ThrottleRequestsException::class,
        ]);

        $exceptions->context(function () {
            if (app()->bound('sentry')) {
                $user = request()->user();
                if ($user) {
                    \Sentry\configureScope(function (\Sentry\State\Scope $scope) use ($user) {
                        $scope->setUser([
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                        ]);
                    });
                }
                
                \Sentry\configureScope(function (\Sentry\State\Scope $scope) {
                    $scope->setTag('environment', app()->environment());
                    $scope->setTag('app_name', (string) config('app.name'));
                    $scope->setTag('laravel_version', app()->version());
                    $scope->setTag('php_version', phpversion());
                });
            }
            return [];
        });

        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => __('common.unauthenticated'),
                    'data' => [],
                    'timestamp' => now()->format((string) config('app.user_datetime_format', 'Y-m-d H:i:s')),
                ], 401);
            }
        });
    })->create();
