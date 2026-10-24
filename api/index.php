<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

if (getenv('VERCEL')) {
    $storagePath = sys_get_temp_dir().'/riocell-storage';
    $directories = [
        $storagePath.'/app/private',
        $storagePath.'/app/public',
        $storagePath.'/fonts',
        $storagePath.'/framework/cache/data',
        $storagePath.'/framework/sessions',
        $storagePath.'/framework/testing',
        $storagePath.'/framework/views',
        $storagePath.'/logs',
    ];

    foreach ($directories as $directory) {
        if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new RuntimeException("Unable to create Laravel runtime directory: {$directory}");
        }
    }

    $app->useStoragePath($storagePath);
}

$app->handleRequest(Request::capture());
