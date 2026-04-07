<?php

/**
 * ENTRY POINT untuk Shared Hosting (InfinityFree, 000webhost, dll)
 * 
 * Letakkan file ini di root folder htdocs/ 
 * (sejajar dengan folder app/, vendor/, storage/, dll)
 * 
 * Jangan hapus file public/index.php yang asli — biarkan saja.
 */

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Maintenance mode
if (file_exists($maintenance = __DIR__ . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';

// Override public path → sekarang htdocs/ itu sendiri yang jadi public
$app->bind('path.public', fn() => __DIR__);

// Handle request
$app->handleRequest(Request::capture());
