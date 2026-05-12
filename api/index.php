<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$tmp = sys_get_temp_dir().'/form-cbt';

foreach ([
    $tmp.'/storage/framework/cache',
    $tmp.'/storage/framework/sessions',
    $tmp.'/storage/framework/views',
    $tmp.'/bootstrap/cache',
] as $path) {
    if (! is_dir($path)) {
        mkdir($path, 0777, true);
    }
}

$_ENV['APP_STORAGE_PATH'] = $_ENV['APP_STORAGE_PATH'] ?? $tmp.'/storage';
$_SERVER['APP_STORAGE_PATH'] = $_SERVER['APP_STORAGE_PATH'] ?? $tmp.'/storage';
$_ENV['VIEW_COMPILED_PATH'] = $_ENV['VIEW_COMPILED_PATH'] ?? $tmp.'/storage/framework/views';
$_SERVER['VIEW_COMPILED_PATH'] = $_SERVER['VIEW_COMPILED_PATH'] ?? $tmp.'/storage/framework/views';

foreach ([
    'APP_CONFIG_CACHE' => $tmp.'/bootstrap/cache/config.php',
    'APP_EVENTS_CACHE' => $tmp.'/bootstrap/cache/events.php',
    'APP_PACKAGES_CACHE' => $tmp.'/bootstrap/cache/packages.php',
    'APP_ROUTES_CACHE' => $tmp.'/bootstrap/cache/routes-v7.php',
    'APP_SERVICES_CACHE' => $tmp.'/bootstrap/cache/services.php',
] as $key => $path) {
    $_ENV[$key] = $_ENV[$key] ?? $path;
    $_SERVER[$key] = $_SERVER[$key] ?? $path;
}

require __DIR__.'/../vendor/autoload.php';

/** @var Illuminate\Foundation\Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
