<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (getenv('VERCEL')) {
    $runtimePath = sys_get_temp_dir().'/riocell';
    $cachePath = $runtimePath.'/bootstrap/cache';
    $storagePath = $runtimePath.'/storage';

    $runtimeDirectories = [
        $cachePath,
        $storagePath.'/app/private',
        $storagePath.'/app/public',
        $storagePath.'/fonts',
        $storagePath.'/framework/cache/data',
        $storagePath.'/framework/sessions',
        $storagePath.'/framework/testing',
        $storagePath.'/framework/views',
        $storagePath.'/logs',
    ];

    foreach ($runtimeDirectories as $directory) {
        if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new RuntimeException("Unable to create Laravel runtime directory: {$directory}");
        }
    }

    $runtimeEnvironment = [
        'APP_CONFIG_CACHE' => $cachePath.'/config.php',
        'APP_EVENTS_CACHE' => $cachePath.'/events.php',
        'APP_PACKAGES_CACHE' => $cachePath.'/packages.php',
        'APP_ROUTES_CACHE' => $cachePath.'/routes-v7.php',
        'APP_SERVICES_CACHE' => $cachePath.'/services.php',
        'VIEW_COMPILED_PATH' => $storagePath.'/framework/views',
    ];

    foreach ($runtimeEnvironment as $key => $value) {
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }

    $_SERVER['LARAVEL_STORAGE_PATH'] = $storagePath;
}

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

if (getenv('VERCEL')) {
    $app->useStoragePath($storagePath);
}

$app->handleRequest(Request::capture());
