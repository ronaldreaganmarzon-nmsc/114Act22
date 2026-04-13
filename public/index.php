<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Fix API requests with missing/malformed JSON
if (str_contains($_SERVER['REQUEST_URI'] ?? '', '/api')) {
    // Ensure Content-Type is set for API requests
    if (empty($_SERVER['CONTENT_TYPE'])) {
        $_SERVER['CONTENT_TYPE'] = 'application/json';
    }
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
