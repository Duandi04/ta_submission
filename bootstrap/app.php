<?php

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'role'             => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'       => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Sentry integration
        $exceptions->reportable(function (Throwable $e) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        });
    })
    ->booted(function () {
        // General API rate limiter: 60 requests per minute per authenticated user or IP
        RateLimiter::for('api', function (Request $request) {
            $limit = (int) config('app.api_rate_limit', env('API_RATE_LIMIT', 60));
            return Limit::perMinute($limit)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'error'   => 'Too Many Requests',
                        'message' => 'You have exceeded the API rate limit. Please wait before retrying.',
                        'retry_after' => $headers['Retry-After'] ?? 60,
                    ], 429, $headers);
                });
        });

        // File upload rate limiter: 10 uploads per minute per authenticated user or IP
        RateLimiter::for('api.upload', function (Request $request) {
            $limit = (int) config('app.api_upload_rate_limit', env('API_UPLOAD_RATE_LIMIT', 10));
            return Limit::perMinute($limit)
                ->by('upload:' . ($request->user()?->id ?: $request->ip()))
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'error'   => 'Upload Rate Limit Exceeded',
                        'message' => 'You have exceeded the file upload rate limit (10/minute). Please wait before retrying.',
                        'retry_after' => $headers['Retry-After'] ?? 60,
                    ], 429, $headers);
                });
        });
    })
    ->create();
