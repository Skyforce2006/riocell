<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

// Wajib membuat struktur folder di /tmp untuk serverless Vercel
$storagePath = '/tmp/storage';
if (!file_exists($storagePath)) {
    mkdir($storagePath, 0777, true);
}
foreach (['framework/views', 'framework/cache', 'framework/sessions', 'logs'] as $folder) {
    if (!file_exists("$storagePath/$folder")) {
        mkdir("$storagePath/$folder", 0777, true);
    }
}
$app->useStoragePath($storagePath);

return $app;