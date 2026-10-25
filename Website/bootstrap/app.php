<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'check.schedule' => \App\Http\Middleware\CheckScheduleTime::class,
        'auth.student'   => \App\Http\Middleware\AuthStudent::class,
    ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Route::middleware('throttle:...') pada routes/api.php melempar
        // ThrottleRequestsException dengan response bawaan Laravel:
        // {"message": "Too Many Attempts."} — tanpa field 'success' dan
        // dalam bahasa Inggris. Ini tidak konsisten dengan kontrak API
        // (semua response lain selalu {success, message, ...}), sehingga
        // aplikasi mobile perlu ditangani khusus. Kita seragamkan di sini.
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $retryAfter = $e->getHeaders()['Retry-After'] ?? null;

                return response()->json([
                    'success' => false,
                    'message' => $retryAfter
                        ? "Terlalu banyak permintaan. Coba lagi dalam {$retryAfter} detik."
                        : 'Terlalu banyak permintaan. Coba lagi sebentar lagi.',
                ], 429, $e->getHeaders());
            }
        });
    })->create();
