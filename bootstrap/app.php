<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        // Registrar alias para Spatie Permissions
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Manejo personalizado para API
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            if ($request->is('api/*')) {
                return true;
            }
            return $request->expectsJson();
        });

        // Personalizar respuesta 404
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Recurso no encontrado.',
                    'error' => 'Not Found'
                ], 404);
            }
        });

        // Personalizar respuesta 401 (No autenticado)
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'No autenticado.',
                    'error' => 'Unauthorized'
                ], 401);
            }
        });

        // Personalizar error general 500 (para ocultar stack trace en producción o formatearlo)
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') && !$e instanceof ValidationException) {
                // En local queremos ver el error real, pero en formato JSON
                // En producción, mensaje genérico
                $debug = config('app.debug');

                return response()->json([
                    'message' => $debug ? $e->getMessage() : 'Error interno del servidor.',
                    'error' => 'Internal Server Error',
                    'file' => $debug ? $e->getFile() : null,
                    'line' => $debug ? $e->getLine() : null,
                    // 'trace' => $debug ? $e->getTrace() : null, // Opcional, puede ser muy largo
                ], 500);
            }
        });
    })->create();
